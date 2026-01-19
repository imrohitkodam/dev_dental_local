<?php
/**
 * @package       OS
 * @subpackage    ModelAdmin
 * @author        Ossolution Team
 */
defined('_JEXEC') or die();

class OSModelItem extends OSModel
{
	/**
	 * The record data
	 *
	 * @var object
	 */
	protected $data = null;

	/**
	 * Constructor function
	 *
	 * @param array $config
	 */
	public function __construct($config = array())
	{
		parent::__construct($config);

		$this->state->insert('id', 'int', 0);
	}

	/**
	 * Method to get the record data
	 *
	 * @return object
	 */
	public function getData()
	{
		if (empty($this->data))
		{
			if (empty($this->data))
			{
				if ($this->state->id)
				{
					$this->loadData();
				}
				else
				{
					$this->initData();
				}
			}
			//print_r($this->data);die();
			if (property_exists($this->data, 'params'))
			{
				$registry = new \Joomla\Registry\Registry();
                if($this->data->params!="") {
                    $registry->loadString($this->data->params);
                    $this->data->params = $registry->toArray();
                }
			}
		}

		return $this->data;
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
	}

	/**
	 * Init the record data object
	 */
	public function initData()
	{
		$this->data = $this->getTable();
	}
}
