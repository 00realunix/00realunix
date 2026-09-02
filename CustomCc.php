<?php

class Eshop_CustomPayment_Block_Form_CustomCc extends Mage_Payment_Block_Form
{
    protected function _construct()
    {
        parent::_construct();

        $this->setTemplate('eshop/custompayment/form/cc.phtml');
    }
    public function getMethodCode()
    {
        return 'custompayment_method';
    }

    protected function _toHtml()
    {

    }
}
