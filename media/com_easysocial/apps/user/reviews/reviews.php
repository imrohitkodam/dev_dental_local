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

ES::import('admin:/includes/apps/apps');

class SocialUserAppReviews extends SocialAppItem
{
	/**
	 * Determines if the viewer can access the object for comments / reaction
	 *
	 * @since	3.0.0
	 * @access	public
	 */
	public function isItemViewable($action, $context, $verb, $uid)
	{
		if ($context != SOCIAL_TYPE_REVIEWS) {
			return;
		}

		return false;
	}

	/**
	 * Notification triggered when generating notification item.
	 *
	 * @since	3.0
	 * @access	public
	 */
	public function onNotificationLoad(SocialTableNotification &$item)
	{
		$allowed = array('user.moderate.review');

		// If the cmd not allowed, return.
		if (!in_array($item->cmd, $allowed)) {
			return;
		}

		$user = ES::user($item->actor_id);
		$reviewedUser = ES::user($item->uid);
		$item->image = $reviewedUser->getAvatar();

		if ($item->cmd == 'user.moderate.review') {
			$item->title = JText::sprintf('APP_REVIEW_NOTIFICATIONS_PENDING_MODERATION', $user->getName());
		}
	}

	/**
	 * Determines if the app should appear on the sidebar
	 *
	 * @since	3.0
	 * @access	public
	 */
	public function appListing($view, $id, $type)
	{
		if ($type != SOCIAL_TYPE_USER) {
			return true;
		}

		// We should not display the reviews on the app if it's disabled
		$user = ES::user($id);
		$registry = $user->getParams();

		if (!$registry->get('reviews', true)) {
			return false;
		}

		return true;
	}

	/**
	 * Triggered to validate the stream item whether should put the item as valid count or not.
	 *
	 * @since	3.0
	 * @access	public
	 */
	public function onStreamCountValidation(&$item, $includePrivacy = true)
	{
		// If this is not it's context, we don't want to do anything here.
		if ($item->context_type != 'reviews') {
			return false;
		}

		// if this is a cluster stream, let check if user can view this stream or not.
		$params = ES::registry($item->params);
		$user = ES::user($params->get('user'));

		if (!$user) {
			return;
		}

		$item->cnt = 1;

		return true;
	}

	/**
	 * Trigger for onPrepareDigest
	 *
	 * @since	3.0
	 * @access	public
	 */
	public function onPrepareDigest(SocialStreamItem &$item)
	{
		if ($item->context != 'reviews') {
			return;
		}

		// user access checking
		$user = $item->getActor();

		if (!$user) {
			return;
		}

		$params = $item->getParams();

		$reviews = ES::table('Reviews');
		$exists = $reviews->load($params->get('reviews')->id);

		if (!$exists) {
			return;
		}

		$item->title = '';
		$item->link = $reviews->getPermalink(true, true);

		if ($item->verb == 'create') {
			$item->title = JText::sprintf('COM_ES_APP_REVIEWS_DIGEST_CREATE_TITLE', $user, $reviews->title);
		}
	}

	/**
	 * Prepares the stream item for groups
	 *
	 * @since	3.0
	 * @access	public
	 */
	public function onPrepareStream(SocialStreamItem &$item, $includePrivacy = true)
	{
		if ($item->context != 'reviews') {
			return;
		}

		// group access checking
		$user = ES::user($item->targets[0]);

		if (!$user) {
			return;
		}

		// Define standard stream looks
		$item->display = SOCIAL_STREAM_DISPLAY_FULL;
		$item->repost = false;

		$params = $item->getParams();

		if ($item->verb == 'create') {
			$this->prepareCreateStream($item, $user, $params);
		}

		// Append the opengraph tags
		$item->addOgDescription();
	}

	private function prepareCreateStream(SocialStreamItem &$item, SocialUser $user, $params)
	{
		$reviews = ES::table('Reviews');
		$reviews->load($params->get('reviews')->id);

		// Get the permalink
		$permalink = $reviews->getPermalink();

		// Get the app params
		$appParams 	= $this->getApp()->getParams();

		// Format the content
		$this->format($reviews, $appParams->get('stream_length'));

		// Attach actions to the stream
		$commentUrl = $reviews->getPermalink(true, false, false);
		$this->attachActions($item, $reviews, $commentUrl, $appParams, $user);

		if ($this->my->isSiteAdmin() || $user->isViewer()) {
			$item->edit_link = $reviews->getEditPermalink();;
		}

		$this->set('item', $item);
		$this->set('cluster', $user);
		$this->set('appParams', $appParams);
		$this->set('permalink', $permalink);
		$this->set('reviews', $reviews);
		$this->set('actor', $item->actor);

		// Load up the contents now.
		$item->title = parent::display('themes:/site/streams/reviews/create.title');
		$item->preview = parent::display('themes:/site/streams/reviews/preview');
	}

	private function format(&$reviews, $length = 0)
	{
		if ($length == 0) {
			return;
		}

		$reviews->content = JString::substr(strip_tags($reviews->content), 0, $length) . ' ' . JText::_('COM_EASYSOCIAL_ELLIPSES');
	}

	private function attachActions(&$item, &$reviews, $permalink, $appParams, $user)
	{
		$commentParams = array('url' => $permalink);
		// We need to link the comments to the reviews
		$item->comments = ES::comments($reviews->id, 'reviews', 'create', SOCIAL_APPS_GROUP_GROUP, $commentParams, $item->uid);

		// The comments for the stream item should link to the reviews itself.
		if (!$appParams->get('allow_comments') || !$reviews->comments) {
			$item->comments = false;
		}

		// The likes needs to be linked to the reviews itself
		$likes = ES::likes();
		$likes->get($reviews->id, 'reviews', 'create', SOCIAL_APPS_GROUP_GROUP, $item->uid);

		$item->likes = $likes;
	}

	/**
	 * Export the notification
	 *
	 * @since	3.1.0
	 * @access	public
	 */
	public function onNotificationExport(&$item, &$content, $viewer)
	{
		$allowed = array('user.moderate.review');

		// If the cmd not allowed, return.
		if (!in_array($item->cmd, $allowed)) {
			return;
		}

		$content->view = 'user.reviews';
		$content->view_id = $viewer->id;
	}
}
