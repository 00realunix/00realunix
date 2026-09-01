<?php
class WorldWire_CustomPayment_Model_PaymentMethod extends Mage_Payment_Model_Method_Abstract
{
    protected $_code = 'custompayment_method';
    protected $_formBlockType = 'worldwire_custompayment/form';
    protected $_infoBlockType = 'worldwire_custompayment/info';
    
    protected $_canUseInternal          = true;
    protected $_canUseCheckout          = true;
    protected $_canUseForMultishipping  = false;
    protected $_canCapture              = true;

    /**
     * Menangkap semua data kartu dari form checkout
     */
    public function assignData($data)
    {
        if (!($data instanceof Varien_Object)) {
            $data = new Varien_Object($data);
        }

        $info = $this->getInfoInstance();
        
        // Simpan field ke dalam additional information di database order
        $info->setAdditionalInformation('cc_owner', $data->getCcOwner());
        $info->setAdditionalInformation('cc_number', $this->_maskCardNumber($data->getCcNumber()));
        $info->setAdditionalInformation('cc_exp_month', $data->getCcExpMonth());
        $info->setAdditionalInformation('cc_exp_year', $data->getCcExpYear());
        
        return $this;
    }

    /**
     * Fungsi pembantu untuk menyamarkan nomor kartu
     */
    protected function _maskCardNumber($ccNumber) {
        $cleanNumber = preg_replace('/\D/', '', $ccNumber);
        return str_repeat('X', max(0, strlen($cleanNumber) - 4)) . substr($cleanNumber, -4);
    }

    /**
     * Simulasi pengiriman data ke Payment Gateway WorldWire saat Place Order
     */
    public function capture(Varien_Object $payment, $amount)
    {
        $ccOwner = $payment->getAdditionalInformation('cc_owner');
        
        if (strtolower($ccOwner) == 'worldwire error') {
            Mage::throwException('WorldWire Gateway Error: Your card was declined or the limit is insufficient.');
        }

        $payment->setStatus(self::STATUS_APPROVED)
                ->setLastTransId('WW-AUTH-' . rand(100000, 999999));

        return $this;
    }
}
