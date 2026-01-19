<?php
/**
 * @version         1.9.7
 * @package        Joomla
 * @subpackage     EDocman
 * @author         Tuan Pham Ngoc
 * @copyright      Copyright (C) 2011 - 2018 Ossolution Team
 * @license        GNU/GPL, see LICENSE.php
 */

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die();

class EDocmanViewUserdocumentsHtml extends OSViewHtml
{

	function display($tpl = null)
	{
		$config = EDocmanHelper::getConfig();
		if (!JFactory::getUser()->get('id'))
		{
			// Allow users to login
			$return = base64_encode(JUri::getInstance()->toString());
			JFactory::getApplication()->redirect('index.php?option=com_users&view=login&return=' . $return);
		}

		// Calculate default item ids
		$views  = array('categories', 'category', 'document');
		$itemId = 0;
		foreach ($views as $view)
		{
			if ($itemId = EDocmanHelperRoute::findView($view, 0))
			{
				break;
			}
		}
		$filter_order     = JFactory::getApplication()->input->getString('filter_order');
		$filter_order_Dir = JFactory::getApplication()->input->getString('filter_order_Dir');
        $this->state      = $this->model->getState();
        $this->state->set('filter_order',$filter_order);
        $this->state->set('filter_order_Dir',$filter_order_Dir);
		$this->Itemid     = JFactory::getApplication()->input->getInt('Itemid', 0);
		$this->items      = $this->model->getData();
		$this->pagination = $this->model->getPagination();

		$this->defaultItemid = $itemId ? $itemId : $this->Itemid;
		$this->path       = EDocmanHelper::getConfigValue('documents_path');
		$this->config	  = $config;
		$this->bootstrapHelper = new EDocmanHelperBootstrap($config->twitter_bootstrap_version);
		parent::display($tpl);
	}
}