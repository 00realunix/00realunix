<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once 'app/Mage.php';
Mage::app('admin'); // Inisialisasi admin

echo "<pre style='background:#000; color:#0f0; padding:20px; font-family:monospace;'>";
echo "=== HASIL DIAGNOSA MODUL ESHOP ===\n\n";

// 1. Cek File Aktivasi
$modules = (array)Mage::getConfig()->getNode('modules')->children();
if (isset($modules['Eshop_CustomPayment'])) {
    echo "[OK] Modul Eshop_CustomPayment TERDAFTAR di sistem.\n";
    echo "     Status Aktif: " . $modules['Eshop_CustomPayment']->active . "\n";
} else {
    echo "[ERROR] Modul Eshop_CustomPayment TIDAK DIKENALI oleh Magento.\n";
    echo "        Periksa apakah file app/etc/modules/Eshop_CustomPayment.xml sudah ada dan benar.\n";
}

// 2. Cek Model
$modelClass = Mage::getConfig()->getModelClassName('eshop_custompayment/paymentMethod');
echo "\n[INFO] Mencari Class Model: " . $modelClass . "\n";
if (class_exists($modelClass)) {
    echo "[OK] Class Model ditemukan dan berhasil dimuat.\n";
    $model = Mage::getModel('eshop_custompayment/paymentMethod');
    echo "     Code Metode: " . $model->getCode() . "\n";
} else {
    echo "[ERROR] Class Model TIDAK DITEMUKAN.\n";
}

// 3. Cek Blok Tampilan
$blockClass = Mage::getConfig()->getBlockClassName('eshop_custompayment/form_customCc');
echo "\n[INFO] Mencari Class Blok: " . $blockClass . "\n";
if (class_exists($blockClass)) {
    echo "[OK] Class Blok Form ditemukan.\n";
} else {
    echo "[ERROR] Class Blok Form TIDAK DITEMUKAN.\n";
}

echo "\n==================================</pre>";
