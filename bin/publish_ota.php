<?php
/**
 * Over-The-Air (OTA) Bundle Publisher for Tsigiro Mobile App
 * Packages android/app/src/main/assets into public/downloads/ota_bundle.zip
 * Updates config/mobile_ota.json with bundle version, sha256 hash, and release notes.
 *
 * Usage:
 *   php bin/publish_ota.php [version] [release_notes]
 * Example:
 *   php bin/publish_ota.php 1.0.1 "Added real-time role approvals"
 */

$rootDir = dirname(__DIR__);
$assetsDir = $rootDir . '/android/app/src/main/assets';
$outputZip = $rootDir . '/public/downloads/ota_bundle.zip';
$configFile = $rootDir . '/config/mobile_ota.json';

if (!is_dir($assetsDir)) {
    fwrite(STDERR, "Error: Assets directory not found at $assetsDir\n");
    exit(1);
}

// Load existing config
$config = [];
if (file_exists($configFile)) {
    $config = json_decode(file_get_contents($configFile), true) ?: [];
}

// Determine new version
$currentVersion = $config['bundle_version'] ?? '1.0.0';
$customVersion = $argv[1] ?? null;
$customNotes = $argv[2] ?? null;

if ($customVersion) {
    $newVersion = trim($customVersion);
} else {
    // Auto-increment patch: 1.0.0 -> 1.0.1
    $parts = explode('.', $currentVersion);
    if (count($parts) === 3) {
        $parts[2] = (int)$parts[2] + 1;
        $newVersion = implode('.', $parts);
    } else {
        $newVersion = $currentVersion . '.1';
    }
}

$releaseNotes = $customNotes ?: ($config['release_notes'] ?? 'General performance improvements and updates.');

echo "====================================================\n";
echo " Tsigiro Mobile OTA Bundle Publisher\n";
echo " Current Version: $currentVersion\n";
echo " Target Version:  $newVersion\n";
echo " Notes:           $releaseNotes\n";
echo "====================================================\n\n";

echo "Packaging assets from: $assetsDir\n";

if (file_exists($outputZip)) {
    unlink($outputZip);
}

$zip = new ZipArchive();
if ($zip->open($outputZip, ZipArchive::CREATE | ZipArchive::OVERWRITE) !== true) {
    fwrite(STDERR, "Error: Could not create zip file at $outputZip\n");
    exit(1);
}

$files = new RecursiveIteratorIterator(
    new RecursiveDirectoryIterator($assetsDir, RecursiveDirectoryIterator::SKIP_DOTS),
    RecursiveIteratorIterator::LEAVES_ONLY
);

$count = 0;
foreach ($files as $file) {
    if (!$file->isDir()) {
        $filePath = $file->getRealPath();
        $relativePath = substr($filePath, strlen($assetsDir) + 1);

        // Exclude system files like .DS_Store
        if (basename($relativePath) === '.DS_Store' || strpos($relativePath, '__MACOSX') !== false) {
            continue;
        }

        $zip->addFile($filePath, $relativePath);
        $count++;
    }
}

$zip->close();

if (!file_exists($outputZip) || filesize($outputZip) === 0) {
    fwrite(STDERR, "Error: Failed to generate zip bundle or zip is empty.\n");
    exit(1);
}

$zipSize = filesize($outputZip);
$sha256 = hash_file('sha256', $outputZip);

echo "Bundle created successfully!\n";
echo " - Files packaged: $count\n";
echo " - Bundle size:    " . round($zipSize / 1024, 2) . " KB\n";
echo " - SHA-256 hash:   $sha256\n\n";

// Update config/mobile_ota.json
$config['bundle_version'] = $newVersion;
$config['bundle_hash'] = $sha256;
$config['bundle_url'] = '/public/downloads/ota_bundle.zip';
$config['bundle_size_bytes'] = $zipSize;
$config['min_apk_version'] = $config['min_apk_version'] ?? 1;
$config['release_notes'] = $releaseNotes;
$config['updated_at'] = date('Y-m-d H:i:s');

file_put_contents($configFile, json_encode($config, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES) . "\n");

echo "Updated $configFile\n";
echo "OTA Release $newVersion is now live and ready for distribution!\n";
