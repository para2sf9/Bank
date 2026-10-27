<?php
// Save this as check-php.php and run in browser
echo "<h2>PHP MongoDB Setup Checker</h2>";
echo "<pre>";
echo "PHP Version: " . phpversion() . "
";
echo "PHP Architecture: " . (PHP_INT_SIZE * 8) . "-bit
";
echo "Thread Safe: " . (ZEND_THREAD_SAFE ? "Yes" : "No") . "
";
echo "Extension Dir: " . ini_get('extension_dir') . "

";

echo "MongoDB Extension: " . (extension_loaded('mongodb') ? "INSTALLED (version: " . phpversion('mongodb') . ")" : "NOT INSTALLED") . "
";
echo "OpenSSL: " . (extension_loaded('openssl') ? "Yes" : "No") . "
";
echo "JSON: " . (extension_loaded('json') ? "Yes" : "No") . "
";

echo "
--- php.ini location ---
";
echo php_ini_loaded_file() ?: "Not found";
echo "
</pre>";
?>
