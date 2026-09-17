package zw.co.tsigiro.mobile

import android.app.Activity
import android.content.Context
import android.content.SharedPreferences
import android.util.Log
import android.webkit.WebView
import android.widget.Toast
import org.json.JSONObject
import java.io.BufferedInputStream
import java.io.File
import java.io.FileOutputStream
import java.io.InputStream
import java.net.HttpURLConnection
import java.net.URL
import java.security.MessageDigest
import java.util.zip.ZipEntry
import java.util.zip.ZipInputStream

class OtaManager(private val context: Context) {

    companion object {
        private const val TAG = "TsigiroOTA"
        private const val PREFS_NAME = "tsigiro_ota_prefs"
        private const val KEY_BUNDLE_VERSION = "ota_bundle_version"
        private const val KEY_LAST_CHECK = "ota_last_check"
        private const val DEFAULT_VERSION = "1.0.0"
        private const val DEFAULT_API_BASE = "https://portal.tsigiro.co.zw"
    }

    private val prefs: SharedPreferences =
        context.getSharedPreferences(PREFS_NAME, Context.MODE_PRIVATE)

    private val otaRootDir: File = File(context.filesDir, "ota")
    private val currentBundleDir: File = File(otaRootDir, "current")
    private val stagingBundleDir: File = File(otaRootDir, "staging")

    init {
        if (!otaRootDir.exists()) {
            otaRootDir.mkdirs()
        }
    }

    /**
     * Returns the active index.html URL to load in WebView.
     * Uses downloaded OTA bundle if available and valid; otherwise falls back to bundled APK assets.
     */
    fun getActiveIndexUrl(): String {
        val otaIndex = File(currentBundleDir, "index.html")
        return if (otaIndex.exists() && otaIndex.length() > 0) {
            Log.i(TAG, "Loading OTA bundle from: ${otaIndex.absolutePath}")
            "file://${otaIndex.absolutePath}"
        } else {
            Log.i(TAG, "Loading bundled assets from file:///android_asset/index.html")
            "file:///android_asset/index.html"
        }
    }

    /**
     * Returns the currently active bundle version string.
     */
    fun getActiveBundleVersion(): String {
        val otaIndex = File(currentBundleDir, "index.html")
        return if (otaIndex.exists()) {
            prefs.getString(KEY_BUNDLE_VERSION, DEFAULT_VERSION) ?: DEFAULT_VERSION
        } else {
            DEFAULT_VERSION
        }
    }

