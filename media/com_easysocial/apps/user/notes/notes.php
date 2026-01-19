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

ES::import('admin:/includes/apps/apps');

class SocialUserAppNotes extends SocialAppItem
{
	/**
	 * Determines if the viewer can access the object for comments / reaction
	 *
	 * @since	3.0
	 * @access	public
	 */
	public function isItemViewable($action, $context, $verb, $uid)
	{
		if ($context != 'notes') {
			return;
		}

		$table = ES::table('streamitem');
		$table->load(array('context_type' => $context, 'verb' => $verb, 'context_id' => $uid));

		if (!$table->id) {
			// item not found.
			return false;
		}

		$privacy = $this->my->getPrivacy();

		if (!$privacy->validate('core.view', $table->id, SOCIAL_TYPE_ACTIVITY, $table->actor_id)) {
			return false;
		}

		return true;
	}

	/**
	 * Determines if the element is supported in this app
	 *
	 * @since	3.1.0
	 * @access	public
	 */
	private function isSupportedElement($element)
	{
		static $supported = null;

		if (is_bool($element)) {
			return false;
		}

		if (!isset($supported[$element])) {
			$supported[$element] = false;
			$allowed = array('notes.user.create', 'notes.user.update');

			if (in_array($element, $allowed)) {
				$supported[$element] = true;
			}
		}

		return $supported[$element];
	}

	/**
	 * Triggered to validate the stream item whether should put the item as valid count or not.
	 *
	 * @since	1.2
	 * @access	public
	 */
	public function onStreamCountValidation(&$item, $includePrivacy = true)
	{
		// If this is not it's context, we don't want to do anything here.
		if ($item->context_type != 'notes') {
			return false;
		}

		$item->cnt = 1;

		if ($includePrivacy) {

			$uid = $item->id;
			$my = ES::user();
			$privacy = ES::privacy($my->id);

			$sModel = ES::model('Stream');
			$aItem 	= $sModel->getActivityItem($item->id, 'uid');

			if ($aItem) {
				$uid = $aItem[0]->id;

				if (!$privacy->validate('core.view', $uid , SOCIAL_TYPE_ACTIVITY , $item->actor_id)) {
					$item->cnt = 0;
				}
			}
		}

		return true;
	}

	/**
	 * Responsible to return the excluded verb from this app context
	 *
	 * @since	1.2
	 * @access	public
	 */
	public function onStreamVerbExclude(&$exclude)
	{
		// Get app params
		$params = $this->getParams();

		$excludeVerb = [];

		if (! $params->get('stream_update', true)) {
			$excludeVerb[] = 'update';
		}

		if (! $params->get('stream_create', true)) {
			$excludeVerb[] = 'create';
		}

		if ($excludeVerb !== false) {
			$exclude['notes'] = $excludeVerb;
		}
	}

	/**
	 * Prepares the stream item
	 *
	 * @since	1.0
	 * @access	public
	 */
	public function onPrepareStream(SocialStreamItem &$item, $includePrivacy = true)
	{
		if ($item->context !== 'notes') {
			return;
		}

		// Determine if we should display the stream items
		$params = $this->getParams();

		$allowed = array('create', 'update');

		if (!in_array($item->verb, $allowed)) {
			return;
		}

		if (!$params->get('stream_' . $item->verb, true)) {
			return;
		}

		// Load the note
		$note = $this->getTable('Note');
		$note->load($item->contextId);

		$item->comments = ES::comments($item->contextId, $item->context, $item->verb, SOCIAL_APPS_GROUP_USER, array('url' => $note->getPermalink(false, true, false)));
		$item->likes = ES::likes($item->contextId, $item->context, $item->verb, SOCIAL_APPS_GROUP_USER, $item->uid);
		$item->repost = ES::repost($item->uid, SOCIAL_TYPE_STREAM);

		$this->set('params', $params);
		$this->set('note', $note);
		$this->set('actor', $item->actor);

		$item->display = SOCIAL_STREAM_DISPLAY_FULL;
		$item->title = parent::display('themes:/site/streams/notes/' . $item->verb . '.title');
		$item->preview = parent::display('themes:/site/streams/notes/preview');

		// Append the opengraph tags
		$item->addOgDescription($note->getContent());
	}


	/**
	 * Processes notifications
	 *
	 * @since	1.2
	 * @access	public
	 */
	public function onNotificationLoad(SocialTableNotification &$item)
	{
		if (!$this->isSupportedElement($item->context_type)) {
			return;
		}

		if ($item->type == 'likes') {

			$note = $this->getTable('Note');
			$note->load($item->uid);

			$obj = $this->getHook('notification', 'likes');
			$obj->execute($item, $note);

			return;
		}

		if ($item->type == 'comments') {

			$note = $this->getTable('Note');
			$note->load($item->uid);

			$obj = $this->getHook('notification', 'comments');
			$obj->execute($item, $note);

			return;
		}
	}

	/**
	 * Before a comment is deleted, delete notifications tied to the comment
	 *
	 * @since	3.1.0
	 * @access	public
	 */
	public function onBeforeDeleteComment(SocialTableComments $comment)
	{
		if (!$this->isSupportedElement($comment->element)) {
			return;
		}

		// Here we know that comments associated with article is always
		// comment.uid = notification.uid
		$model = ES::model('Notifications');
		$model->deleteNotificationsWithUid($comment->uid, $comment->element);
	}

