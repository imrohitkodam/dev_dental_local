<?php
/**
 * @version         1.9.7
 * @package        Joomla
 * @subpackage     EDocman
 * @author         Tuan Pham Ngoc
 * @copyright      Copyright (C) 2011 - 2018 Ossolution Team
 * @license        GNU/GPL, see LICENSE.php
 */

// no direct access
defined('_JEXEC') or die('Restricted access');

class EDocmanViewCategoryFeed extends OSView
{
	// Creates the Event Feed
	public function display()
	{
		$Itemid = JFactory::getApplication()->input->getInt('Itemid');
		$doc = JFactory::getDocument();
		$config = JFactory::getConfig();
		$model = $this->getModel();
		$state = $model->getState();
		$model->limitstart(0)
			->limit($config->get('feed_limit'))
			->filter_order('tbl.created_time')
			->filder_order_Dir('DESC');
		$rows = $model->getData();
		$category = EDocmanHelper::getCategory($state->id);
		foreach ($rows as $row)
		{
			$title = $this->escape($row->title);
			$title = html_entity_decode($title, ENT_COMPAT, 'UTF-8');
			$link = JRoute::_('index.php?option=com_edocman&view=document&id=' . $row->id . '&Itemid=' . $Itemid);
			// feed item description text
			@$created = ($row->created_time ? date('r', strtotime($row->created_time)) : '');
			// load individual item creator class
			$item = new JFeedItem();
			$item->title = $title;
			$item->link = $link;
			$item->description = $row->description;
			$item->date = @$created;
			$item->category = $category ? $category->title : '';
			$doc->addItem($item);
		}
	}
}