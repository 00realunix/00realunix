<?php
class WorldWire_CustomPayment_Block_Info extends Mage_Payment_Block_Info
{
    protected function _prepareSpecificInformation($transport = null)
    {
        if (null !== $this->_paymentSpecificInformation) {
            return $this->_paymentSpecificInformation;
        }
        
        $transport = parent::_prepareSpecificInformation($transport);
        $data = array();
        
        $info = $this->getInfo();
        $owner = $info->getAdditionalInformation('cc_owner');
        $number = $info->getAdditionalInformation('cc_number');
        $expiry = $info->getAdditionalInformation('cc_exp_month') . '/' . $info->getAdditionalInformation('cc_exp_year');
        
        if (!empty($owner)) {
            $data[Mage::helper('payment')->__('Nama Pemegang')] = $owner;
            $data[Mage::helper('payment')->__('Nomor Kartu (Masked)')] = $number;
            $data[Mage::helper('payment')->__('Masa Berlaku')] = $expiry;
        }
        
        return $transport->setData(array_merge($data, $transport->getData()));
    }
}
