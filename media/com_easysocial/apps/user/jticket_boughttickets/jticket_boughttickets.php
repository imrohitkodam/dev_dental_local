<?php
/**
 * @version    SVN: <svn_id>
 * @package    JTicketing
 * @author     Techjoomla <extensions@techjoomla.com>
 * @copyright  Copyright (c) 2009-2015 TechJoomla. All rights reserved.
 * @license    GNU General Public License version 2 or later.
 */
defined('_JEXEC') or die('Unauthorized Access');
use Joomla\CMS\Factory;
use Joomla\CMS\HTML\HTMLHelper;

Foundry::import('admin:/includes/apps/apps');

if (!defined('DS'))
{
	define('DS', '/');
}

/**
 * JTicketing bought ticket list application for EasySocial.
 *
 * @version  Release: <1.0>
 * @since    1.0
 */

class SocialUserJticket_BoughtTickets extends SocialAppItem
{
	/**
	 * Class constructor.
	 *
	 * @since 1.0
	 * @access public
	 */
	public function __construct ()
	{
		// Load language file for plugin
		$lang = Factory::getLanguage();
		$lang->load('plg_app_user_jticket_boughttickets', JPATH_ADMINISTRATOR);

		if (file_exists(JPATH_ROOT . '/media/techjoomla_strapper/tjstrapper.php'))
		{
			require_once JPATH_ROOT . '/media/techjoomla_strapper/tjstrapper.php';
			TjStrapper::loadTjAssets('com_jticketing');
		}

		HTMLHelper::_('bootstrap.tooltip');
		HTMLHelper::_('bootstrap.renderModal', 'a.modal');
		$extension                = 'com_jticketing';
		$base_dir                 = JPATH_SITE;
		$lang->load($extension, $base_dir);

		parent::__construct();
	}
}
