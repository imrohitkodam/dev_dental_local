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

PP::import('admin:/tables/table');

class PayplansTableDownload extends PayplansTable
{
	public $download_id = null;
	public $user_id = null;
	public $state = null;
	public $params = null;
	public $created = null;

	public function __construct($db)
	{
		parent::__construct('#__payplans_download', 'download_id', $db);
	}

	/**
	 * Override parent delete method to manually delete archive file as well.
	 *
	 * @since	5.0.1
	 * @access	public
	 */
	public function delete($pk = null)
	{
		$user = PP::user($this->user_id);
		$gdpr = PP::gdpr();

		// Delete temporary folder if it exists
		$tmpFolder = PP_DOWNLOADS . '/' . md5($user->getId() . $user->getEmail());

		if (JFolder::exists($tmpFolder)) {
			JFolder::delete($tmpFolder);
		}

		$params = $this->getParams();

		if ($params) {
			$file = $params->get('path', '');

			if ($file && JFile::exists($file)) {
				JFile::delete($file);
			}
		}

		return parent::delete($pk);
	}
}