<?php
/**
* @package		PayPlans
* @copyright	Copyright (C) Stack Ideas Sdn Bhd. All rights reserved.
* @license		GNU/GPL, see LICENSE.php
* PayPlans is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/
defined('_JEXEC') or die('Unauthorized Access');

require_once(__DIR__ . '/controller.php');

class PayplansControllerSampledataInstall extends PayplansSetupController
{
	public function execute()
	{
		$this->engine();

		$sampleData = $this->input->get('sampledata', '');
		if (!$sampleData) {
			return true;
		}

		// For development mode, we want to skip all this
		if ($this->isDevelopment()) {
			return $this->output($this->getResultObj('COM_PP_INSTALLATION_DEVELOPER_MODE', true));
		}

		// Create Sample Plans
		$this->createSamplePlans();

		// create sample payment method
		$this->createSampleApps();

		$this->setInfo(JText::_('Sample Data installed on the site'));
		$result = $this->getResultObj(JText::_('Sample Data installed on the site'), true);
		
		return $this->output($result);
	}

	/**
	 * Create necessary core apps during installation if it doesn't exist yet
	 *
	 * @since	4.0.0
	 * @access	public
	 */
	public function createSampleApps()
	{
		$this->engine();

		$db = PP::db();

		// check for payment methos app record
		$query = 'SELECT COUNT(1) FROM ' . $db->quoteName('#__payplans_app') . 'WHERE `group` = "payment" AND `type` != "offlinepay"';
		$db->setQuery($query);
		$total = $db->loadResult();

		// if more one payment method created then do nothing
		if ($total > 1) {
			return true;
		}


		// Create stripe payment if it doesn't exists yet
		$query = array();
		$query[] = 'SELECT COUNT(1) FROM ' . $db->qn('#__payplans_app') . ' WHERE `type` = "stripe"';
		$db->setQuery($query);
		$exists = $db->loadResult() > 0;

		if (!$exists) {

			$coreParams = '{"applyAll":"1"}';
			$appParams = '{"form_type":"form","popup_store_name":"","date_type":"MM \/ YY","enable_sca":"0","public_key":"pk_test_TYfC1wh50eAdyaVUpIrurjGg","secret_key":"sk_test_rrD1xDk9Adt3t371yNgpBEYa","allow_recurring_cancel":"1","auto_fill_email":"1","sandbox":"1"}';

			$table = PP::table('App');
			$table->group = 'payment';
			$table->title = 'Stripe Payment';
			$table->type = 'stripe';
			$table->description = 'This payment method is used for the stripe payment method.';
			$table->published = 1;
			$table->core_params = $coreParams;
			$table->app_params = $appParams;
			$table->ordering = 2;
			$table->store();
		}
	}

	/**
	 * Creates sample plans during the installation of PayPlans
	 *
	 * @since	4.2.0
	 * @access	public
	 */
	public function createSamplePlans()
	{
		$this->engine();

		// Check if there are any plans created in PayPlans yet.
		/*$db = PP::db();

		$query = 'SELECT COUNT(1) FROM ' . $db->quoteName('#__payplans_plan');
		$db->setQuery($query);
		$total = $db->loadResult();

		if ($total > 0) {
			return false;
		}*/

		$lipsum = 'Lorem Ipsum is simply dummy text of the printing and typesetting industry. Lorem Ipsum has been the industrys standard dummy text ever since the 1500s, when an unknown printer took a galley of type and scrambled it to make a type specimen book. It has survived not only five centuries, but also the leap into electronic typesetting, remaining essentially unchanged. It was popularised in the 1960s with the release of Letraset sheets containing Lorem Ipsum passages, and more recently with desktop publishing software like Aldus PageMaker including versions of Lorem Ipsum.';
		$plans = [
			[
				'title' => 'Sample Fixed Plan',
				'published' => true,
				'visible' => true,
				'checked_out' => NULL,
				'description' => $lipsum,
				'ordering' => 1,
				'details' => '{"expirationtype":"fixed","expiration":"000100000000","recurrence_count":"1","price":"1.00","trial_price_1":"0.00","trial_time_1":"000000000000","trial_price_2":"0.00","trial_time_2":"000000000000","currency":"USD"}'
			],
			[
				'title' => 'Sample Recurring Plan',
				'published' => true,
				'visible' => true,
				'checked_out' => NULL,
				'description' => $lipsum,
				'ordering' => 2,
				'details' => '{"expirationtype":"recurring","expiration":"000000000300","recurrence_count":"3","price":"10.00","trial_price_1":"0.00","trial_time_1":"000000000000","trial_price_2":"0.00","trial_time_2":"000000000000","currency":"USD"}'
			],
			[
				'title' => 'Sample Recurring + Trial 1 Plan',
				'published' => true,
				'visible' => true,
				'checked_out' => NULL,
				'description' => $lipsum,
				'ordering' => 3,
				'details' => '{"expirationtype":"recurring_trial_1","expiration":"000000000300","recurrence_count":"0","price":"20.00","trial_price_1":"0.00","trial_time_1":"000000000500","trial_price_2":"0.00","trial_time_2":"000000000000","currency":"USD"}'
			],
			[
				'title' => 'Sample Recurring + Trial 2 Plan',
				'published' => true,
				'visible' => true,
				'checked_out' => NULL,
				'description' => $lipsum,
				'ordering' => 4,
				'details' => '{"expirationtype":"recurring_trial_2","expiration":"000000000300","recurrence_count":"10","price":"10.00","trial_price_1":"20.00","trial_time_1":"000000000300","trial_price_2":"30.00","trial_time_2":"000000000300","currency":"EUR"}'
			],
			[
				'title' => 'Sample Forever Plan',
				'published' => true,
				'visible' => true,
				'checked_out' => NULL,
				'description' => $lipsum,
				'ordering' => 5,
				'details' => '{"expirationtype":"forever","expiration":"000000000000","recurrence_count":"1","price":"10.00","trial_price_1":"0.00","trial_time_1":"000000000000","trial_price_2":"0.00","trial_time_2":"000000000000","currency":"USD"}'
			]
		];

		foreach ($plans as $data) {
			$plan = PP::plan();

			$plan->bind($data);
			
			// Tell the abstract library to not trigger any plugins because at this point of time,
			// the plugins may not be updated yet since the plugins and modules are only installed after this
			$plan->trigger = false;

			$plan->save();
		}

		return true;
	}
}
