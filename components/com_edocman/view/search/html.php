<?php
/**
 * @version        1.9.8
 * @package        Joomla
 * @subpackage     EDocman
 * @author         Dang Thuc Dam
 * @copyright	   Copyright (C) 2011-2017 Ossolution Team
 * @license        GNU/GPL, see LICENSE.php
 */

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die();

class EDocmanViewSearchHtml extends OSViewHtml
{

	function display()
	{
		$db = JFactory::getDbo();
		$user = JFactory::getUser();
		$config = EDocmanHelper::getConfig();
		$userId = $user->get('id');
		$model = $this->getModel();
		$state = $model->getState();
		$items = $model->getData();
		$pagination = $model->getPagination();
		$Itemid	= $this->input->getInt('Itemid',0);

		foreach ($items as $item)
		{
			if($state->show_category == 1){
				$query = $db->getQuery(true);
				$query->select('a.*')->from('#__edocman_categories as a')->join('inner','#__edocman_document_category as b on a.id = b.category_id')->where('b.document_id = "'.$item->id.'" and b.is_main_category = 1');
				$db->setQuery($query);
				$item->category = $db->loadObject();
			}
		}

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

		// Set new indicator
		if ($config->day_for_new > 0 && count($items))
		{
			EDocmanHelper::setNewIndicator($items, (int) $config->day_for_new);
		}

		if ($config->day_for_update > 0 && count($items))
		{
			EDocmanHelper::setUpdateIndicator($items, (int) $config->day_for_update);
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
		$this->userId				= $userId;
		$this->items				= $items;
		$this->pagination			= $pagination;
		$this->config				= $config;
		$this->category				= EDocmanHelper::getCategory($state->filter_category_id);
		$this->bootstrapHelper		= new EDocmanHelperBootstrap($config->twitter_bootstrap_version);
		$this->show_category		= $state->show_category;
		$this->categoryId			= $state->filter_category_id;
		$this->Itemid				= $Itemid;
		parent::display();
	}
}