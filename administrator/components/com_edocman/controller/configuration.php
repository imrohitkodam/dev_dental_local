<?php
/**
 * @version        1.9.7
 * @package        Joomla
 * @subpackage     Edocman
 * @author         Dang Thuc Dam
 * @copyright      Copyright (C) 2011 - 2018 Ossolution Team
 * @license        GNU/GPL, see LICENSE.php
 */

// No direct access
defined('_JEXEC') or die();

class EDocmanControllerConfiguration extends EDocmanController
{

	function __construct(OSInput $input = null, $config = array())
	{
		parent::__construct($input, $config);
	}

	/**
	 * Save configuration data
	 */
	public function save()
	{
		$data  = $this->input->getData();
		$model = $this->getModel('Configuration');
		$model->store($data);
        if (isset($data['custom_css']))
        {
            JFile::write(JPATH_ROOT . '/components/com_edocman/assets/css/custom.css', trim($data['custom_css']));
        }

		if($this->task == "apply"){
			$this->setRedirect('index.php?option=com_edocman&view=configuration', JText::_('EDOCMAN_CONFIGURATION_SAVED'));
		}else{
			$this->setRedirect('index.php?option=com_edocman', JText::_('EDOCMAN_CONFIGURATION_SAVED'));
		}
	}

	/**
	 * Redirect to default view of the component
	 */
	public function cancel()
	{
		$this->setRedirect('index.php?option=com_edocman&view=' . $this->defaultView);
	}
}