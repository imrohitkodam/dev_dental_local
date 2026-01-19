<?php
/**
 * @version        1.9.7
 * @package        Joomla
 * @subpackage     Edocman
 * @author         Tuan Pham Ngoc
 * @copyright      Copyright (C) 2011 - 2018 Ossolution Team
 * @license        GNU/GPL, see LICENSE.php
 */
// No direct access.
defined('_JEXEC') or die;

/**
 * Edocman model.
 */
class EDocmanModelDocument extends EDocmanModelCommonDocument
{
	public function __construct(array $config = array())
	{
		parent::__construct($config);
	}

    /**
     * @param $value
     * @param $pks
     * @param $contexts
     * @return bool
     */
    public function batch($value, $pks)
    {
        $table = JTable::getInstance('Document','EDocmanTable');
        // Parent exists so we let's proceed
        while (!empty($pks))
        {
            // Pop the first ID off the stack
            $pk = array_shift($pks);
            // Check that the row actually exists
            if (!$table->load($pk))
            {
                if ($error = $table->getError())
                {
                    // Fatal error
                    $this->setError($error);
                    return false;
                }
                else
                {
                    // Not fatal error
                    $this->setError(JText::sprintf('JLIB_APPLICATION_ERROR_BATCH_MOVE_ROW_NOT_FOUND', $pk));
                    continue;
                }
            }
            if($value['assetgroup_id'] != "") {
                $table->access = $value['assetgroup_id'];
            }
            // Store the row.
            if (!$table->store())
            {
                $this->setError($table->getError());
                return false;
            }
        }
        return true;
    }

	/**
	 * Init the record data object
	 */
	public function initData()
	{
		$this->data = $this->getTable();
		$this->data->license_id = EdocmanHelper::getDefaultLicense();
	}

	/**
	 * Load the record from database
	 */
	public function loadData()
	{
		$db    = $this->getDbo();
		$query = $db->getQuery(true);
		$query->select('*')
			->from($this->table)
			->where('id = ' . (int) $this->state->id);
		$db->setQuery($query);

		$this->data = $db->loadObject();
		if($this->data->license_id == 0){
			$this->data->license_id = EdocmanHelper::getDefaultLicense();
		}
        if($this->data->id > 0) {
            $groups = EDocmanHelper::getGroupLevels(1, $this->data->id);
            if($groups != ''){
                $this->data->groups = explode(",",$groups);
            }
        }
	}
}