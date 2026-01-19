<?php
/**
* @package		EasySocial
* @copyright	Copyright (C) 2010 - 2016 Stack Ideas Sdn Bhd. All rights reserved.
* @license		GNU/GPL, see LICENSE.php
* EasySocial is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/
defined('_JEXEC') or die('Unauthorized Access');

class FeedsViewDashboard extends SocialAppsView
{
	/**
	 * Renders the list of feeds created by the user
	 *
	 * @since	2.0
	 * @access	public
	 */
	public function display($userId = null, $docType = null)
	{
		$model = $this->getModel('Feeds');
		$result	= $model->getItems($userId);

		// If there are tasks, we need to bind them with the table.
		$feeds = array();

		if ($result) {
			foreach ($result as $row) {

				// Bind the result back to the note object.
				$feed = $this->getTable('Feed');
				$feed->bind($row);

				$feeds[] = $feed;
			}
		}

		$this->set('app', $this->app);
		$this->set('feeds', $feeds);

		echo parent::display('dashboard/default');
	}
}
