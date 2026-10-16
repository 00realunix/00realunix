<?php
/**
 * Script untuk mengambil data riwayat pembayaran pesanan, 
 * lengkap dengan Tanggal, Billing Address, dan Detail Metode Pembayaran di Magento 1.
 * 
 * Petunjuk:
 * 1. Letakkan di root direktori Magento 1 Anda.
 * 2. Jalankan lewat browser.
 * 3. HAPUS FILE INI SETELAH DIGUNAKAN!
 */

// 1. Inisialisasi environment Magento 1
require_once 'app/Mage.php';
umask(0);
Mage::app('admin_c6f57ws66t');

try {
    echo "<h2>Data Riwayat Pembayaran & Billing Address Pesanan</h2>";
    echo "<p>Menampilkan 50 transaksi terbaru (Urut dari yang paling baru):</p>";

    // 2. Ambil koleksi pesanan (Order Collection)
    $orders = Mage::getModel('sales/order')->getCollection()
        ->addAttributeToSelect('*')
        ->setOrder('created_at', 'DESC')
        ->setPageSize(50); // Anda bisa mengubah angka ini untuk menambah jumlah baris data

    if ($orders->getSize() == 0) {
        echo "<span style='color:red;'>Tidak ada data pesanan yang ditemukan.</span>";
        exit;
    }

    // Buat tabel output
    echo "<table border='1' cellpadding='8' cellspacing='0' style='border-collapse:collapse; font-family:Arial, sans-serif; width:100%; font-size:13px;'>";
    echo "<tr style='background-color:#333; color:#fff; font-weight:bold;'>
            <th>ID Pesanan</th>
            <th>Tanggal Pembayaran / Order</th>
            <th>Total Bayar</th>
            <th>Metode Pembayaran & Detail</th>
            <th>Full Billing Address</th>
            <th>Status Pesanan</th>
          </tr>";

    foreach ($orders as $order) {
        // 3. Ambil data object Payment
        $payment = $order->getPayment();
        if (!$payment) {
            continue;
        }

        $paymentMethodCode = $payment->getMethod();
        $paymentMethodTitle = Mage::getStoreConfig('payment/'.$paymentMethodCode.'/title', $order->getStoreId());
        if(empty($paymentMethodTitle)) {
            $paymentMethodTitle = $paymentMethodCode; // Fallback ke kode jika title kosong
        }

        // Ambil info detail tambahan jika ada (misal: Last 4 digit CC, info bank, cc_type, dll)
        $additionalInfo = array();
        if ($payment->getCcLast4()) $additionalInfo[] = "CC Last 4: " . $payment->getCcLast4();
        if ($payment->getCcType()) $additionalInfo[] = "CC Type: " . $payment->getCcType();
        if ($payment->getLastTransId()) $additionalInfo[] = "Trans ID: " . $payment->getLastTransId();
        
        // Ambil data serialization data payment tambahan dari module pihak ketiga jika ada
        $additionalData = $payment->getAdditionalInformation();
        if (is_array($additionalData)) {
            foreach ($additionalData as $key => $value) {
                if (is_string($value) || is_numeric($value)) {
                    $additionalInfo[] = "<strong>".htmlspecialchars($key)."</strong>: " . htmlspecialchars($value);
                }
            }
        }

        $detailPaymentHtml = "<strong>" . htmlspecialchars($paymentMethodTitle) . "</strong>";
        if (!empty($additionalInfo)) {
            $detailPaymentHtml .= "<br/><small style='color:#555;'>" . implode("<br/>", $additionalInfo) . "</small>";
        }

        // 4. Ambil Tanggal Pembayaran (Gunakan tanggal invoice jika sudah dibayar, atau tanggal order sebagai basis)
        $paymentDate = $order->getCreatedAt(); // Default waktu order dibuat
        $invoices = $order->getInvoiceCollection();
        if ($invoices->getSize() > 0) {
            foreach ($invoices as $invoice) {
                $paymentDate = $invoice->getCreatedAt(); // Tanggal invoice dibuat melambangkan tanggal dana masuk/pembayaran
                break;
            }
        }
        // Format tanggal agar mudah dibaca manusia
        $formattedDate = date('d F Y  -  H:i:s', strtotime($paymentDate));

        // 5. Ambil Data Lengkap Alamat Penagihan (Full Billing Address)
        $billingAddress = $order->getBillingAddress();
        $billingHtml = "Data Alamat Tidak Ditemukan";
        
        if ($billingAddress) {
            $street = implode(', ', $billingAddress->getStreet());
            $billingHtml = "<strong>" . htmlspecialchars($billingAddress->getFirstname() . " " . $billingAddress->getLastname()) . "</strong><br/>";
            $billingHtml .= htmlspecialchars($street) . "<br/>";
            $billingHtml .= htmlspecialchars($billingAddress->getCity()) . ", " . htmlspecialchars($billingAddress->getRegion()) . " - " . htmlspecialchars($billingAddress->getPostcode()) . "<br/>";
            $billingHtml .= htmlspecialchars(Mage::app()->getLocale()->getCountryTranslation($billingAddress->getCountryId())) . "<br/>";
            $billingHtml .= "Tlp: " . htmlspecialchars($billingAddress->getTelephone());
        }

        // 6. Ambil Total Nominal Belanjaan
        $totalPaid = $order->getBaseGrandTotal();
        $currencyCode = $order->getOrderCurrencyCode();
        $formattedTotal = Mage::helper('core')->currency($totalPaid, true, false);

        // Tampilkan baris tabel per transaksi
        echo "<tr>";
        echo "<td align='center'><strong>#{$order->getIncrementId()}</strong></td>";
        echo "<td>{$formattedDate}</td>";
        echo "<td align='right' style='color:#2c3e50; font-weight:bold;'>{$formattedTotal}</td>";
        echo "<td>{$detailPaymentHtml}</td>";
        echo "<td>{$billingHtml}</td>";
        echo "<td align='center'><span style='padding:3px 6px; background:#f4f4f4; border:1px solid #ccc; border-radius:3px;'>{$order->getStatus()}</span></td>";
        echo "</tr>";
    }

    echo "</table>";
    echo "<br/><br/><span style='color:red; font-weight:bold;'>PENTING: Demi privasi data konsumen, SEGERA HAPUS file script ini (`get_payment_details.php`) dari hosting Anda sekarang!</span>";

} catch (Exception $e) {
    echo "<br/><span style='color:red; font-weight:bold;'>Gagal mengambil data riwayat pembayaran: " . $e->getMessage() . "</span>";
}
