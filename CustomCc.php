<?php

class Eshop_CustomPayment_Block_Form_CustomCc extends Mage_Payment_Block_Form_Abstract
{
    protected function _construct()
    {
        parent::_construct();

        $this->setTemplate('eshop/custompayment/form/cc.phtml');
    }
}
