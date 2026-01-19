<?php
/**
 * @version        1.11.3
 * @package        Joomla
 * @subpackage     EDocman
 * @author         Tuan Pham Ngoc
 * @copyright      Copyright (C) 2011 - 2019 Ossolution Team
 * @license        GNU/GPL, see LICENSE.php
 */

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die();

class EDocmanViewBookmarkHtml extends OSViewHtml
{

	function display($tpl = null)
	{
		$config                 = EDocmanHelper::getConfig();
		// Calculate default item ids
		$views                  = array('categories', 'category', 'document');
		$itemId                 = 0;
		foreach ($views as $view)
		{
			if ($itemId = EDocmanHelperRoute::findView($view, 0))
			{
				break;
			}
		}
		$filter_order           = JFactory::getApplication()->input->getString('filter_order');
		$filter_order_Dir       = JFactory::getApplication()->input->getString('filter_order_Dir');
        $this->state            = $this->model->getState();
        $this->state->set('filter_order',$filter_order);
        $this->state->set('filter_order_Dir',$filter_order_Dir);

		$this->Itemid           = JFactory::getApplication()->input->getInt('Itemid', 0);
		$this->items            = $this->model->getData();
		$this->pagination       = $this->model->getPagination();

		$this->defaultItemid    = $itemId ? $itemId : $this->Itemid;
		$this->path             = EDocmanHelper::getConfigValue('documents_path');
		$this->config	        = $config;
		$this->bootstrapHelper  = new EDocmanHelperBootstrap($config->twitter_bootstrap_version);
		parent::display($tpl);
	}
}