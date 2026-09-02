<?php

class Eshop_CustomPayment_Model_PaymentMethod extends Mage_Payment_Model_Method_Cc
{

    protected $_code = 'custompayment_method';
    

    protected $_formBlockType = 'eshop_custompayment/form_customCc';
    protected $_infoBlockType = 'payment/info_cc';


    protected $_isGateway               = true;
    protected $_canAuthorize            = true;
    protected $_canCapture              = true;
    protected $_canUseCheckout          = true;


    protected $_canSaveCc               = true; 


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
    public function capture(Varien_Object $payment, $amount)
    {
        if ($amount <= 0) {
            Mage::throwException(Mage::helper('payment')->__('Invalid transaction amount.'));
        }

        
        $ccOwner = $payment->getCcOwner();
        
        
        if (strtolower(trim($ccOwner)) == 'eshop error') {
            Mage::throwException(
                Mage::helper('payment')->__('Gateway Error: Your card was declined or the limit is insufficient.')
            );
        }

        
        $transactionId = 'WW-AUTH-' . mt_rand(100000, 999999);
        
        $payment->setTransactionId($transactionId)
                ->setIsTransactionClosed(1); 

        return $this;
    }
}
