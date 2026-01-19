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

class PPHelperEuvat extends PPHelperStandardApp
{
	/**
	 * Determines if app is really enabled
	 *
	 * @since	4.0.0
	 * @access	public
	 */
	public function isEnabled()
	{
		if (!$this->app->getId()) {
			return false;
		}

		if (!$this->app->published) {
			return false;
		}

		return true;
	}

	public function getPurposeOptions()
	{
		$options = [
			PP_EUVAT_PURPOSE_NONE => JText::_('COM_PP_APP_EUVAT_USE_PURPOSE_SELECT'),
			PP_EUVAT_PURPOSE_PERSONAL => JText::_('COM_PP_APP_EUVAT_USE_PURPOSE_PERSONAL'),
			PP_EUVAT_PURPOSE_BUSINESS => JText::_('COM_PP_APP_EUVAT_USE_PURPOSE_BUSINESS'),
			PP_EUVAT_PURPOSE_BUSINESS_NO_VAT => JText::_('COM_PP_APP_EUVAT_USE_PURPOSE_BUSINESS_NO_VAT')
		];

		$purposeOptions = [];

		foreach ($options as $val => $title) {
			$option = new stdClass();
			$option->value = $val;
			$option->title = $title;

			$purposeOptions[] = $option;
		}

		return $purposeOptions;
	}

	/**
	 * Process tax inclusion
	 *
	 * @since	4.0.0
	 * @access	public
	 */
	public function doTaxRequest($invoiceKey = false, $country = '', $purpose = 0, $businessVat = '', $businessName = '')
	{
		//should allow user to proceed
		$proceed = false;

		$invoiceId = PP::getIdFromKey($invoiceKey);
		$invoice = PP::Invoice($invoiceId);
		$businessVatNo = $businessVat;

		// check if vat number start with the country code
		$isoCode2 = substr($businessVat, 0, 2);
		if (preg_match("/[a-z]/i", $isoCode2)) {
			$countryId = PP::getCountryIdByIso($isoCode2);

			// If country code exist in vat number then remove country isco code
			if ($countryId && $countryId === $country) {
				$businessVatNo = substr($businessVat, 2);
			}
		}

		$args  = [$invoice, $country, &$purpose, $businessVatNo];
		$results = PPEvent::trigger('onPayplansApplyTax', $args, '', $invoice);

		$error = '';
		$success = '';
		$taxes = [];

		// TODO:
		foreach ($results as $result => $item) {
			if (!is_null($item)) {
				$taxes[] = $item;

				//check if there is any warning message or not.
				if (isset($item->errors) && $item->errors) {
					$error = $item->errors;
				}

				// Check if there is any success message or not
				if (isset($item->success) && $item->success) {
					$success = $item->success;
				}
			}
		}

		// Delete existing euvat tax
		$model = PP::model('modifier');
		$model->deleteTypeModifiers($invoiceId, 'eu-vat');

		if ($taxes) {

			$proceed = true;
			foreach ($taxes as $ind => $tax) {

				$ref = $country . '-' . $businessVat;

				$modifierParams = new stdClass();
				$modifierParams->type = 'eu-vat';
				$modifierParams->percentage	= true;
				$modifierParams->serial = PP_MODIFIER_PERCENT_TAX;
				$modifierParams->amount = $tax->rate;
				$modifierParams->message = $tax->title;
				$modifierParams->reference = $ref;
				$modifierParams->frequency = PP_MODIFIER_FREQUENCY_EACH_TIME;

				$invoice->addModifier($modifierParams);
			}
		}

		// update user preference and country
		$user = $invoice->getBuyer(PP_INSTANCE_REQUIRE);

		if ($user->getUserName() != 'Not_Registered') {
			$userPref = $user->getPreferences();
			$userPref->set('business_purpose', $purpose);
			$userPref->set('business_name', $businessName);
			$userPref->set('tin', $businessVat);
			$user->setPreferences($userPref);

			$user->setCountry($country);
			$user->save();
		}
			
		// update invoice
		$invoice->refresh();
		$invoice->save();

		return [$invoice, $error, $proceed, $success];
	}

