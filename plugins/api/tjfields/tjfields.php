<?php
/**
 * @package     Tjfields
 * @subpackage  com_tjfields
 *
 * @author      Techjoomla <extensions@techjoomla.com>
 * @copyright   Copyright (C) 2009 - 2020 Techjoomla. All rights reserved.
 * @license     http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
 */

defined('_JEXEC') or die( 'Restricted access');

use Joomla\CMS\Factory;
/**
 * Base Class for api plugin
 *
 * @package     Tjfields
 * @subpackage  component
 * @since       __DEPLOY_VERSION__
 */
class PlgAPITjfields extends ApiPlugin
{
	/**
	 * Tjfields api plugin to load com_api classes
	 *
	 * @param   string  $subject  The object to observe
	 * @param   array   $config   An optional associative array of configuration settings.
	 *
	 * @since   __DEPLOY_VERSION__
	 */
	public function __construct($subject, $config = array())
	{
		parent::__construct($subject, $config = array());
		ApiResource::addIncludePath(dirname(__FILE__) . '/tjfields');

		// Load language files
		$lang = Factory::getLanguage();
		$lang->load('plg_api_tjfields', JPATH_ADMINISTRATOR, '', true);
		$lang->load('com_tjfields', JPATH_SITE, '', true);
	}
}
