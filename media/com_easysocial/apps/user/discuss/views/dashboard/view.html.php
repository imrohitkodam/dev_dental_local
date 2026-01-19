<?php
/**
* @package		EasyDiscuss
* @copyright	Copyright (C) 2010 - 2015 Stack Ideas Sdn Bhd. All rights reserved.
* @license		GNU/GPL, see LICENSE.php
* EasyDiscuss is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/
defined('_JEXEC') or die('Unauthorized Access');

require_once(JPATH_ADMINISTRATOR . '/components/com_easydiscuss/includes/router.php');

/**
 * Dashboard view for Discuss app.
 *
 * @since	1.0
 * @access	public
 */
class DiscussViewDashboard extends SocialAppsView
{
	public function exists()
	{
		$file = JPATH_ADMINISTRATOR . '/components/com_easydiscuss/includes/easydiscuss.php';

		if (!JFile::exists($file)) {
			return false;
		}

		require_once($file);

		return true;
	}

	/**
	 * Displays the application output in the canvas.
	 *
	 * @since	1.0
	 * @access	public
	 * @param	int		The user id that is currently being viewed.
	 */
	public function display($userId = null, $docType = null)
	{
		// Check if EasyDiscuss really exists on the site
		if (!$this::exists()) {
			echo JText::_('APP_EASYDISCUSS_EASYDISCUSS_NOT_INSTALLED');
			return;
		}

		// Get the discuss model
		$model = ED::model('Subscribe');

		// Get list of blog posts created by the user on the site.
		$subscriberPosts = $model->getSubscriptions($userId);

		$subs = array();

		if ($subscriberPosts) {
			foreach ($subscriberPosts as $subscriberPost) {
				$obj = new stdClass();
				$obj->id = $subscriberPost->id;
				$obj->type = $subscriberPost->type;
				$obj->cid = $subscriberPost->cid;
				$obj->unsublink	= ED::getUnsubscribeLink($subscriberPost, false);

				if ($subscriberPost->type == 'site') {
					$obj->title	= JText::_("APP_EASYDISCUSS_SITE_SUBSCRIBED");
					$obj->link = EDR::_('index.php?option=com_easydiscuss');

				} else if ($subscriberPost->type == 'post') {
					$post = ED::table('Post');


					$post->load($subscriberPost->cid);
					$obj->title	= $post->title;
					$obj->link	= EDR::_('index.php?option=com_easydiscuss&view=post&id=' . $post->id);

				} else if ($subscriberPost->type == 'category') {
					$category = ED::table('Category');
					$category->load($subscriberPost->cid);
					$obj->title	= $category->title;
					$obj->link = EDR::getCategoryRoute($category->id);

				} else if ($subscriberPost->type == 'user') {
					$profile = ED::user($subscriberPost->cid);
					$obj->title	= $profile->getName();
					$obj->link = $profile->getLink();

				} else {
					unset($obj);
				}

				if (!empty($obj)) {
					$obj->title	= ED::string()->escape($obj->title);
					$subs[$subscriberPost->type][] = $obj;
					unset($obj);
				}
			}
		}

		$this->set('subs', $subs);
		$this->set('subscriberPosts', $subscriberPosts);

		echo parent::display('dashboard/default');
	}
}
