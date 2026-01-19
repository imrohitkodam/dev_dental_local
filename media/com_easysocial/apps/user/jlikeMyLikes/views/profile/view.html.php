<?php
/**
 * @version    SVN: <svn_id>
 * @package    JLike
 * @author     Techjoomla <extensions@techjoomla.com>
 * @copyright  Copyright (c) 2009-2015 TechJoomla. All rights reserved.
 * @license    GNU General Public License version 2 or later.
 */

// No direct access
defined('_JEXEC') or die();

/**
 * Profile view for JLikeMyLikes app
 *
 * @since  1.0
 * @access  public
 */
class JlikeMyLikesViewProfile extends SocialAppsView
{
	/**
	 * Displays the application output in the canvas.
	 *
	 * @param   int     $userId   The user id that is currently being viewed.
	 * @param   string  $docType  Document type
	 *
	 * @since   1.0
	 * @access  public
	 *
	 * @return  HTML
	 */
	public function display ($userId = null, $docType = null)
	{
		// Get the user params
		$params = $this->getUserParams($userId);

		// Get the app params
		$appParams = $this->app->getParams();

		$total = (int) $params->get('total', $appParams->get('total', 5));
		$this->set('total', $total);

		// Get list of all likes user has likes on the site.
		$model   = $this->getModel('jlikeMyLikes');
		$records = $model->getItems($userId, $total);
		$this->set('records', $records);

		$user = Foundry::user($userId);
		$this->set('user', $user);
		$this->set('userId', $userId);

		$app = JFactory::getApplication();
		$jlikeParams = $app->getParams('com_jlike');
		$this->set('jlikeParams', $jlikeParams);

		echo parent::display('profile/default');
	}
}
