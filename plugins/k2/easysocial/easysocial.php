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

jimport('joomla.filesystem.file');

$file = JPATH_ADMINISTRATOR . '/components/com_easysocial/includes/plugins.php';

if (!JFile::exists($file)) {
	return;
}

require_once($file);
require_once(JPATH_ROOT . '/components/com_content/helpers/route.php');

class PlgK2EasySocial extends EasySocialPlugins
{
	public $group = 'k2';
	public $element = 'easysocial';
	protected $autoloadLanguage = true;

	/**
	 * Generate new stream activity.
	 *
	 * @since	1.0
	 * @access	public
	 */
	private function createStream($article, $verb, $actor = null)
	{
		$tmpl = ES::stream()->getTemplate();

		if (is_null($actor)) {
			$actor = $article->created_by;
		}

		//check if this user already perform the same action or not.
		$stream = ES::stream();

		if ($stream->exists($article->id, 'k2', $verb, $actor)) {
			return;
		}

		// Set the creator of this article.
		$tmpl->setActor($actor, SOCIAL_TYPE_USER);

		// Set the context of the stream item.
		$tmpl->setContext($article->id, 'k2');

		// Set the verb
		$tmpl->setVerb($verb);

		JTable::addIncludePath(JPATH_ROOT . '/administrator/components/com_k2/tables');

		$category = JTable::getInstance('K2Category', 'Table');
		$category->load($article->catid);

		require_once(JPATH_ROOT . '/components/com_k2/helpers/route.php');

		// Get the permalink
		$permalink = K2HelperRoute::getItemRoute( $article->id . ':' . $article->alias , $article->catid );

		// Get the category permalink
		$categoryPermalink = K2HelperRoute::getCategoryRoute($category->id . ':' . $category->alias);

		// Store the article in the params
		$registry = new JRegistry();
		$registry->set('article', $article);
		$registry->set('category', $category);
		$registry->set('permalink', $permalink);
		$registry->set('categoryPermalink', $categoryPermalink);

		// We need to tell the stream that this uses the core.view privacy.
		$tmpl->setAccess('core.view');

		// Set the template params
		$tmpl->setParams($registry);

		ES::stream()->add($tmpl);
	}

	/**
	 * Retrieves the plugin params
	 *
	 * @since	3.2.16
	 * @access	public
	 */
	public function getPluginParams()
	{
		static $tmp = false;

		if (!$tmp) {
			$plugin	= JPluginHelper::getPlugin('k2', 'easysocial');
			$tmp = new JRegistry($plugin->params);
		}

		return $tmp;
	}


	/**
	 * Get the user id given the k2 profile id
	 *
	 * @since	1.2
	 * @access	public
	 */
	public function getUserId($profileId)
	{
		$db = ES::db();
		$query = array(
			'SELECT `userID` as `id` FROM `#__k2_users`',
			'WHERE `id`=' . $db->Quote($profileId)
		);

		$db->setQuery($query);

		$id = $db->loadResult();

		return $id;
	}

	/**
	 * Determines if EasySocial is installed on the site
	 *
	 * @since	3.2.16
	 * @access	public
	 */
	public function exists()
	{
		$file = JPATH_ADMINISTRATOR . '/components/com_easysocial/includes/easysocial.php';

		if (!JFile::exists($file)) {
			return false;
		}

		include_once($file);

		// Load the language file
		JFactory::getLanguage()->load('plg_k2_easysocial', JPATH_ADMINISTRATOR);

		return true;
	}

	/**
	 * Loads required assets
	 *
	 * @since	3.2.16
	 * @access	public
	 */
	public function loadAssets()
	{
		if (!$this->exists()) {
			return false;
		}

		$doc = JFactory::getDocument();

		if ($doc->getType() != 'html') {
			return;
		}

		static $loaded = null;

		if (is_null($loaded)) {
			$page = ES::document();
			$page->init();
			$page->processScripts();

			$file = rtrim(JURI::root(), '/') . '/plugins/k2/easysocial/assets/style.css';
			$doc->addStylesheet($file);

			$loaded = true;
		}

		return $loaded;
	}

	/**
	 * Responsible to output the html codes in the user info area of K2
	 *
	 * @since	3.2.16
	 * @access	public
	 */
	public function onK2UserDisplay(&$profile, &$params, $limitstart)
	{
		if (!$this->exists()) {
			return;
		}

		// Load our assets
		$this->loadAssets();

		// Get the correct user id
		$id = $this->getUserId($profile->id);
		$author = ES::user($id);
		$my = ES::user();

		// Get the params
		$pluginParams = $this->getPluginParams();

		// Initialize default data
		$badges = array();
		$messaging = false;
		$friends = false;
		$follow = false;
		$points = false;

		// Get Badges
		if ($pluginParams->get('display_badges', true)) {
			$badges	= $author->getBadges();
		}

		// Determines if we should display the messaging link
		if ($pluginParams->get('display_pm', true) && $my->id != $author->id) {
			$messaging 	= true;
		}

		// Determines if we should display the points
		if ($pluginParams->get('display_points', true)) {
			$points = true;
		}

		// Determines if we should display the add friend
		if ($pluginParams->get('display_friend', true) && $my->id != $author->id && !$author->isFriends($my->id)) {
			$friends = true;
		}

		// Determines if we should display the follow
		if ($pluginParams->get('display_follow', true) && $my->id != $author->id && !$author->isFollowed($my->id)) {
			$follow = true;
		}

		ob_start();
		require_once(__DIR__ . '/tmpl/author.block.php');
		$contents = ob_get_contents();
		ob_end_clean();

		return $contents;
	}

