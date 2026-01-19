<?php
/**
 * @version        1.9.10
 * @package        Joomla
 * @subpackage     EDocman
 * @author         Tuan Pham Ngoc
 * @copyright      Copyright (C) 2011 - 2018 Ossolution Team
 * @license        GNU/GPL, see LICENSE.php
 */

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die;

class EDocmanModelCategory extends EDocmanModelList
{
	/**
	 * Instantiate the model.
	 *
	 * @param array $config configuration data for the model
	 *
	 */
	public function __construct($config = array())
	{
		parent::__construct($config);

		$this->state->insert('id', 'int', 0)
			->insert('filter_category_ids', 'string', '')
			->insert('filter_categories_ids','string','');
	}
}