	/**
	 * Processes notifications
	 *
	 * @since	1.2
	 * @access	public
	 */
	public function onAfterCommentSave($comment)
	{
		if (!$this->isSupportedElement($comment->element)) {
			return;
		}

		// Get the verb
		$segments = explode('.', $comment->element);
		$verb = isset($segments[2]) ? $segments[2] : '';

		if (!$verb) {
			return;
		}

		// Get the note object
		$note = $this->getTable('Note');
		$note->load($comment->uid);

		// retrieve the first mentioned user from the comment
		$exclusion = $comment->getFirstMentionedUserFromComment();

		// Convert the mention tag to permalink only for comment.
		$commentOptions = [
			'commentId' => $comment->id,
			'hasTag' => true,
			'exclusion' => $exclusion
		];

		$parseBBCodeOptions = [
			'escape' => false,
			'links' => true,
			'code' => true
		];

		$stringLib = ES::string();
		$commentContent = $stringLib->normalizeContent($comment->comment, $parseBBCodeOptions, false, '', $commentOptions);

		$emailOptions = array(
			'title' => 'APP_USER_NOTES_EMAILS_COMMENT_ITEM_TITLE',
			'template' => 'apps/user/notes/comment.item',
			'comment' => $commentContent,
			'permalink' => $note->getPermalink(true, true)
		);

		$systemOptions = array(
			'title' => '',
			'content' => $commentContent,
			'context_type' => $comment->element,
			'url' => $note->getPermalink(false, false, false),
			'actor_id' => $comment->created_by,
			'uid' => $comment->uid,
			'aggregate' => true
		);

		// Notify the note owner if the commenter is not the note owner
		if ($comment->created_by != $note->user_id && !$comment->isChild()) {
			ES::notify('comments.item', array($note->user_id), $emailOptions, $systemOptions);
		}

		// Get a list of recipients to be notified for this stream item.
		// We exclude the owner of the note and the actor of the like here
		$recipients = $this->getStreamNotificationTargets($comment->uid, 'notes', 'user', $verb, array(), array($note->user_id, $comment->created_by));

		$emailOptions['title'] = 'APP_USER_NOTES_EMAILS_COMMENT_INVOLVED_TITLE';
		$emailOptions['template'] = 'apps/user/notes/comment.involved';

		if (!$comment->isChild()) {
			// Notify other participating users
			ES::notify('comments.involved', $recipients, $emailOptions, $systemOptions);
		}

		$emailOptions['title'] = 'COM_ES_EMAILS_REPLIED_TITLE_SUBJECT';
		$emailOptions['template'] = 'apps/user/notes/comment.replied';

		// Notify the owner of the parent comment
		if ($comment->isChild() && $comment->created_by != $comment->getParent()->created_by) {
			ES::notify('comments.replied', [$comment->getParent()->created_by], $emailOptions, $systemOptions);
		}
	}

	/**
	 * Processes notifications
	 *
	 * @since	1.2
	 * @access	public
	 */
	public function onAfterLikeSave($likes)
	{
		if (!$this->isSupportedElement($likes->type)) {
			return;
		}

		// Get the verb
		$segments = explode('.', $likes->type);
		$verb = $segments[2];

		$note = $this->getTable('Note');
		$note->load($likes->uid);

		$systemOptions = array(
			'title' => '',
			'context_type' => $likes->type,
			'url' => $note->getPermalink(false, false, false),
			'actor_id' => $likes->created_by,
			'uid' => $likes->uid,
			'aggregate' => true
		);

		// Notify the owner first if the liker is not the note owner
		if ($likes->created_by != $note->user_id) {
			ES::notify('likes.item', array($note->user_id), array(), $systemOptions);
		}

		// Get a list of recipients to be notified for this stream item
		// We exclude the owner of the note and the actor of the like here
		$recipients = $this->getStreamNotificationTargets($likes->uid, 'notes', 'user', $verb, array(), array($note->user_id, $likes->created_by));

		ES::notify('likes.involved', $recipients, array(), $systemOptions);
	}

	/**
	 * Prepares the activity log
	 *
	 * @since	1.0
	 * @access	public
	 */
	public function onPrepareActivityLog(SocialStreamItem &$item, $includePrivacy = true)
	{
		if ($item->context !== 'notes') {
			return;
		}

		$note = $this->getTable('Note');
		$note->load($item->contextId);

		$this->set('note', $note);
		$this->set('actor', $item->actor);

		$item->title = parent::display('streams/' . $item->verb . '.title');
	}

	/**
	 * Method to load notification for the REST API
	 *
	 * @since	3.1.0
	 * @access	public
	 */
	public function onPrepareRestNotification(&$item, SocialUser $viewer)
	{
		if (!$this->isSupportedElement($item->context_type)) {
			return;
		}

		// Run standard notification processing
		$this->onNotificationLoad($item);
		$target = $item->target;

		// Load the notes to get user id
		$note = $this->getTable('Note');
		$note->load($item->uid);

		$target->id = $item->uid;
		$target->type = 'app';
		$target->endpoint = 'app';
		$target->query_string = 'app=' . $this->getApp()->id . '&type=user&uid=' . $note->user_id . '&id=' . $note->id;

		$item->target = $target;
	}
}
