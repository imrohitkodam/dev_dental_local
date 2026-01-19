<?php

/*
 * @package     Perfect Publisher
 *
 * @author      Extly, CB. <team@extly.com>
 * @copyright   Copyright (c)2012-2025 Extly, CB. All rights reserved.
 * @license     https://www.gnu.org/licenses/gpl-3.0.html GNU/GPL
 *
 * @see         https://www.extly.com
 */

defined('_JEXEC') || exit;

// Check for PHP4
if (defined('PHP_VERSION')) {
    $version = \PHP_VERSION;
} elseif (function_exists('phpversion')) {
    $version = \PHP_VERSION;
} else {
    // No version info. I'll lie and hope for the best.
    $version = '5.0.0';
}

// Old PHP version detected. EJECT! EJECT! EJECT!
if (!version_compare($version, '7.4.0', '>=')) {
    \Joomla\CMS\Factory::getApplication()->enqueueMessage('PHP versions 4.x and 5.x are no longer supported by Perfect Publisher.', 'error');
}

require_once JPATH_ADMINISTRATOR.'/components/com_autotweet/api/autotweetapi.php';

$base_url = EParameter::getComponentParam(CAUTOTWEETNG, 'base_url');

if ((defined('AUTOTWEET_CRONJOB_RUNNING')) && (AUTOTWEET_CRONJOB_RUNNING) && (!filter_var($base_url, \FILTER_VALIDATE_URL))) {
    throw new Exception('AUTOTWEET_CRONJOB: Url base not set.');
}

$config = [];
$controller = null;

// If we are processing Authorizatoion callback, redirect to controller
$session = \Joomla\CMS\Factory::getSession();
$channelId = $session->get('channelId');

// LinkedIn Case
$input = new \Joomla\CMS\Input\Input($_REQUEST);
$linkedInErrorDescription = $input->getString('error_description');

if (!empty($channelId) && $linkedInErrorDescription) {
    \Joomla\CMS\Factory::getApplication()->enqueueMessage($linkedInErrorDescription, 'error');
    $session->set('channelId', false);
}

if (!empty($channelId)) {
    $code = $input->getString('code');
    $oauthToken = $input->getString('oauth_token');
    $oauthVerifier = $input->getString('oauth_verifier');
    $state = $input->getString('state');

    if ($code || $oauthToken || $oauthVerifier || $state) {
        $controller = XTF0FModel::getTmpInstance('Channeltypes', 'AutoTweetModel')->getAuthCallback($channelId);
        $config['input'] = ['task' => 'callback'];
    } else {
        $session->set('channelId', false);
    }
}

// XTF0F app
XTF0FDispatcher::getTmpInstance('com_autotweet', $controller, $config)->dispatch();
