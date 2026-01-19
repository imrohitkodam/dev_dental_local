<?php
/**
* @package		PayPlans
* @copyright	Copyright (C) Stack Ideas Sdn Bhd. All rights reserved.
* @license		GNU/GPL, see LICENSE.php
* PayPlans is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/
defined('_JEXEC') or die('Unauthorized Access');

jimport('joomla.filesystem.file');

$file = JPATH_ADMINISTRATOR . '/components/com_payplans/includes/payplans.php';

if (!JFile::exists($file)) {
	return;
}

require_once($file);

class plgPayplansEasySocialStorageLimit extends PPPlugins
{
	/**
	 * Determines which category to be shown to the author
	 *
	 * @since	4.2.4
	 * @access	public
	 */
	public function onEasySocialGetStorageSizeLimit($userId, &$size)
	{
		$helper = $this->getAppHelper();
		$user = PP::user($userId);

		$limit = $helper->hasStorageSizeOverride($user);
		if ($limit !== false) {
			$size = $limit;
		}
	}
}