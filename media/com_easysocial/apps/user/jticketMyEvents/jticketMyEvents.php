<?php
/**
 * @version    SVN: <svn_id>
 * @package    JTicketing
 * @author     Techjoomla <extensions@techjoomla.com>
 * @copyright  Copyright (c) 2009-2015 TechJoomla. All rights reserved.
 * @license    GNU General Public License version 2 or later.
 */

// No direct access
defined('_JEXEC') or die();

use Joomla\CMS\Factory;
use Joomla\CMS\Uri\Uri;
use Joomla\CMS\HTML\HTMLHelper;

Foundry::import('admin:/includes/apps/apps');

if (!defined('DS'))
{
	define('DS', '/');
}

/**
 * JTicketing users event application for EasySocial.
 *
 * @version  Release: <1.0>
 * @since    1.0
 */
class SocialUserAppJticketMyEvents extends SocialAppItem
{
	/**
	 * Constructor
	 *
	 * @param   array  $options  country id
	 *
	 * @since   1.0
	 */
	public function __construct ($options = array())
	{
		// Load language file for plugin
		$lang = Factory::getLanguage();
		$lang->load('plg_app_user_jticketMyEvents', JPATH_ADMINISTRATOR);
		$extension                = 'com_jticketing';
		$base_dir                 = JPATH_SITE;
		$lang->load($extension, $base_dir);

		if (file_exists(JPATH_ROOT . '/media/techjoomla_strapper/tjstrapper.php'))
		{
			require_once JPATH_ROOT . '/media/techjoomla_strapper/tjstrapper.php';
			TjStrapper::loadTjAssets('com_jticketing');
		}

		$document = Factory::getDocument();
		HTMLHelper::_('script', 'components/com_jticketing/assets/js/masonry.pkgd.min.js');
		require_once JPATH_SITE . '/components/com_jticketing/helpers/main.php';
		parent::__construct($options);
	}
}
