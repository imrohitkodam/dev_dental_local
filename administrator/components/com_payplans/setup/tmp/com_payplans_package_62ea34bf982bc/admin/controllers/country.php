<?php
/**
* @package		PayPlans
* @copyright	Copyright (C) 2010 - 2020 Stack Ideas Sdn Bhd. All rights reserved.
* @license		GNU/GPL, see LICENSE.php
* PayPlans is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/
defined('_JEXEC') or die('Unauthorized Access');

class PayplansControllerCountry extends PayPlansController
{
	public function __construct()
	{
		parent::__construct();

		$this->checkAccess('config');
		
		$this->registerTask('publish', 'togglePublish');
		$this->registerTask('unpublish', 'togglePublish');
	}

	/**
	 * Toggles the default state for a country
	 *
	 * @since	4.1.2
	 * @access	public
	 */
	public function featured()
	{ 
		$ids = $this->input->get('cid', array(), 'array');

		if (!$ids) {
			$message = JText::_('COM_PP_INVALID_IDS');
			$this->info->set($message, 'danger');
			return $this->redirectToView('config', 'countries');
		}

		// There can only be one country as default at any given point of time.
		$id = (int) $ids[0];

		$model = PP::model('Country');
		$model->setDefault($id);

		$country = PP::table('Country');
		$country->load($id);

		$actionlog = PP::actionlog();
		$actionlog->log('COM_PP_ACTIONLOGS_COUNTRY_DEFAULT', 'country', array(
				'countryTitle' => $country->title
		));

		$msg = JText::sprintf('COM_PP_COUNTRY_FEATURE_SUCCESSFULLY', $country->title);
		
		$this->info->set($msg, 'success');
		return $this->redirectToView('config', 'countries');
	}

	/** 
	* Method to unfeature country
	*
	* @since 4.1.6
	* @access public
	*/
	public function unfeatured()
	{
		$ids = $this->input->get('cid', array(), 'array');

		if (!$ids) {
			$message = JText::_('COM_PP_INVALID_IDS');
			$this->info->set($message, 'danger');
			return $this->redirectToView('config', 'countries');
		}

		// There can only be one country as default at any given point of time.
		$id = (int) $ids[0];

		$model = PP::model('Country');
		$model->resetDefault($id);

		$country = PP::table('Country');
		$country->load($id);

		$actionlog = PP::actionlog();
		$actionlog->log('COM_PP_ACTIONLOGS_COUNTRY_DEFAULT_RESET', 'country', array(
				'countryTitle' => $country->title
		));

		$msg = JText::sprintf('COM_PP_COUNTRY_UNFEATURE_SUCCESSFULLY', $country->title);
		
		$this->info->set($msg, 'success');
		return $this->redirectToView('config', 'countries');
	}


	/**
	 * Method to publish / unpublish
	 *
	 * @since	4.0.0
	 * @access	public
	 */
	public function togglePublish()
	{
		$ids = $this->input->get('cid', array(), 'array');

		if (!$ids) {
			$message = JText::_('COM_PP_INVALID_IDS');
			$this->info->set($message, 'danger');
			return $this->redirectToView('config', 'countries');
		}

		$task = $this->getTask();

		$state = $task == 'publish' ? 1 : 0;

		$actionlog = PP::actionlog();
		$actionString = $task == 'publish' ? 'COM_PP_ACTIONLOGS_COUNTRY_PUBLISHED' : 'COM_PP_ACTIONLOGS_COUNTRY_UNPUBLISHED';

		foreach ($ids as $id) {
			$id = (int) $id;

			$country = PP::table('Country');
			$country->load($id);

			$country->published = $state;
			$country->store();

			$actionlog->log($actionString, 'country', array(
					'countryTitle' => $country->title
			));
		}

		$msg = JText::_('COM_PP_COUNTRY_PUBLISHED_SUCCESSFULLY');

		if ($task != 'publish') {
			$msg = JText::_('COM_PP_COUNTRY_UNPUBLISHED_SUCCESSFULLY');
		}

		$this->info->set($msg, 'success');
		return $this->redirectToView('config', 'countries');
	}
}