	/**
	 * Validate VAT
	 *
	 * @since	4.0.0
	 * @access	public
	 */
	public function validateVAT($country, $vatNumber)
	{
		// for vat number sample, please see:
		// https://www.braemoor.co.uk/software/vattestx.php

		$model = PP::model('Country');
		$countries = $model->loadRecords();

		$sCountry = $countries[$country];
		$sCountryCode = $sCountry->isocode2;

		// Construct the REST query
		if ($sCountryCode == 'GR') {
			// Greece is a special case; the ISO code is GR, the VAT country code is EL.
			$sCountryCode = 'EL';
		}

		try {

			$result = $this->soapValidation($sCountryCode, $vatNumber);

			if ($result === false) {
				// server disabled soap lib. lets try with file_get_content method.
				$result = $this->standardValidation($sCountryCode, $vatNumber);
			}

			//Set error if both extensions was not loaded
			if (!$result) {
				// Invalid VAT or VAT validation service is down
				return JText::_('COM_PP_EUVAT_VAT_VALIDATION_NO_RESPONSE');
			}

			// check property 'valid' which will available in response
			if (!isset($result->valid) || !$result->valid) {
				// Invalid VAT or VAT validation service is down
				return JText::_('COM_PP_EUVAT_VAT_VALIDATION_FAILED');
			}

		} catch (Exception $e) {
			return JText::_('COM_PP_EUVAT_VAT_VALIDATION_FAILED');
		}

		return true;
	}

	/**
	 * Soap method to validate vat number
	 * It return an object of stdclass have properties :
	 *  class stdClass (6) {
	 *			  public $countryCode => string(2) "COUNTRY_CODE"
	 *			  public $vatNumber   => string(9) "VAT_NUMBER"
	 *			  public $requestDate => string(16) "DATE REQUESTED"
	 *			  public $valid       => bool(true/false)
	 *			  public $name        => string(3) "---"
	 *			  public $address     => string(3) "---"
	 *			}
	 *
	 * @since	4.0.0
	 * @access	public
	 */
	protected function soapValidation($countryCode, $vatNumber)
	{
		if (extension_loaded('soap')) {

			// $countryCode = 'AT';
			// $vatNumber = 'U66664013';

			$option = [
				'countryCode' => $countryCode, 
				'vatNumber' => $vatNumber
			];

			$client = new SoapClient("http://ec.europa.eu/taxation_customs/vies/checkVatService.wsdl", ['trace' => true, 'cache_wsdl' => WSDL_CACHE_MEMORY]);

			$result = $client->checkVat([
				'countryCode' => $countryCode,
				'vatNumber'   => $vatNumber
			]);
			
			return $result;
		}

		return false;
	}


	/**
	 * file_get_content method to validate vat number
	 * @since	4.0.0
	 * @access	public
	 */
	protected function standardValidation($countryCode, $vatNumber)
	{
		if (function_exists('file_get_contents')) {

			$timeout = 15;
			$url = "http://ec.europa.eu/taxation_customs/vies/services/checkVatService";

			$response = [];
			$pattern = '/<(%s).*?>([\s\S]*)<\/\1/';
			$keys = [
				'countryCode',
				'vatNumber',
				'requestDate',
				'valid'
			];

			$content = "<s11:Envelope xmlns:s11='http://schemas.xmlsoap.org/soap/envelope/'>
						  <s11:Body>
							<tns1:checkVat xmlns:tns1='urn:ec.europa.eu:taxud:vies:services:checkVat:types'>
							  <tns1:countryCode>%s</tns1:countryCode>
							  <tns1:vatNumber>%s</tns1:vatNumber>
							</tns1:checkVat>
						  </s11:Body>
						</s11:Envelope>";

			$opts = [
				'http' => [
					'method' => 'POST',
					'header' => "Content-Type: text/xml; charset=utf-8; SOAPAction: checkVatService",
					'content' => sprintf($content, $countryCode, $vatNumber),
					'timeout' => $timeout
				]
			];

			$ctx = stream_context_create($opts);
			$result = file_get_contents($url, false, $ctx);

			if (preg_match(sprintf($pattern, 'checkVatResponse'), $result, $matches)) {
				foreach ($keys as $key) {
					preg_match(sprintf($pattern, $key), $matches[2], $value) && $response[$key] = $value[2];
				}
			}

			if ($response && isset($response['valid']) && $response['valid']) {
				return true;
			}

		}

		return false;
	}

	/**
	 * Curl method to validate vat number
	 * It returns json like :
	 *      {
	 *		  "response": {
	 *		    "country_code": "COUNTRY_CODE",
	 *		    "vat_number": "VAT_NUMBER",
	 *		    "valid": "false/true",
	 *		    "name": "---",
	 *		    "address": "---"
	 *		  }
	 *	   }
	 * @since	4.0.0
	 * @access	public
	 */
	protected function curlValidation($countryCode, $vatNumber)
	{
		if (extension_loaded('curl')) {

			$url = 'http://vatid.eu/check/' . $countryCode . '/' . $vatNumber;

			$curl = new JHttpTransportCurl(new JRegistry());
			$result = $curl->request('GET', new JURI($url), null, ['Accept' => 'application/json'], 30);

			$output = json_decode($result->body)->response;
			//true and false is in string, so convert it to boolean
			$output->valid = ($output->valid === 'true') ? true : false;

			return $output;
		}

		return false;
	}
}
