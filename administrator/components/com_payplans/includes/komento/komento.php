<?php
/**
* @package      PayPlans
* @copyright    Copyright (C) Stack Ideas Sdn Bhd. All rights reserved.
* @license      GNU/GPL, see LICENSE.php
* PayPlans is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/
defined('_JEXEC') or die('Unauthorized Access');

class PPKomento
{
	protected $file = JPATH_ROOT . '/administrator/components/com_komento/includes/komento.php';

	/**
	 * Determines if Komento exists on the site
	 *
	 * @since	5.0.1
	 * @access	public
	 */
	public function exists()
	{
		static $exists = null;

		if (is_null($exists)) {
			$enabled = JComponentHelper::isEnabled('com_komento');
			$fileExists = JFile::exists($this->file);
			$exists = false;

			if ($enabled && $fileExists) {
				$exists = true;
				require_once($this->file);
			}
		}

		return $exists;
	}
}