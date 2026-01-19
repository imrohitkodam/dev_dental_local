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

PP::import('admin:/includes/model');

class PayplansModelCountry extends PayPlansModel
{
	public function __construct()
	{
		parent::__construct('country');
	}

	/**
	 * Sets a default country
	 *
	 * @since	4.1.2
	 * @access	public
	 */
	public function getDefaultCountry()
	{
		$db = $this->db;

		// Update the current country
		$query = array(
			'SELECT * FROM `#__payplans_country`',
			'WHERE `default`=' . $db->Quote(1)
		);

		$db->setQuery($query);
		$object = $db->loadObject();
		
		return $object;
	}

	/**
	 * Sets a default country
	 *
	 * @since	4.1.2
	 * @access	public
	 */
	public function setDefault($countryId)
	{
		$db = $this->db;
			
		// Reset all countries
		$query = array('UPDATE `#__payplans_country` SET `default` = 0');
		$db->setQuery($query);
		$db->Query();

		// Update the current country
		$query = array(
			'UPDATE `#__payplans_country` SET `default` = 1',
			'WHERE `country_id`=' . $db->Quote($countryId)
		);
		$db->setQuery($query);
		return $db->Query();
	}

	/** 
	* Unfeature country
	* 
	* @since 4.1.6
	* @access public 
	*/
	public function resetDefault($countryId) 
	{
		$db = $this->db;

		// Update the current country
		$query = array(
			'UPDATE `#__payplans_country` SET `default` = 0',
			'WHERE `country_id`=' . $db->Quote($countryId)
		);

		$db->setQuery($query);
		return $db->Query();
	} 


	/**
	 * Retrieve list of countries from db
	 *
	 * @since	4.1.0
	 * @access	public
	 */
	public function getItems()
	{
		$search = $this->getState('search');
		$state = $this->getState('published');

		$db = $this->db;

		$wheres = array();
		$query = array();

		$query[] = 'SELECT a.*';
		$query[] = 'FROM ' . $db->qn('#__payplans_country') . ' AS a';

		if ($search) {
			$wheres[] = 'a.' . $db->nameQuote('title') . " like " . $db->Quote('%' . $search . '%');
		}

		if ($state != 'all' && $state != '') {
			$wheres[] = 'a.' . $db->nameQuote('published') . " = " . $db->Quote((int) $state);
		}

		$where = '';

		if (count($wheres) > 0) {
			$where = ' where ';
			$where .= (count($wheres) == 1) ? $wheres[0] : implode(' and ', $wheres);
		}

		$query = implode(' ', $query);
		$query .= $where;

		$this->setTotal($query, true);

		$result	= $this->getData($query);

		return $result;
	}
}

