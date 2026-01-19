<?php
/**
 * @version        1.7.6
 * @package        Joomla
 * @subpackage     EDocman
 * @author         Tuan Pham Ngoc
 * @copyright      Copyright (C) 2011 - 2018 Ossolution Team
 * @license        GNU/GPL, see LICENSE.php
 */

defined('_JEXEC') or die;

class EDocmanModelSearch extends EDocmanModelList
{
	/**
	 * Instantiate the model.
	 *
	 * @param array $config configuration data for the model
	 *
	 */

	public function __construct($config = array())
	{
        $config['remember_states'] = false;

		parent::__construct($config);

		$this->state->insert('filter_category_id', 'int', 0)
                    ->insert('filter_search', 'string', '')
                    ->insert('filter_tag', 'string', '')
					->insert('show_category','int',0)
					->insert('filter_filetype','string','');
	}
}