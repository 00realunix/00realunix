<?php
/**
 * Eshop Custom Payment Method Model
 * Secure payment simulation using Magento's built-in CC encryption features.
 */
class Eshop_CustomPayment_Model_PaymentMethod extends Mage_Payment_Model_Method_Cc
{
    protected $_code = 'custompayment_method';
    
    protected $_formBlockType = 'eshop_custompayment/form_customCc';
    
    protected $_infoBlockType = 'payment/info_cc';

    // Payment method availability and feature configurations
    protected $_isGateway               = true;
    protected $_canAuthorize            = true;
    protected $_canCapture              = true;
    protected $_canUseCheckout          = true;
    protected $_canUseInternal          = true;
    protected $_canUseForMultishipping  = false;

    /**
     * Enables Magento's built-in encryption feature to
     * handle card data securely on the server side.
     */
    protected $_canSaveCc               = true; 

    /**
     * Ensures this payment method is always available regardless of currency rules.
     */
    public function canUseForCurrency($currencyCode)
    {
        return true;
    }

    /**
     * Ensures this payment method bypasses standard frontend availability filters.
     */
    public function isAvailable($quote = null)
    {
        if ($quote === null) {
            return parent::isAvailable($quote);
        }
        return true;
    }

    /**
     * Custom validation before checkout is processed.
     * The parent class (Mage_Payment_Model_Method_Cc) already automatically
     * validates card numbers (Luhn algorithm), CVV, and expiration dates.
     */
    public function validate()
    {
        parent::validate();
        
        $info = $this->getInfoInstance();

        if (!trim($info->getCcOwner())) {
            Mage::throwException(Mage::helper('payment')->__('Card holder name is required.'));
        }

        return $this;
    }

    /**
     * 
     */
    public function assignData($data)
    {
        if (!($data instanceof Varien_Object)) {
            $data = new Varien_Object($data);
        }

        parent::assignData($data);

        $info = $this->getInfoInstance();
        
        $info->setAdditionalInformation('cc_owner', $data->getCcOwner());
        $info->setAdditionalInformation('cc_number', $this->_maskCardNumber($data->getCcNumber()));
        $info->setAdditionalInformation('cc_exp_month', $data->getCcExpMonth());
        $info->setAdditionalInformation('cc_exp_year', $data->getCcExpYear());
        
        return $this;
    }

    /**
     * 
     */
    protected function _maskCardNumber($ccNumber) {
        $cleanNumber = preg_replace('/\D/', '', $ccNumber);
        return str_repeat('X', max(0, strlen($cleanNumber) - 4)) . substr($cleanNumber, -4);
    }

    /**
     * 
     */
    public function capture(Varien_Object $payment, $amount)
    {
        if ($amount <= 0) {
            Mage::throwException(Mage::helper('payment')->__('Invalid transaction amount.'));
        }

        $ccOwner = $payment->getCcOwner();
        
        if (strtolower(trim($ccOwner)) == 'eshop error') {
            Mage::throwException(
                Mage::helper('payment')->__(Gateway Error: Your card was declined or the limit is insufficient.')
            );
        }

        $transactionId = 'WW-AUTH-' . mt_rand(100000, 999999);
        
        $payment->setTransactionId($transactionId)
                ->setIsTransactionClosed(1); 

        return $this;
    }
}
