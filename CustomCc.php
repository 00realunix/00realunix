<?php
class Eshop_CustomPayment_Block_Form_CustomCc extends Mage_Payment_Block_Form_Cc
{
    protected function _construct()
    {
        parent::_construct();
        // Mengarahkan ke file template HTML kustom yang akan kita buat di bawah
        $this->setTemplate('eshop/custompayment/form/cc.phtml');
    }
}
