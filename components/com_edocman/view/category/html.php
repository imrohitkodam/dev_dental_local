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

class EDocmanViewCategoryHtml extends OSViewHtml
{

	function display($tpl = null)
	{
		$config = EDocmanHelper::getConfig();
		$userId = JFactory::getUser()->get('id');
		$model  = $this->getModel();
		$state  = $model->getState();
		if ($state->id && !EDocmanHelper::canAccessCategory($state->id))
		{
			if (!$userId)
			{
				// Give not logged in users to login
				$return = base64_encode(JUri::getInstance()->toString());
				JFactory::getApplication()->redirect('index.php?option=com_users&view=login&return=' . $return);
			}
			else
			{
				JFactory::getApplication()->redirect('index.php', JText::_('EDOCMAN_INVALID_CATEGORY'));

				return;
			}
		}
		$items    = $model->getData();
		$category = EDocmanHelper::getCategory($state->id);
		if ($category)
		{
			$document        = JFactory::getDocument();
			$metaKey         = $category->metakey;
			$metaDescription = $category->metadesc;
			if ($metaKey)
			{
				$document->setMetaData('keywords', $metaKey);
			}

			if ($metaDescription)
			{
				$document->setMetaData('description', $metaDescription);
			}

			$document->setTitle($category->title . ' - ' . JFactory::getConfig()->get('sitename'));
			if ($config->process_plugin)
			{
				$category->description = JHtml::_('content.prepare', $category->description);
			}
		}
		if ($config->show_sort_options)
		{
			$options               = array();
			$options[]             = JHTML::_('select.option', '', JText::_('EDOCMAN_SORT_BY'));
			$options[]             = JHTML::_('select.option', 'tbl.title', JText::_('EDOCMAN_TITLE'));
			$options[]             = JHTML::_('select.option', 'tbl.created_time', JText::_('EDOCMAN_DATE'));
			$options[]             = JHTML::_('select.option', 'tbl.ordering', JText::_('EDOCMAN_ORDERING'));
			$lists['filter_order'] = JHTML::_('select.genericlist', $options, 'filter_order', ' class="input-medium" onchange="submit();" ', 'value', 'text', $state->filter_order);

			$options                   = array();
			$options[]                 = JHTML::_('select.option', 'ASC', JText::_('EDOCMAN_ASC'));
			$options[]                 = JHTML::_('select.option', 'DESC', JText::_('EDOCMAN_DESC'));
			$lists['filter_order_Dir'] = JHTML::_('select.genericlist', $options, 'filter_order_Dir', ' class="input-medium" onchange="submit();" ', 'value', 'text', $state->filter_order_Dir);
			$this->lists               = $lists;			
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
				$fileExt  = strtolower(JFile::getExt($fileName));
				if (in_array($fileExt, $extsForView) || ($item->view_url != ''))
				{
					$item->canView = 1;
				}
				else
				{
					$item->canView = 0;
				}
			}
		}

		if ($config->day_for_new > 0 && count($items))
		{
			EDocmanHelper::setNewIndicator($items, (int) $config->day_for_new);
		}

		if ($config->day_for_update > 0 && count($items))
		{
			EDocmanHelper::setUpdateIndicator($items, (int) $config->day_for_update);
		}

		if ($state->id && $category->category_layout)
		{
			$needles = array(
				'category' => array((int) $state->id)
			);
			if (!EDocmanHelperRoute::findItem($needles))
			{
				$this->setLayout($category->category_layout);
			}
		}
		if ($config->enable_rss)
		{
			$document = JFactory::getDocument();
			$link     = '&format=feed&limitstart=';
			$attribs  = array('type' => 'application/rss+xml', 'title' => 'RSS 2.0');
			$document->addHeadLink(JRoute::_($link . '&type=rss'), 'alternate', 'rel', $attribs);
			$attribs = array('type' => 'application/atom+xml', 'title' => 'Atom 1.0');
			$document->addHeadLink(JRoute::_($link . '&type=atom'), 'alternate', 'rel', $attribs);
		}

		// Load children categories of the current category
		if ($state->id > 0)
		{
			JLoader::register('EDocmanModelCategories', JPATH_ROOT . '/components/com_edocman/model/categories.php');
			$this->categories = OSModel::getInstance('Categories', 'EDocmanModel', array('ignore_session' => true, 'ignore_request' => true))
				->limitstart(0)
				->limit(0)
				->filter_order('tbl.ordering')
				->id($state->id)
				->getData();
		}
		else
		{
			$this->categories = array();
		}

		//Handle breadcrumbs
		$app      = JFactory::getApplication();
		$menuItem = $app->getMenu()->getActive();
		if ($menuItem)
		{
			if (isset($menuItem->query['view']) && ($menuItem->query['view'] == 'categories' || $menuItem->query['view'] == 'category'))
			{
				$parentId = (int) $menuItem->query['id'];
				if ($state->id)
				{
					$pathway = $app->getPathway();
					$paths   = EDocmanHelper::getCategoriesBreadcrumb($state->id, $parentId);
					for ($i = count($paths) - 1; $i >= 0; $i--)
					{
						$path    = $paths[$i];
						$pathUrl = EDocmanHelperRoute::getCategoryRoute($path->id, $this->Itemid);
						$pathway->addItem($path->title, $pathUrl);
					}
				}
			}
		}
		$pagination = $model->getPagination();
		if ($state->filter_order && ($state->filter_order != $state->getDefault('filter_order')))
		{
			$pagination->setAdditionalUrlParam('filter_order', $state->filter_order);
		}
		if ($state->filter_order_Dir == 'DESC' && ($state->filter_order_Dir != $state->getDefault('filter_order_Dir')))
		{
			$pagination->setAdditionalUrlParam('filter_order_Dir', $state->filter_order_Dir);
		}

		$show_subcat = 1;
		if($this->input->get('content_plugin') == 1){
			$show_subcat = $this->input->get('show_subcat',1);
		}

		$this->combine_categories = $this->input->get('combine_categories',0);

		$this->userId     = $userId;
		$this->items      = $items;
		$this->pagination = $pagination;
		$this->config     = $config;
		$this->category   = $category;
		$this->state      = $state;
		$this->show_subcat= $show_subcat;
		$this->categoryId = $state->id;
		$this->params     = JFactory::getApplication()->getParams();
		$this->bootstrapHelper = new EDocmanHelperBootstrap($config->twitter_bootstrap_version);

		parent::display($tpl);
	}
}