	/**
	 * Check the user session for the award points.
	 *
	 * @since	1.2
	 * @access	public
	 */
	public function sessionExists()
	{
		// Get the IP address from the current user
		$ip	= $_SERVER['REMOTE_ADDR'];

		// Check the K2 item view
		$this->app = JFactory::getApplication();
		$view = $this->app->input->get('view');

		// Get the current k2 item id
		$itemId = $this->app->input->get('id', 0, 'int');

		if (!empty($ip) && !empty($itemId) && $view == 'item') {

			$token = md5($ip . $itemId);
			$session = JFactory::getSession();
			$exists	= $session->get($token , false);

			// If the session existed return true
			if ($exists) {
				return true;
			}

			// Set the token so that the next time the same visitor visits the page, it wouldn't get executed again.
			$session->set($token , 1);
		}

		return false;
	}

	/**
	 * Assigns point when an article is being viewed
	 *
	 * @since	1.2
	 * @access	public
	 */
	public function onK2PrepareContent(&$item, &$params)
	{
		if ($item instanceof TableK2Category) {
			return;
		}

		// this trigger was triggered from module, so we will ignore the processing.
		if ($params->get('parsedInModule', false)) {
			return;
		}

		if (empty($item->created_by)) {
			return;
		}

		// Get the plugin params
		$pluginParams = $this->getPluginParams();

		// Get the author object
		$author = $item->created_by;
		$author	= ES::user($author);

		if (!isset($item->author) || !$item->author) {
			$item->author = new stdClass();
		}

		// Replace name if necessary
		if ($pluginParams->get('modify_author_name', true)) {
			$item->author->name = $author->getName();
		}

		// Replace avatar if necessary
		if ($pluginParams->get('modify_author_avatar', true)) {
			$item->author->avatar = $author->getAvatar(SOCIAL_AVATAR_MEDIUM);
		}

		if ($pluginParams->get('modify_author_link', true)) {
			$item->author->link = $author->getPermalink();
		}

		// Only assign points when the user is not the author.
		if ($this->my->id != $author->id && JRequest::getVar('view') == 'item' && !$this->sessionExists()) {

			// @points: com_k2.read.article
			// Assign points to the reader
			if ($pluginParams->get('points_read', true)) {
				$this->assignPoints('read.article', $this->my->id);
			}

			// @badges: com_k2.read.article
			// Assign badge point to the reader
			if ($pluginParams->get('badges_read', true)) {
				$this->assignBadge('read.article', JText::_('PLG_K2_EASYSOCIAL_READ_ARTICLE'), $this->my->id);
			}

			// @points: com_k2.read.article
			// Assign points to the author
			if ($pluginParams->get('points_read_author', true)) {
				$this->assignPoints('author.read.article', $author->id);
			}

			$appParams = $this->getAppParams('k2', SOCIAL_TYPE_USER);

			// @stream: create
			// Create stream if necessary
			// Only create stream if the user is not a guest
			if ($appParams->get('stream_read', false) && $this->my->id) {
				$this->createStream($item, 'read', $this->my->id);
			}
		}
	}

	/**
	 * Assign points to author when they create a new article in K2
	 *
	 * @since	1.2
	 * @access	public
	 */
	public function onAfterK2Save(&$item, $isNew)
	{
		$params = $this->getPluginParams();
		$appParams = $this->getAppParams('k2', SOCIAL_TYPE_USER);

		if ($isNew) {
			// @badges: com_k2.create.article
			// Assign badge point to the author for creating article
			if ($params->get('badges_create', true)) {
				$this->assignBadge('create.article', JText::_('PLG_K2_EASYSOCIAL_CREATE_ARTICLE'), $this->my->id);
			}

			// @points: com_k2.create.article
			// Assign points to the author for creating article
			if ($params->get('points_create', true)) {
				$this->assignPoints('create.article', $item->created_by);
			}

			// @stream: create
			// Create stream if necessary
			if ($appParams->get('stream_create', true) && $item->published == "1") {
				$this->createStream($item, 'create', $item->created_by);
			}

		} else {

			// @badges: com_k2.update.article
			// Assign badge point to the author for updating article
			if ($params->get('badges_update', true)) {
				$this->assignBadge('update.article', JText::_('PLG_K2_EASYSOCIAL_UPDATE_ARTICLE'), $this->my->id);
			}

			// @stream: update
			// Create stream if necessary
			if ($appParams->get('stream_update', true) && $item->published == "1") {
				$this->createStream($item, 'update', $item->created_by);
			}
		}
	}

	/**
	 * Assign points
	 *
	 * @since	2.0
	 * @access	public
	 */
	private function assignPoints($command, $userId = null)
	{
		if (is_null($userId)) {
			$userId = ES::user()->id;
		}

		return ES::points()->assign($command, 'com_k2', $userId);
	}

	/**
	 * Assign badges
	 *
	 * @since	2.0
	 * @access	public
	 */
	private function assignBadge($rule, $message, $creatorId = null)
	{
		$creator = ES::user($creatorId);

		$state = ES::badges()->log('com_k2', $rule, $creator->id, $message);

		return $state;
	}

	/**
	 * Perform cleanup when a k2 article is being deleted
	 *
	 * @since	3.2.16
	 * @access	public
	 */
	public function onContentAfterDelete($context, $data)
	{
		if ($context != 'com_k2.item') {
			return;
		}

		$stream = ES::stream();
		$stream->delete($data->id, 'k2');
	}
}
