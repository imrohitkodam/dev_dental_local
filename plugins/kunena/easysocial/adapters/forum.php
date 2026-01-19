<?php
/**
* @package		EasySocial
* @copyright	Copyright (C) Stack Ideas Sdn Bhd. All rights reserved.
* @license		GNU/GPL, see LICENSE.php
* EasySocial is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/
defined('_JEXEC') or die('Unauthorized Access');

class SocialKunenaAdapterPluginForum
{
	/**
	 * Determines if kunena 5.x is enabled
	 *
	 * @since	6.0
	 * @access	public
	 */
	public function isEnabled()
	{
		static $exists = null;

		if (is_null($exists)) {

			$file = JPATH_ADMINISTRATOR . '/components/com_kunena/api.php';
			$exists = false;

			if (!JFile::exists($file)) {
				return $exists;
			}

			$exists = true;

			// Load Kunena's api file
			require_once($file);

			// Load Kunena's language
			KunenaFactory::loadLanguage('com_kunena.libraries', 'admin');
		}

		return $exists;
	}
}
