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

class NotesViewDashboard extends SocialAppsView
{
	/**
	 * Generates the list of notes on the user's dashboard
	 *
	 * @since	2.0
	 * @access	public
	 */
	public function display( $userId = null , $docType = null )
	{
		$model = $this->getModel('Notes');
		$notes = $model->getItems($userId);
		$user = ES::user($userId);

		// We need to get the comment and likes count.
		$this->format($notes, $user);

		$params	= $this->getUserParams($userId);

		$this->set('app', $this->app);
		$this->set('params', $params );
		$this->set('user', $user);
		$this->set('notes', $notes);

		echo parent::display('themes:/site/notes/dashboard/default');
	}

	public function format(&$notes, $owner)
	{
		if (!$notes) {
			return;
		}

		$stream = ES::stream();

		foreach ($notes as $note) {
			$note->permalink = ESR::apps(array('layout' => 'canvas', 'id' => $this->app->getAlias(), 'cid' => $note->id, 'uid' => $owner->getAlias(), 'type' => SOCIAL_TYPE_USER));
		}

	}
}
