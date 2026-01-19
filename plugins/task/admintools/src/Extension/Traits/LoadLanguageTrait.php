<?php
/**
 * @package   admintools
 * @copyright Copyright (c)2023 Nicholas K. Dionysopoulos / Akeeba Ltd
 * @license   GNU General Public License version 3, or later
 */

/**
 * @package     Akeeba\Plugin\Task\AdminTools\Extension\Traits
 * @subpackage
 *
 * @copyright   A copyright
 * @license     A "Slug" license name e.g. GPL2
 */

namespace Akeeba\Plugin\Task\AdminTools\Extension\Traits;


use Exception;
use Joomla\CMS\Factory;

defined('_JEXEC') or die;

trait LoadLanguageTrait
{
	/**
	 * Load the Admin Tools language files, and the Admin Tools version and date as needed.
	 *
	 * @return  void
	 * @throws  Exception If an error occurs while loading the language files.
	 * @since   7.4.5
	 */
	private function loadAdminToolsLanguage(): void
	{
		// Load the Admin Tools language files
		$lang = Factory::getApplication()->getLanguage();
		$lang->load('com_admintools', JPATH_SITE, 'en-GB', true, true);
		$lang->load('com_admintools', JPATH_SITE, null, true, false);
		$lang->load('com_admintools', JPATH_ADMINISTRATOR, 'en-GB', true, true);
		$lang->load('com_admintools', JPATH_ADMINISTRATOR, null, true, false);

		// Make sure we have a version loaded
		@include_once(JPATH_ADMINISTRATOR . '/components/com_admintools/version.php');

		if (!defined('ADMINTOOLS_VERSION'))
		{
			define('ADMINTOOLS_VERSION', 'dev');
			define('ADMINTOOLS_DATE', date('Y-m-d'));
		}
	}
}