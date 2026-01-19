<?php
/**
 * @version         1.9.7
 * @package        Joomla
 * @subpackage     EDocman
 * @author         Tuan Pham Ngoc
 * @copyright	Copyright (C) 2011-2016 Ossolution Team
 * @license        GNU/GPL, see LICENSE.php
 */

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die();

class EDocmanViewSearchHtml extends OSViewHtml
{

	function display()
	{
		$user = JFactory::getUser();
		$config = EDocmanHelper::getConfig();
		$userId = $user->get('id');
        $model = $this->getModel();
        $state = $model->getState();		
		$items = $model->getData();
		$pagination = $model->getPagination();
		if ($config->process_plugin && count($items))
		{
			foreach ($items as $item)
			{
				$item->short_description = JHtml::_('content.prepare', $item->short_description);
			}
		}
		if ($config->show_view_button && count($items))
		{
			$extsForView = explode(',', $config->exts_for_view);
			for ($i = 0, $n = count($extsForView); $i < $n; $i++)
			{
				$extsForView[$i] = strtolower(trim($extsForView[$i]));
			}
			foreach ($items as $item)
			{
				$fileName = $item->filename;
				$fileExt = strtolower(JFile::getExt($fileName));
				if (in_array($fileExt, $extsForView))
				{
					$item->canView = 1;
				}
				else
				{
					$item->canView = 0;
				}
			}
		}
        if ($state->filter_category_id)
        {
            $pagination->setAdditionalUrlParam('filter_category_id', $state->filter_category_id);

        }
        if ($state->filter_search)
        {
            $pagination->setAdditionalUrlParam('filter_search', $state->filter_search);
        }
        if ($state->filter_tag)
        {
            $pagination->setAdditionalUrlParam('filter_tag', $state->filter_tag);
        }        
		$this->userId = $userId;
		$this->items = $items;
		$this->pagination = $pagination;		
		$this->config = $config;
        $this->category = EDocmanHelper::getCategory($state->filter_category_id);;

		parent::display();
	}
}