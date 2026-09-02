<?php
class Eshop_CustomPayment_Model_PaymentMethod extends Mage_Payment_Model_Method_Abstract
{
    protected $_code = 'custompayment_method';
    
    // Disambungkan ke Block Custom agar form HTML muncul resmi otomatis
    protected $_formBlockType = 'eshop_custompayment/form_customCc';
    protected $_infoBlockType = 'payment/info_cc';

    protected $_isGateway               = true;
    protected $_canAuthorize            = true;
    protected $_canCapture              = true;
    protected $_canUseCheckout          = true;

    // Menandakan bahwa metode ini membutuhkan pengisian kartu kredit
    protected $_canSaveCc               = false; 

    /**
     * Memvalidasi form sebelum checkout diproses
     */
    public function validate()
    {
        parent::validate();
        
        $info = $this->getInfoInstance();
        $errorMsg = false;

        if (!$info->getCcOwner()) {
            $errorMsg = Mage::helper('payment')->__('Nama pemilik kartu harus diisi.');
        } elseif (!$info->getCcNumber()) {
            $errorMsg = Mage::helper('payment')->__('Nomor kartu kredit harus diisi.');
        } elseif (!$info->getCcExpMonth() || !$info->getCcExpYear()) {
            $errorMsg = Mage::helper('payment')->__('Masa berlaku kartu kredit harus diisi.');
        }

        if ($errorMsg) {
            Mage::throwException($errorMsg);
        }

        return $this;
    }
}
