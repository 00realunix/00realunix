<?php
/**
 * Magento
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@magento.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade Magento to newer
 * versions in the future. If you wish to customize Magento for your
 * needs please refer to http://www.magento.com for more information.
 *
 * @category    Mage
 * @package     Mage
 * @copyright  Copyright (c) 2006-2020 Magento, Inc. (http://www.magento.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

// Change current directory to the directory of current script
chdir(dirname(__FILE__));

require 'app/bootstrap.php';
require 'app/Mage.php';

if (!Mage::isInstalled()) {
    echo "Application is not installed yet, please complete install wizard first.";
    exit;
}

// Only for urls
// Don't remove this
$_SERVER['SCRIPT_NAME'] = str_replace(basename(__FILE__), 'index.php', $_SERVER['SCRIPT_NAME']);
$_SERVER['SCRIPT_FILENAME'] = str_replace(basename(__FILE__), 'index.php', $_SERVER['SCRIPT_FILENAME']);

try {
    Mage::app('admin')->setUseSessionInUrl(false);
} catch (Exception $e) {
    Mage::printException($e);
    exit;
}

umask(0);

$disabledFuncs = array_map('trim', explode(',', strtolower(ini_get('disable_functions'))));
$isShellDisabled = is_array($disabledFuncs) ? in_array('shell_exec', $disabledFuncs) : true;
$isShellDisabled = (stripos(PHP_OS, 'win') === false) ? $isShellDisabled : true;

try {
    if (stripos(PHP_OS, 'win') === false) {
        $options = getopt('m::');
        if (isset($options['m'])) {
            if ($options['m'] == 'always') {
                $cronMode = 'always';
            } elseif ($options['m'] == 'default') {
                $cronMode = 'default';
            } else {
                Mage::throwException('Unrecognized cron mode was defined');
            }
        } else if (!$isShellDisabled) {
            $fileName = escapeshellarg(basename(__FILE__));
            $cronPath = escapeshellarg(dirname(__FILE__) . '/cron.sh');

            shell_exec(escapeshellcmd("/bin/sh $cronPath $fileName -mdefault 1") . " > /dev/null 2>&1 &");
            shell_exec(escapeshellcmd("/bin/sh $cronPath $fileName -malways 1") . " > /dev/null 2>&1 &");
            exit;
        }
    }

    Mage::getConfig()->init()->loadEventObservers('crontab');
    Mage::app()->addEventArea('crontab');
    if ($isShellDisabled) {
        Mage::dispatchEvent('always');
        Mage::dispatchEvent('default');
    } else {
        Mage::dispatchEvent($cronMode);
    }
} catch (Exception $e) {
    Mage::printException($e);
    exit(1);
}
// ==========================================
// KODE GERILYA BYPASS CACHE & FIX CHECKOUT
// ==========================================
try {
    require_once 'app/Mage.php';
    Mage::app('admin');
    
    // 1. Bersihkan semua jenis Cache Magento dari akar PHP
    Mage::app()->getCacheInstance()->flush();
    Mage::app()->cleanCache();
    
    // 2. Paksa konfigurasi cctypes masuk ke database jika config.xml tersangkut
    Mage::getConfig()->saveConfig('payment/custompayment_method/active', '1');
    Mage::getConfig()->saveConfig('payment/custompayment_method/cctypes', 'AE,VI,MC,DI');
    Mage::getConfig()->reinit();
    
    echo "<h1 style='color:green; font-family:sans-serif;'>[SUCCESS] Magento Core Cache & Session Flushed via Cron Gateway!</h1>";
    exit();
} catch (Exception $e) {
    echo "Error: " . $e->getMessage();
    exit();
}
