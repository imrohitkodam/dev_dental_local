<?php
/**
 * @version        1.10.0
 * @package        Joomla
 * @subpackage     EDocman
 * @author         Tuan Pham Ngoc
 * @copyright      Copyright (C) 2011-2018 Ossolution Team
 * @license        GNU/GPL, see LICENSE.php
 */
defined('_JEXEC') or die();

class EDocmanViewCategoriesHtml extends OSViewHtml
{

	function display()
	{
		$model  = $this->getModel();
		$state  = $model->getState();
		$config = EDocmanHelper::getConfig();
		$items  = $model->getData();
		if ($state->id && !EDocmanHelper::canAccessCategory($state->id))
		{
			$user = JFactory::getUser();
			if (!$user->id)
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
		if ($state->id)
		{
			$document = JFactory::getDocument();
			$category = EDocmanHelper::getCategory($state->id);
			if ($category->metakey)
			{
				$document->setMetaData('keywords', $category->metakey);
			}
			if ($category->metadesc)
			{
				$document->setMetaData('description', $category->metadesc);
			}
			if ($config->process_plugin)
			{
				$category->description = JHtml::_('content.prepare', $category->description);
			}
			$this->category = $category;
			$document->setTitle($category->title . ' - ' . JFactory::getConfig()->get('sitename'));
		}

		// Trigger content plugin in category description
		if ($config->process_plugin && count($items))
		{
			foreach ($items as $item)
			{
				$item->description = JHtml::_('content.prepare', $item->description);
			}
		}

		$this->bootstrapHelper = new EDocmanHelperBootstrap($config->twitter_bootstrap_version);
		$this->config     = $config;
		$this->items      = $items;
		$this->pagination = $model->getPagination();
		$this->categoryId = $state->id;
		$this->params = JFactory::getApplication()->getParams();

		parent::display();
	}
}