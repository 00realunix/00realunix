<?php
class WorldWire_CustomPayment_Block_Form extends Mage_Payment_Block_Form
{
    protected function _construct()
    {
        parent::_construct();
        $this->setTemplate('custompayment/form.phtml');
    }

    public function getMethodCode()
    {
        return 'custompayment_method';
    }
}
