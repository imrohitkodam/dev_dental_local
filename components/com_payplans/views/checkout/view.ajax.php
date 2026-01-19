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

	/**
	 * Validate the Password
	 *
	 * @since	4.2
	 * @access	public
	 */
	public function validatePassword()
	{
		$isValid = true;
		$message = "";

		$password = $this->input->get('password', '', 'default');

		// get user params
		$params = JComponentHelper::getParams('com_users');

		// Verify that the passwords are valid and not empty
		if (empty($password)) {
			$message = JText::_('COM_PP_JOOMLA_PASSWORD_EMPTY_PASSWORD');
			$isValid = false;
		}

		$minLengthPassword = $params->get('minimum_length', 0);
		if ($minLengthPassword > 0 && strlen($password) < $minLengthPassword) {
			$isValid = false;
			$message = JText::sprintf('COM_PP_JOOMLA_PASSWORD_MINIMUM_CHAR', $minLengthPassword);
		}

		// Verify minimum symbols
		$minSymbolsPassword = $params->get('minimum_symbols', 0);
		if ($minSymbolsPassword > 0) {

			// Get the total number of symbols used in the password
			$totalSymbols = preg_match_all('[\W]', $password, $matches);

			if ($totalSymbols < $minSymbolsPassword) {
				$isValid = false;
				$message = JText::sprintf('COM_PP_JOOMLA_PASSWORD_MINIMUM_SYMBOLS', $minSymbolsPassword);
			}
		}

		// Verify minimum integers
		$minIntegersPassword = $params->get('minimum_integers', 0);
		if ($minIntegersPassword > 0) {
			$totalIntegers = preg_match_all('/[0-9]/', $password, $matches);

			if ($totalIntegers < $minIntegersPassword) {
				$isValid = false;
				$message = JText::sprintf('COM_PP_JOOMLA_PASSWORD_MINIMUM_INTEGER', $minIntegersPassword);
			}
		}

		// Verify minimum uppercase
		$minUppercasePassword = $params->get('minimum_uppercase', 0);
		if ($minUppercasePassword > 0) {
			$totalUppercase = preg_match_all('/[A-Z]/', $password, $matches);

			if ($totalUppercase < $minUppercasePassword) {
				$isValid = false;
				$message = JText::sprintf('COM_PP_JOOMLA_PASSWORD_MINIMUM_UPPERCASE', $minUppercasePassword);
			}
		}

		// Verify minimum uppercase
		$minLowercasePassword = $params->get('minimum_lowercase', 0);
		if ($minLowercasePassword > 0) {
			$totalLowercase = preg_match_all('/[a-z]/', $password, $matches);

			if ($totalLowercase < $minLowercasePassword) {
				$isValid = false;
				$message = JText::sprintf('COM_PP_JOOMLA_PASSWORD_MINIMUM_LOWERCASE', $minUppercasePassword);
			}
		}

		return $this->resolve($isValid, $message);
	}
}