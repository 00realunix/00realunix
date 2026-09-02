<?php
// 1. Load inti core Magento 1 secara paksa
require_once 'app/Mage.php';

// 2. Inisialisasi aplikasi Magento (menggunakan mode admin agar semua data terbuka)
Mage::app('admin');

echo "<pre style='background:#1e1e1e; color:#7bf1a8; padding:20px; font-family:monospace; line-height:1.5; border-radius:8px;'>";
echo "==================================================\n";
echo "        MAGENTO 1 EXTERNAL DEBUGGING TOOL         \n";
echo "        VERSION: ESHOP_CUSTOMPAYMENT              \n";
echo "==================================================\n\n";

// --- TES 1: CEK REGISTRASI MODUL ---
echo "[TES 1] Memeriksa registrasi modul di app/etc/modules/ ...\n";
$modules = (array)Mage::getConfig()->getNode('modules')->children();

if (isset($modules['Eshop_CustomPayment'])) {
    echo "  -> STATUS: <span style='color:#5ce1e6;'>TERDAFTAR</span>\n";
    echo "  -> Active: " . $modules['Eshop_CustomPayment']->active . "\n";
    echo "  -> Code Pool: " . $modules['Eshop_CustomPayment']->codePool . "\n";
} else {
    echo "  -> STATUS: <span style='color:#ff5e5e;'>TIDAK TERDAFTAR</span> (File XML di app/etc/modules/Eshop_CustomPayment.xml salah nama/struktur!)\n";
}
echo "--------------------------------------------------\n\n";


// --- TES 2: CEK MODEL INTI ---
echo "[TES 2] Memeriksa kecocokan class Model PHP ...\n";
try {
    $modelClass = Mage::getConfig()->getModelClassName('eshop_custompayment/paymentMethod');
    echo "  -> Nama Class yang dicari Magento: " . $modelClass . "\n";
    
    if (class_exists($modelClass)) {
        echo "  -> STATUS: <span style='color:#5ce1e6;'>OK</span> (File Model ditemukan dan Class berhasil dimuat).\n";
        
        // Tes inisialisasi objek model pembayaran kustom Anda
        $methodInstance = Mage::getModel('eshop_custompayment/paymentMethod');
        if ($methodInstance) {
             echo "  -> Objek Model berhasil di-instansiasi.\n";
             echo "  -> Is Available: " . ($methodInstance->isAvailable() ? 'YES' : 'NO') . "\n";
        }
    } else {
        echo "  -> STATUS: <span style='color:#ff5e5e;'>ERROR</span> (File PHP ada, tapi penulisan nama class di dalam file PaymentMethod.php salah!).\n";
    }
} catch (Exception $e) {
    echo "  -> STATUS: <span style='color:#ff5e5e;'>FATAL ERROR</span> (" . $e->getMessage() . ")\n";
}
echo "--------------------------------------------------\n\n";


// --- TES 3: CEK AKTIVASI DI DATABASE / CONFIG ---
echo "[TES 3] Memeriksa apakah status pembayaran AKTIF di admin ...\n";
$isActive = Mage::getStoreConfig('payment/custompayment_method/active');
$title = Mage::getStoreConfig('payment/custompayment_method/title');
$ccTypes = Mage::getStoreConfig('payment/custompayment_method/cctypes');

if ($isActive) {
    echo "  -> STATUS: <span style='color:#5ce1e6;'>AKTIF (Enabled)</span>\n";
    echo "  -> Judul di Checkout: " . $title . "\n";
    echo "  -> Allowed CC Types: " . ($ccTypes ? $ccTypes : "<span style='color:#ff5e5e;'>KOSONG (Wajib dipilih di admin agar muncul di frontend!)</span>") . "\n";
} else {
    echo "  -> STATUS: <span style='color:#ff5e5e;'>NONAKTIF (Disabled)</span>\n";
    echo "  -> Solusi: Masuk ke Admin Panel > System > Configuration > Payment Methods, lalu aktifkan 'Enabled = Yes'.\n";
}
echo "--------------------------------------------------\n\n";

// --- TES 4: CEK REGISTER BLOCK FORM ---
echo "[TES 4] Memeriksa inisialisasi Block Form ...\n";
try {
    $blockClass = Mage::getConfig()->getBlockClassName('eshop_custompayment/form_customCc');
    echo "  -> Nama Class Block yang dicari Magento: " . $blockClass . "\n";
    
    if (class_exists($blockClass)) {
        echo "  -> STATUS: <span style='color:#5ce1e6;'>OK</span> (Class Block Form ditemukan).\n";
        
        $blockInstance = Mage::app()->getLayout()->createBlock('eshop_custompayment/form_customCc');
        if ($blockInstance) {
             echo "  -> Jalur template di dalam file Block: " . $blockInstance->getTemplate() . "\n";
             
             // Cek keberadaan fisik file phtml di server
             $templatePath = Mage::getBaseDir('design') . '/frontend/base/default/template/' . $blockInstance->getTemplate();
             if (file_exists($templatePath)) {
                 echo "  -> STATUS FILE PHTML: <span style='color:#5ce1e6;'>DITEMUKAN</span> (" . $blockInstance->getTemplate() . ")\n";
             } else {
                 echo "  -> STATUS FILE PHTML: <span style='color:#ff5e5e;'>TIDAK DITEMUKAN</span> (File tidak ada di: " . $templatePath . ")\n";
             }
        }
    } else {
        echo "  -> STATUS: <span style='color:#ff5e5e;'>ERROR</span> (Magento tidak bisa memuat Class Block ini! Cek penulisan nama class di CustomCc.php).\n";
    }
} catch (Exception $e) {
    echo "  -> STATUS: <span style='color:#ff5e5e;'>FATAL ERROR</span> (" . $e->getMessage() . ")\n";
}
echo "==================================================\n";

echo "</pre>";
