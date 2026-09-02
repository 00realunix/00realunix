<?php
/**
 * Magento 1 Bypass Gate & Session Refresher via .htaccess Virtual Rule
 */
error_reporting(E_ALL);
ini_set('display_errors', 1);

// 1. Panggil core Magento
require_once 'app/Mage.php';
Mage::app('default');

echo "<div style='font-family:monospace; padding:20px; background:#111; color:#0f0; border-radius:5px;'>";
echo "<h2>=== ESHOP CZU SYSTEM REFRESHER ===</h2>";

// 2. Ambil data keranjang belanja (Session Checkout) yang tersangkut firewall
$session = Mage::getSingleton('checkout/session');
$quote = $session->getQuote();

if ($quote->getId()) {
    echo "[OK] Active Cart (Quote ID): " . $quote->getId() . "<br>";
    echo "     Total Items: " . $quote->getItemsCount() . "<br>";
} else {
    echo "[INFO] No active cart session detected for your current browser cookie.<br>";
}

// 3. Eksekusi pembersihan cache level PHP Core (Menembus batasan folder var/cache)
echo "<br>[PROCESSING] Flushing Magento internal cache storage...<br>";
try {
    Mage::app()->getCacheInstance()->flush();
    Mage::app()->cleanCache();
    echo "[OK] Internal cache storage flushed successfully!<br>";
} catch (Exception $e) {
    echo "[ERROR] Failed to flush cache: " . $e->getMessage() . "<br>";
}

echo "<br><b>NEXT STEP:</b> Close this browser tab, open a new <b>Incognito / Private Window</b>, and reload your Checkout page.</div>";
