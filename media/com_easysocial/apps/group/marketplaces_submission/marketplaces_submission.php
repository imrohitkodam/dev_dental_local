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

class SocialGroupAppMarketplaces_submission extends SocialAppItem
{
	/**
	 * Determines if the viewer can access the object for comments / reaction
	 *
	 * @since	4.0
	 * @access	public
	 */
	public function isItemViewable($action, $context, $verb, $uid)
	{
		if ($context != 'marketplaces') {
			return;
		}

		// user marketplaces apps should not even reach here.
		// just return false
		return false;
	}

	/**
	 * Renders the marketplace story form
	 *
	 * @since   4.0
	 * @access  public
	 */
	public function onPrepareStoryPanel($story)
	{
		// Ensure that marketplaces is enabled
		if (!$this->config->get('marketplaces.enabled')) {
			return;
		}

		$params = $this->getParams();

		// Determine if we should attach ourselves here.
		if (!$params->get('story_marketplace', true)) {
			return;
		}

		// If the anywhereId exists, means this came from Anywhere module
		// We need to exclude marketplace form from it.
		if (!is_null($story->anywhereId)) {
			return;
		}

		// Ensure that marketplace in group is allowed
		$group = ES::group($story->cluster);

		// Check if user are allowed to create event.
		if (!$group->canCreateListing() || !$group->canAccessMarketplaces()) {
			return;
		}

		// We only allow listing creation on dashboard, which means if the story target and current logged in user is different, then we don't show this
		// Empty target is also allowed because it means no target.
		if (!empty($story->target) && $story->target != $this->my->id) {
			return;
		}

		if (!$this->getApp()->hasAccess($this->my->profile_id)) {
			return;
		}

		// Create plugin object
		$plugin = $story->createPlugin('marketplace', 'panel');

		// Get the theme class
		$theme = ES::themes();
		$theme->set('title', $plugin->title);

		// Get the available event category
		$categories = ES::model('MarketplaceCategories')->getCreatableCategories(ES::user()->getProfile()->id);

		$theme->set('categories', $categories);

		$plugin->button->html = $theme->output('site/story/marketplaces/button');
		$plugin->content->html = $theme->output('site/story/marketplaces/form');

		$access = $this->my->getAccess();

		$script = ES::get('Script');
		$script->set('maxFileSize', $access->get('photos.uploader.maxsize') . 'M');
		$script->set('allowCondition', $params->get('marketplace_condition', true));
		$script->set('allowStock', $params->get('marketplace_availability', true));
		$plugin->script = $script->output('site/story/marketplaces/plugin');

		return $plugin;
	}

	public function onBeforeStorySave(&$template, &$stream, &$content)
	{
		if ($template->context_type != 'marketplace') {
			return;
		}

		$params = $this->getParams();

		// Determine if we should attach ourselves here.
		if (!$params->get('story_marketplace', true)) {
			return;
		}

		$cluster = ES::cluster($template->cluster_type, $template->cluster_id);

		if (!$cluster->canCreateListing()) {
			return;
		}

		$in = ES::input();
		$title = $in->getString('marketplace_title');
		$description = $in->getString('marketplace_description', '');
		$condition = $in->getInt('marketplace_condition');
		$stock = $in->getInt('marketplace_stock');
		$currency = $in->getString('marketplace_currency');
		$categoryid = $in->getInt('marketplace_category');
		$price = $in->getString('marketplace_price');
		$photos = $in->get('marketplace_photos', array(), 'default');

		// If no category id, then we don't proceed
		if (empty($categoryid)) {
			return;
		}

		$my = ES::user();

		$listing = ES::marketplace();

		$listing->title = $title;
		$listing->description = $description;
		$listing->currency = $currency;
		$listing->type = SOCIAL_TYPE_GROUP;
		$listing->uid = $cluster->id;
		$listing->user_id = $my->id;
		$listing->category_id = $categoryid;
		$listing->price = $price;
		$listing->created = ES::date()->toSql();
		$listing->stock = $params->get('marketplace_availability', true) ? $stock : 0;
		$listing->condition = $params->get('marketplace_condition', true) ? $condition : 0;

		$listing->state = SOCIAL_MARKETPLACE_PENDING;

		if ($my->isSiteAdmin() || !$my->getAccess()->get('marketplaces.moderate')) {
			$listing->state = SOCIAL_STATE_PUBLISHED;
		}

		// Trigger apps
		ES::apps()->load(SOCIAL_TYPE_USER);

		$dispatcher = ES::dispatcher();
		$triggerArgs = [&$listing, &$my, true];

		// @trigger: onMarketplaceBeforeSave
		$dispatcher->trigger(SOCIAL_TYPE_USER, 'onMarketplaceBeforeSave', $triggerArgs);

		$saveOptions = [
			'isFromStory' => true
		];

		$state = $listing->save($saveOptions);

		// Save the photos only after the listing is saved
		if ($photos) {
			$listing->savePhotos($photos);
		}

		if ($state) {
			ES::points()->assign('marketplace.add', 'com_easysocial', $listing->getAuthor()->id);

			// after store the photo then only indexing this marketplace item
			$listing->syncIndex();
		}

		// Notifies admin when a new listing is created
		if ($listing->state === SOCIAL_MARKETPLACE_PENDING || !$my->isSiteAdmin()) {
			ES::model('Marketplaces')->notifyAdmins($listing);
		}

		// @trigger: onMarketplaceAfterSave
		$triggerArgs = array(&$listing, &$my, true);
		$dispatcher->trigger(SOCIAL_TYPE_USER, 'onMarketplaceAfterSave' , $triggerArgs);

		$template->context_type = 'marketplaces';
		$template->context_id = $listing->id;

		$params = array(
			'listing' => $listing
		);

		$template->setParams(ES::json()->encode($params));
	}

}

