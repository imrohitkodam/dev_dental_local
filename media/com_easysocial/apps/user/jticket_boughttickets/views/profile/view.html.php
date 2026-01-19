<?php
/**
 * @version    SVN: <svn_id>
 * @package    JTicketing
 * @author     Techjoomla <extensions@techjoomla.com>
 * @copyright  Copyright (c) 2009-2015 TechJoomla. All rights reserved.
 * @license    GNU General Public License version 2 or later.
 */
defined('_JEXEC') or die('Unauthorized Access');
use Joomla\CMS\Factory;

/**
 * Profile view for JTicketing app to view tickets bought by that user
 *
 * @since  1.0
 * @access  public
 */
class  Jticket_BoughtTicketsViewProfile extends SocialAppsView
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
		$lang = Factory::getLanguage();
		$lang->load('plg_app_user_jticket_boughttickets', JPATH_ADMINISTRATOR);

		// Get the user params
		$params = $this->getUserParams($userId);

		// Get the app params
		$appParams = $this->app->getParams();

		// Load all required helpers.
		$jticketingmainhelperPath = JPATH_ROOT . '/components/com_jticketing/helpers/main.php';

		if (!class_exists('jticketingmainhelper'))
		{
			JLoader::register('jticketingmainhelper', $jticketingmainhelperPath);
			JLoader::load('jticketingmainhelper');
		}

		$db = Factory::getDbo();
		$user = Factory::getUser();
		$jticketingmainhelper = new jticketingmainhelper;
		$where = '';
		$target_data = '';
		$no_authorize = '';

		if ($user->id != $userId )
		{
			$no_authorize = 'no';
		}
		else
		{
			// Get the user params
			$params = $this->getUserParams($userId);
			$app  = Factory::getApplication();

			// Get the app params
			$appParams = $this->app->getParams();

			// Get the blog model
			$total = (int) $params->get('total', $appParams->get('total', 5));

			$query = $jticketingmainhelper->getMyticketDataSite($where);

			$query .= " AND a.user_id=" . $user->id;

			if (!empty($limit))
			{
				$query .= ' limit ' . $limit;
			}

			$db->setQuery($query);
			$target_data = $db->loadObjectlist();
		}

		$no_of_porducts = $params->get('total', '10');
		$my = Foundry::user();
		$logged_id = $my->id;
		$this->set('target_data', $target_data);
		$this->set('no_authorize', $no_authorize);
		$this->set('userId', $userId);

		echo parent::display('profile/default');
	}
}
