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

class PPPlanPriceVariation
{
	private $id = null;
	private $lib = null;
	private $price = null;

	public function __construct($planPriceVariationString)
	{
		$data = $this->parse($planPriceVariationString);

		$this->lib = PP::app($data->id);
		$this->price = $data->price;
	}

	public function parse($planPriceVariationString)
	{
		$tmp = explode('_', $planPriceVariationString);

		$data = new stdClass();
		$data->id = (int) $tmp[3];
		$data->price = (float) $tmp[1];

		return $data;
	}

	public static function factory($planPriceVariationString)
	{
		return new self($planPriceVariationString);
	}

	public function getPrices()
	{
		static $prices = [];

		$id = $this->lib->getId();

		if (!isset($prices[$id])) {
			$params = $this->lib->getAppParams();
			$data = unserialize($params->get('time_price'));
		
			// Flatten the array
			$prices[$id] = [];

			foreach ($data['title'] as $key => $value) {
				$prices[$id][] = $data['price'][$key];
			}
		}

		return $prices[$id];
	}

	public function hasPrice($price)
	{
		$prices = $this->getPrices();
		
		return  in_array($price, $prices);
	}
}
