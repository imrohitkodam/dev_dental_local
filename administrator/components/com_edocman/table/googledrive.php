<?php
/**
 * @version        1.7.6
 * @package        Joomla
 * @subpackage     Edocman
 * @author         Tuan Pham Ngoc
 * @copyright      Copyright (C) 2011 - 2016 Ossolution Team
 * @license        GNU/GPL, see LICENSE.php
 */

// No direct access
defined('_JEXEC') or die();

/**
 * document Table class
 */
class EDocmanTableGoogledrive extends JTable
{

	/**
	 * Constructor
	 *
	 * @param
	 *            JDatabase A database connector object
	 */
	public function __construct(&$db)
	{
		parent::__construct('#__edocman_googledrive', 'id', $db);
	}
}
