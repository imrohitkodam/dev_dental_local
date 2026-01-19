<?php
/**
 * @package	AcyMailing for Joomla!
 * @version	5.13.0
 * @author	acyba.com
 * @copyright	(C) 2009-2023 ACYBA S.A.R.L. All rights reserved.
 * @license	GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 */

defined('_JEXEC') or die('Restricted access');
?><?php

use AcyChecker\Classes\ConfigurationClass;
use AcyChecker\Services\ApiService;

class acycheckerHelper
{
    function __construct()
    {
    }

    public function checkSubscriber(&$subscriber)
    {
        if (!acy_isAcyCheckerInstalled()) return true;

        $config = acymailing_config();
        if ($config->get('email_verification') == 0) return true;

        $this->loadAcychecker();

        $cteConfig = new ConfigurationClass();
        $conditions = $cteConfig->get('registration_conditions');

        if (empty($conditions) || $conditions === 'domain_not_exists') return true;

        $apiService = new ApiService();
        $emailOk = $apiService->testEmail($subscriber->email, $conditions);
        if ($emailOk !== true) {
            acymailing_setVar('acychecker_error', acymailing_translation('ACY_INVALID_EMAIL_ADDRESS'));

            return false;
        }

        return true;
    }

    private function loadAcychecker()
    {
        if ('joomla' === 'joomla') {
            $cteFolder = rtrim(JPATH_ADMINISTRATOR, DS).DS.'components'.DS.'com_acychecker'.DS;
        } else {
            $cteFolder = WP_PLUGIN_DIR.DS.'acychecker'.DS;
        }
        include_once $cteFolder.'vendor'.DS.'autoload.php';
        include_once $cteFolder.'defines.php';
    }

    public function redirectAcyChecker()
    {
        if (acy_isAcyCheckerInstalled()) {
            if ('joomla' === 'joomla') {
                acymailing_redirect(acymailing_route('index.php?option=com_acychecker', false));
            } else {
                acymailing_redirect(admin_url().'admin.php?page=acychecker_dashboard');
            }
        } else {
            acymailing_redirect(acymailing_completeLink('dashboard&task=acychecker', false, true));
        }
    }

}