    /**
     * Checks for Over-The-Air updates and downloads them in the background.
     */
    fun checkForUpdates(
        activity: Activity,
        webView: WebView,
        apiBase: String = DEFAULT_API_BASE,
        isManual: Boolean = false
    ) {
        Thread {
            try {
                val currentVer = getActiveBundleVersion()
                val candidateBases = listOf(
                    apiBase.trimEnd('/'),
                    "http://10.0.2.2/trainit"
                ).distinct()

                var json: JSONObject? = null
                var successfulBase = ""

                for (base in candidateBases) {
                    try {
                        val checkUrl = "$base/api/mobile/ota/check?bundle_version=$currentVer&apk_version=1"
                        Log.d(TAG, "Checking for OTA updates at: $checkUrl")
                        val conn = URL(checkUrl).openConnection() as HttpURLConnection
                        conn.connectTimeout = 4000
                        conn.readTimeout = 4000
                        conn.requestMethod = "GET"
                        conn.setRequestProperty("Accept", "application/json")

                        if (conn.responseCode == 200) {
                            val resp = conn.inputStream.bufferedReader().use { it.readText() }
                            conn.disconnect()
                            if (resp.trim().startsWith("{")) {
                                json = JSONObject(resp)
                                successfulBase = base
                                Log.i(TAG, "OTA server responded from: $base")
                                break
                            }
                        } else {
                            conn.disconnect()
                        }
                    } catch (e: Exception) {
                        Log.d(TAG, "Failed connecting to $base: ${e.message}")
                    }
                }

                if (json == null) {
                    Log.w(TAG, "No OTA server returned a valid update response.")
                    if (isManual) {
                        activity.runOnUiThread {
                            Toast.makeText(context, "Could not reach update server", Toast.LENGTH_SHORT).show()
                        }
                    }
                    return@Thread
                }

                val updateAvailable = json.optBoolean("update_available", false)
                val latestVer = json.optString("latest_bundle_version", currentVer)
                var bundleUrl = json.optString("bundle_url", "")
                if (bundleUrl.contains("localhost")) {
                    bundleUrl = bundleUrl.replace("localhost/tsigiro/portal", "10.0.2.2/trainit")
                        .replace("localhost", "10.0.2.2")
                }
                val expectedHash = json.optString("bundle_hash", "")
                val releaseNotes = json.optString("release_notes", "Performance and feature updates.")

                if (!updateAvailable || bundleUrl.isEmpty()) {
                    Log.i(TAG, "App is up to date (version $currentVer)")
                    if (isManual) {
                        activity.runOnUiThread {
                            Toast.makeText(context, "App is up to date (v$currentVer)", Toast.LENGTH_SHORT).show()
                        }
                    }
                    return@Thread
                }

                Log.i(TAG, "New OTA update found: v$latestVer. Downloading from $bundleUrl")
                if (isManual) {
                    activity.runOnUiThread {
                        Toast.makeText(context, "Downloading update v$latestVer...", Toast.LENGTH_SHORT).show()
                    }
                }

                // Download zip bundle to cache
                val cacheZip = File(context.cacheDir, "ota_bundle_temp.zip")
                if (cacheZip.exists()) cacheZip.delete()

                val dlConn = URL(bundleUrl).openConnection() as HttpURLConnection
                dlConn.connectTimeout = 15000
                dlConn.readTimeout = 30000
                dlConn.connect()

                if (dlConn.responseCode != 200) {
                    Log.e(TAG, "Failed to download zip: HTTP ${dlConn.responseCode}")
                    return@Thread
                }

                dlConn.inputStream.use { input ->
                    FileOutputStream(cacheZip).use { output ->
                        input.copyTo(output)
                    }
                }
                dlConn.disconnect()

                // Verify SHA-256 hash if provided
                if (expectedHash.isNotEmpty()) {
                    val actualHash = calculateSha256(cacheZip)
                    if (!actualHash.equals(expectedHash, ignoreCase = true)) {
                        Log.e(TAG, "Hash mismatch! Expected: $expectedHash, Actual: $actualHash")
                        cacheZip.delete()
                        if (isManual) {
                            activity.runOnUiThread {
                                Toast.makeText(context, "Update verification failed. Retrying later.", Toast.LENGTH_SHORT).show()
                            }
                        }
                        return@Thread
                    }
                    Log.i(TAG, "Bundle SHA-256 verified successfully ($actualHash)")
                }

                // Extract to staging directory
                if (stagingBundleDir.exists()) {
                    stagingBundleDir.deleteRecursively()
                }
                stagingBundleDir.mkdirs()

                val unzippedSuccessfully = unzipBundle(cacheZip, stagingBundleDir)
                cacheZip.delete()

                if (!unzippedSuccessfully) {
                    Log.e(TAG, "Failed to unzip OTA bundle.")
                    stagingBundleDir.deleteRecursively()
                    return@Thread
                }

                // Verify staging index.html exists
                val newIndex = File(stagingBundleDir, "index.html")
                if (!newIndex.exists() || newIndex.length() == 0L) {
                    Log.e(TAG, "Extracted bundle is missing index.html")
                    stagingBundleDir.deleteRecursively()
                    return@Thread
                }

                // Atomic swap: replace current with staging
                synchronized(this) {
                    if (currentBundleDir.exists()) {
                        currentBundleDir.deleteRecursively()
                    }
                    stagingBundleDir.renameTo(currentBundleDir)
                }

                // Save new version
                prefs.edit()
                    .putString(KEY_BUNDLE_VERSION, latestVer)
                    .putLong(KEY_LAST_CHECK, System.currentTimeMillis())
                    .apply()

                Log.i(TAG, "OTA update v$latestVer installed successfully!")

                // Notify frontend
                activity.runOnUiThread {
                    val safeVer = latestVer.replace("'", "\\'")
                    val safeNotes = releaseNotes.replace("'", "\\'").replace("\n", " ")
                    val js = """
                        (function() {
                            if (typeof window.onOtaUpdateReady === 'function') {
                                window.onOtaUpdateReady('$safeVer', '$safeNotes');
                            } else if (typeof window.showToast === 'function') {
                                window.showToast('Update v$safeVer installed. Pull down or restart to apply.', 'success');
                            }
                        })();
                    """.trimIndent()
                    webView.evaluateJavascript(js, null)
                }

            } catch (e: Exception) {
                Log.e(TAG, "OTA update process error: ${e.message}", e)
                if (isManual) {
                    activity.runOnUiThread {
                        Toast.makeText(context, "Update check error: ${e.message}", Toast.LENGTH_SHORT).show()
                    }
                }
            }
        }.start()
    }

    /**
     * Unzips a bundle file into the destination directory with Zip Slip protection.
     */
    private fun unzipBundle(zipFile: File, destDir: File): Boolean {
        return try {
            ZipInputStream(BufferedInputStream(zipFile.inputStream())).use { zis ->
                var entry: ZipEntry? = zis.nextEntry
                while (entry != null) {
                    val newFile = File(destDir, entry.name)

                    // Zip Slip vulnerability protection
                    val destCanonical = destDir.canonicalPath
                    val fileCanonical = newFile.canonicalPath
                    if (!fileCanonical.startsWith(destCanonical)) {
                        Log.e(TAG, "Zip Slip detected in entry: ${entry.name}")
                        return false
                    }

                    if (entry.isDirectory) {
                        newFile.mkdirs()
                    } else {
                        newFile.parentFile?.mkdirs()
                        FileOutputStream(newFile).use { fos ->
                            zis.copyTo(fos)
                        }
                    }
                    zis.closeEntry()
                    entry = zis.nextEntry
                }
            }
            true
        } catch (e: Exception) {
            Log.e(TAG, "Error unzipping bundle: ${e.message}", e)
            false
        }
    }

    /**
     * Calculates SHA-256 hash of a file.
     */
    private fun calculateSha256(file: File): String {
        val digest = MessageDigest.getInstance("SHA-256")
        file.inputStream().use { isStream ->
            val buffer = ByteArray(8192)
            var bytesRead: Int
            while (isStream.read(buffer).also { bytesRead = it } != -1) {
                digest.update(buffer, 0, bytesRead)
            }
        }
        val bytes = digest.digest()
        return bytes.joinToString("") { "%02x".format(it) }
    }

    /**
     * Rolls back to bundled assets if the custom bundle experiences critical errors.
     */
    fun rollback(activity: Activity, webView: WebView) {
        synchronized(this) {
            if (currentBundleDir.exists()) {
                currentBundleDir.deleteRecursively()
            }
            prefs.edit().remove(KEY_BUNDLE_VERSION).apply()
        }
        activity.runOnUiThread {
            Log.w(TAG, "Rolling back to bundled APK assets")
            webView.loadUrl("file:///android_asset/index.html")
        }
    }
}
