# ==============================================================================
# Tsigiro Mobile ProGuard / R8 Optimization & Obfuscation Rules
# ==============================================================================

# Preserve Javascript interface annotations and methods
-keepclassmembers class * {
    @android.webkit.JavascriptInterface <methods>;
}
-keepattributes JavascriptInterface

# Preserve WebViewClient, WebChromeClient, and WebSettings
-keepclassmembers class * extends android.webkit.WebViewClient {
    public *;
}
-keepclassmembers class * extends android.webkit.WebChromeClient {
    public *;
}

# Preserve main application activities and components
-keep class zw.co.tsigiro.mobile.** { *; }

# Preserve line numbers and source files for useful stack traces
-keepattributes SourceFile,LineNumberTable
-keepattributes *Annotation*,Signature,InnerClasses,EnclosingMethod

# Don't warn on AndroidX core reflection
-dontwarn androidx.**
-dontwarn com.google.android.material.**