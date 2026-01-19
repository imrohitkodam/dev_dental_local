<?php
/**
* @package      EasySocial
* @copyright    Copyright (C) 2010 - 2016 Stack Ideas Sdn Bhd. All rights reserved.
* @license      GNU/GPL, see LICENSE.php
* EasySocial is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/
defined('_JEXEC') or die('Unauthorized Access');

class ReviewsViewProfile extends SocialAppsView
{
	/**
	 * Displays the application output in the canvas.
	 *
	 * @since	2.0
	 * @access	public
	 */
	public function display($userId = null, $docType = null)
	{
		// Load up the user
		$user = ES::user($userId);

		$this->setTitle('APP_GROUP_REVIEWS_TITLE_REVIEWS');

		$params = $this->app->getParams();

		// Set the max length of the item
		$options = array('limit' => (int) $params->get('total', 10));

		// Get a list of reviews
		$model = ES::model('Reviews');
		$items = $model->getReviews($user->id, SOCIAL_TYPE_USER, $options);

		$pagination = $model->getPagination();

		$totalReviewOptions = array('pending' => true);

		if (!$this->my->isSiteAdmin()) {
			$totalReviewOptions['userId'] = $this->my->id;
		}

		// Format the item's content.
		$this->format($items, $params);

		$pagination->setVar('option', 'com_easysocial');
		$pagination->setVar('view', 'profile');
		$pagination->setVar('id', $user->getAlias());
		$pagination->setVar('appId', $this->app->getAlias());

		$this->set('params', $params);
		$this->set('pagination', $pagination);
		$this->set('cluster', $user);
		$this->set('items', $items);
		$this->set('totalReviewOptions', $totalReviewOptions);
		$this->set('app', $this->app);
		$this->set('isAdmin', $this->my->isSiteAdmin());

		echo parent::display('themes:/site/reviews/default/default');
	}

	/**
	 * Render sidebar into sidebar module.
	 *
	 * @since	3.0
	 * @access	public
	 */
	public function sidebar($moduleLib, $user)
	{
		$totalReviewOptions = array('pending' => true);

		if (!$this->my->isSiteAdmin()) {
			$totalReviewOptions['userId'] = $this->my->id;
		}

		$this->set('allowSelfReview', false);
		$this->set('moduleLib', $moduleLib);
		$this->set('cluster', $user);
		$this->set('app', $this->app);
		$this->set('totalReviewOptions', $totalReviewOptions);
		$this->set('isAdmin', $this->my->isSiteAdmin());

		echo parent::display('themes:/site/reviews/default/sidebar');
	}


	private function format(&$items, $params)
	{
		$length = $params->get('content_length', 350);

		if ($length == 0) {
			return;
		}

		foreach ($items as &$item) {
			$item->message = ESJString::substr(strip_tags($item->message), 0, $length) . ' ' . JText::_('COM_EASYSOCIAL_ELLIPSES');
		}
	}
}
