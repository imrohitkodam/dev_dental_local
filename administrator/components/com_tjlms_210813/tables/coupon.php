<?php
/**
 * @version    SVN: <svn_id>
 * @package    Com_Tjlms
 * @copyright  Copyright (C) 2005 - 2014. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE.txt
 * Shika is free software. This version may have been modified pursuant
 * to the GNU General Public License, and as distributed it includes or
 * is derivative of works licensed under the GNU General Public License or
 * other free or open source software licenses.
 */

// No direct access
defined('_JEXEC') or die;

/**
 * coupon Table class
 *
 * @since  1.0.0
 */
class TjlmsTablecoupon extends JTable
{
	/**
	 * Constructor
	 *
	 * @param   JDatabase  &$db  A database connector object
	 */
	public function __construct(&$db)
	{
		parent::__construct('#__tjlms_coupons', 'id', $db);

		// Set the alias since the column is called state
		$this->setColumnAlias('published', 'state');
	}

	/**
	 * Overloaded bind function to pre-process the params.
	 *
	 * @param   array   $array   Named array
	 * @param   string  $ignore  string
	 *
	 * @return   null|string    null is operation was satisfactory, otherwise returns an error
	 *
	 * @since  1.0.0
	 */
	public function bind($array, $ignore = '')
	{
		if (!isset($array['course_id']))
		{
			$array['course_id'] = '';
		}
		elseif (is_array($array['course_id']))
		{
			$array['course_id'] = implode(',', $array['course_id']);
		}

		if (!isset($array['subscription_id']))
		{
			$array['subscription_id'] = '';
		}
		elseif (is_array($array['subscription_id']))
		{
			$array['subscription_id'] = implode(',', $array['subscription_id']);
		}

		return parent::bind($array, $ignore);
	}
}
