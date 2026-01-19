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

class UpdateController extends acymailingController
{

    function __construct($config = [])
    {
        parent::__construct($config);
        $this->registerDefaultTask('update');
    }

    function listing()
    {
        return $this->update();
    }

    function install()
    {
        acymailing_increasePerf();

        $newConfig = new stdClass();
        $newConfig->installcomplete = 1;
        $config = acymailing_config();

        $updateHelper = acymailing_get('helper.update');

        if (!$config->save($newConfig)) {
            $updateHelper->installTables();

            return;
        }

        $updateHelper->installLanguages();
        $updateHelper->initList();
        $updateHelper->installTemplates();
        $updateHelper->installNotifications();
        $updateHelper->installFields();
        $updateHelper->installMenu();
        $updateHelper->installExtensions();
        $updateHelper->installBounceRules();
        $updateHelper->fixDoubleExtension();
        $updateHelper->addUpdateSite();
        $updateHelper->fixMenu();

        if (ACYMAILING_J30) acymailing_moveFile(ACYMAILING_BACK.'acymailing_j3.xml', ACYMAILING_BACK.'acymailing.xml');

        $acyToolbar = acymailing_get('helper.toolbar');
        $acyToolbar->setTitle('AcyMailing', 'dashboard');
        $acyToolbar->display();

        echo '<div id="acymailing_div"></div>';
        acymailing_display(acymailing_translation('ACY_SUCCESSFULLY_INSTALLED'));
    }

    function update()
    {

        $config = acymailing_config();
        if (!acymailing_isAllowed($config->get('acl_config_manage', 'all'))) {
            acymailing_display(acymailing_translation('ACY_NOTALLOWED'), 'error');

            return false;
        }

        $acyToolbar = acymailing_get('helper.toolbar');
        $acyToolbar->setTitle(acymailing_translation('UPDATE_ABOUT'), 'update');
        $acyToolbar->link(acymailing_completeLink('dashboard'), acymailing_translation('ACY_CLOSE'), 'cancel');
        $acyToolbar->display();

        acymailing_display(acymailing_translation('ACY_SUCCESSFULLY_INSTALLED'));
    }

    function checkForNewVersion()
    {
        $updatemeHelper = acymailing_get('helper.updateme');
        $userInformation = $updatemeHelper->getLicenseInfo();

        if (empty($userInformation)) {
            echo json_encode(['content' => '<br/><span style="color:#C10000;">'.acymailing_translation('ACY_ERROR_LOAD_FROM_ACYBA').'</span><br/>'.$updatemeHelper->errors]);
            exit;
        }

        $menuHelper = acymailing_get('helper.acymenu');
        $myAcyArea = $menuHelper->myacymailingarea();

        echo json_encode(['content' => $myAcyArea]);
        exit;
    }

    function acysms()
    {
        acymailing_redirect('index.php?option=com_acysms');
    }
}

