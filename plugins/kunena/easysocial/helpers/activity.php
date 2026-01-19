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

use Joomla\String\StringHelper;

class KunenaActivityEasySocial extends KunenaActivity
{
	protected $params = null;

	public function __construct($params)
	{
		$this->params = $params;
	}

	/**
	 * @param $message
	 *
	 * @since Kunena
	 */
	public function onAfterPost($message)
	{
		if (StringHelper::strlen($message->message) > $this->params->get('activity_points_limit', 0)) {
			$this->assignPoints('thread.new');
		}

		if (StringHelper::strlen($message->message) > $this->params->get('activity_badge_limit', 0)) {
			$this->assignBadge('thread.new', JText::_('PLG_KUNENA_EASYSOCIAL_BADGE_NEW_TITLE'));
		}

		$stream = ES::stream();

		$tmpl = $stream->getTemplate();

		$tmpl->setActor($message->userid, SOCIAL_TYPE_USER);
		$tmpl->setContext($message->thread, 'kunena');
		$tmpl->setVerb('create');
		$tmpl->setAccess('core.view');

		$stream->add($tmpl);
	}

	/**
	 * Assign points to user
	 *
	 * @since	2.1.11
	 * @access	public
	 */
	public function assignPoints($command, $target = null)
	{
		$user = ES::user($target);
		$points = ES::points();

		return $points->assign($command, 'com_kunena', $user->id);
	}

	/**
	 * Assign badge to user
	 *
	 * @since	2.1.11
	 * @access	public
	 */
	public function assignBadge($command, $message, $target = null)
	{
		$user = ES::user($target);
		$badge = ES::badges();

		return $badge->log('com_kunena', $command, $user->id, $user->id);
	}

	/**
	 * Triggered after a person replies to a thread
	 *
	 * @since	2.1.11
	 * @access	public
	 */
	public function onAfterReply($message)
	{
		$length = StringHelper::strlen($message->message);

		// Assign points for replying a thread
		if ($length > $this->params->get('activity_points_limit', 0)) {
			$this->assignPoints('thread.reply');
		}

		// Assign badge for replying to a thread
		if ($length > $this->params->get('activity_badge_limit', 0)) {
			$this->assignBadge('thread.reply', JText::_('PLG_KUNENA_EASYSOCIAL_BADGE_REPLY_TITLE'));
		}

		$stream = ES::stream();

		$tmpl = $stream->getTemplate();
		$tmpl->setActor($message->userid, SOCIAL_TYPE_USER);
		$tmpl->setContext($message->id, 'kunena');
		$tmpl->setVerb('reply');
		$tmpl->setAccess('core.view');

		$stream->add($tmpl);

		// Get a list of subscribers
		$recipients = $this->getSubscribers($message);

		if (!$recipients) {
			return;
		}

		$permalink = \Joomla\CMS\Uri\Uri::getInstance()->toString(array('scheme', 'host', 'port')) . $message->getPermaUrl(null);

		$options = [
			'uid' => $message->id,
			'actor_id' => $message->userid,
			'title' => '',
			'type' => 'post',
			'url' => $permalink,
			'image' => '',
		];

		// Add notifications in EasySocial
		ES::notify('post.reply', $recipients, array(), $options);
	}

	/**
	 * Retrieves a list of subscribers on a thread
	 *
	 * @since	2.1.11
	 * @access	public
	 */
	public function getSubscribers($message)
	{
		$config = KunenaFactory::getConfig();

		if ($message->hold > 1) {
			return false;
		}

		$mailsubs = (bool) $config->allowsubscriptions;
		$mailmods = $config->mailmod >= 1;
		$mailadmins = $config->mailadmin >= 1;

		if ($message->hold == 1) {
			$mailsubs = 0;
			$mailmods = $config->mailmod >= 0;
			$mailadmins = $config->mailadmin >= 0;
		}

		$once = false;

		if ($mailsubs) {
			if (!$message->parent) {
				// New topic: Send email only to category subscribers
				$mailsubs = $config->category_subscriptions != 'disabled' ? KunenaAccess::CATEGORY_SUBSCRIPTION : 0;
				$once = $config->category_subscriptions == 'topic';
			} elseif ($config->category_subscriptions != 'post') {
				// Existing topic: Send email only to topic subscribers
				$mailsubs = $config->topic_subscriptions != 'disabled' ? KunenaAccess::TOPIC_SUBSCRIPTION : 0;
				$once = $config->topic_subscriptions == 'first';
			} else {
				// Existing topic: Send email to both category and topic subscribers
				$mailsubs = $config->topic_subscriptions == 'disabled' ? KunenaAccess::CATEGORY_SUBSCRIPTION : KunenaAccess::CATEGORY_SUBSCRIPTION | KunenaAccess::TOPIC_SUBSCRIPTION;

				// FIXME: category subcription can override topic
				$once = $config->topic_subscriptions == 'first';
			}
		}

		// Get all subscribers, moderators and admins who will get the email
		$me = KunenaUserHelper::get();
		$acl = KunenaAccess::getInstance();
		$subscribers = $acl->getSubscribers($message->catid, $message->thread, $mailsubs, $mailmods, $mailadmins, $me->userid);

		if (!$subscribers) {
			return false;
		}

		$result = [];

		foreach ($subscribers as $subscriber) {
			if ($subscriber->id) {
				$result[] = $subscriber->id;
			}
		}

		return $result;
	}

	/**
	 * After a user is thanked
	 *
	 * @since	2.1.11
	 * @access	public
	 */
	public function onAfterThankyou($actor, $target, $message)
	{
		if (StringHelper::strlen($message->message) > $this->params->get('activity_points_limit', 0)) {
			$this->assignPoints('thread.thanks', $target);
		}

		$this->assignBadge('thread.thanks', JText::_('PLG_KUNENA_EASYSOCIAL_BADGE_THANKED_TITLE'), $target);

		$stream = ES::stream();
		$tmpl = $stream->getTemplate();

		$tmpl->setActor($actor, SOCIAL_TYPE_USER);
		$tmpl->setTarget($target);
		$tmpl->setContext($message->id, 'kunena');
		$tmpl->setVerb('thanked');
		$tmpl->setAccess('core.view');

		$stream->add($tmpl);
	}

	/**
	 * After deleting a topic, ensure that the stream item is removed as well
	 *
	 * @since	2.1.11
	 * @access	public
	 */
	public function onAfterDeleteTopic($topic)
	{
		$stream = ES::stream();
		$stream->delete($topic->id, 'thread.new');
	}
}
