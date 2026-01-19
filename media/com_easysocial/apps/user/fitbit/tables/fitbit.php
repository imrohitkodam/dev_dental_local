<?php
/**
* @package		EasySocial
* @copyright	Copyright (C) 2010 - 2019 Stack Ideas Sdn Bhd. All rights reserved.
* @license		GNU/GPL, see LICENSE.php
* EasySocial is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/
defined('_JEXEC') or die('Unauthorized Access');

ES::import('admin:/tables/table');

class FitbitTableFitbit extends SocialTable
{
	public $id = null;
	public $user_id = null;
	public $created = null;
	public $token = null;
	public $expires = null;
	public $params = null;
	public $cron = null;
	public $updated = null;

	public function __construct(&$db)
	{
		parent::__construct('#__social_fitbit', 'id', $db);
	}

	/**
	 * Update the flag on the column so that we know this item is already processed by the cron during the cron cycle
	 *
	 * @since	3.2.0
	 * @access	public
	 */
	public function lockForCron()
	{
		$this->cron = 1;
		$this->store();
	}

	/**
	 * Update the flag on the column so that we know this item is already processed by the cron during the cron cycle
	 *
	 * @since	3.2.0
	 * @access	public
	 */
	public function unlockCron()
	{
		$this->cron = 0;
		$this->store();
	}
}
