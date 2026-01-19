<?php
/**
 * @version    SVN: <svn_id>
 * @package    Quick2cart
 * @author     Techjoomla <extensions@techjoomla.com>
 * @copyright  Copyright (c) 2009-2015 TechJoomla. All rights reserved.
 * @license    GNU General Public License version 2 or later.
 */

// No direct access
defined('_JEXEC') or die();
use Joomla\CMS\Router\Route;
use Joomla\CMS\Factory;
use Joomla\CMS\Uri\Uri;
JLoader::register('JT', JPATH_SITE . "/components/com_jticketing/includes/jticketing.php");

/**
 * Profile view for Q2CProducts app
 *
 * @since  1.0
 * @access  public
 */
class JticketMyEventsViewProfile extends SocialAppsView
{
	/**
	 * Displays the application output in the canvas.
	 *
	 * @param   int     $userId   The user id that is currently being viewed.
	 * @param   string  $docType  document type
	 *
	 * @return  void
	 *
	 * @since   1.0
	 */
	public function display ($userId = null, $docType = null)
	{
		require_once JPATH_SITE . '/components/com_jticketing/helpers/event.php';

		// Get the user params
		$params = $this->getUserParams($userId);
		$app    = Factory::getApplication();

		// Get the app params
		$appParams = $this->app->getParams();

		// Get the blog model
		$total = (int) $params->get('total', $appParams->get('total', 5));
		$pin_width = (int) $appParams->get('pin_width', 45);
		$pin_padding = (int) $appParams->get('pin_padding', 3);
		$category_id = '';

		// Get list of all events created by the user on the site.
		$input           = Factory::getApplication()->input;
		$model           = $this->getModel('JticketMyEvents');
		$events          = $model->getItems($userId, $total, $category_id);
		$eventsCount     = $model->getEventsCount($userId, $category_id);
		$user            = Foundry::user($userId);
		$catList         = JT::model('events')->getEventCategories();
		$this->jt_params = $app->getParams('com_jticketing');
		$itemId          = JT::utilities()->getItemId('index.php?option=com_jticketing&view=events');
		$allevent_link   = Uri::root() . substr(Route::_('index.php?option=com_jticketing&view=events&Itemid=' . $itemId), strlen(Uri::base(true)) + 1);

		// Get integration set
		$integration = $this->jt_params->get('integration', '', 'INT');
		$this->set('integration', $integration);
		$this->set('user', $user);
		$this->set('userId', $userId);
		$this->set('total', $total);
		$this->set('pin_width', $pin_width);
		$this->set('pin_padding', $pin_padding);
		$this->set('events', $events);
		$this->set('limit', $total);
		$this->set('eventsCount', $eventsCount);
		$this->set('categorylists', $catList);
		$this->set('allevent_link', $allevent_link);

		echo parent::display('profile/default');
	}
}
