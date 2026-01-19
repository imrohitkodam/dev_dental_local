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

PP::import('site:/views/views');

class PayPlansViewCheckout extends PayPlansSiteView
{
	/**
	 * Validate the emails
	 *
	 * @since	4.2
	 * @access	public
	 */
	public function validateEmail()
	{
		$email = $this->input->get('email', '', 'email');
		$model = PP::model('User');

		$isValid = $model->validateEmail($email);
		$message = $model->getError();

		return $this->resolve($isValid, $message);
	}

	/**
	 * Validate the username
	 *
	 * @since	4.2
	 * @access	public
	 */
	public function validateUsername()
	{
		$username = $this->input->get('username', '', 'default');
		$model = PP::model('User');

		$isValid = $model->validateUsername($username);
		$message = $model->getError();

		return $this->resolve($isValid, $message);
	}

	public function getCountryFromVatNo()
	{
		$isoCode2 = $this->input->get('isoCode2', '', 'default');
		// Get the country Id from isocode2
		$country = PP::getCountryIdByIso($isoCode2);

		return $this->resolve($country);
		
	}
}