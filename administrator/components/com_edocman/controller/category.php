<?php
/**
 * @version        1.9.7
 * @package        Joomla
 * @subpackage     Edocman
 * @author         Tuan Pham Ngoc
 * @copyright      Copyright (C) 2011 - 2018 Ossolution Team
 * @license        GNU/GPL, see LICENSE.php
 */

// No direct access
defined('_JEXEC') or die();

/**
 * Category controller class.
 */
class EDocmanControllerCategory extends EDocmanController
{
	protected function allowAdd($data = array())
	{
		$user = JFactory::getUser();

		if (isset($data['parent_id']))
		{
			$categoryId = (int) $data['parent_id'];
		}
		else
		{
			$categoryId = 0;
		}

		if ($categoryId)
		{
			return $user->authorise('core.create', 'com_edocman.category.' . $categoryId);
		}
		else
		{
			return $user->authorise('core.create', 'com_edocman') || count(EDocmanHelper::getAuthorisedCategories('core.create'));
		}
	}

	/**
	 * Method to check whether edit function is allowed for the given category
	 *
	 * @see OSControllerAdmin::allowEdit()
	 */
	function allowEdit($data = array())
	{
		$id   = isset($data['id']) ? (int) $data['id'] : 0;
		$user = JFactory::getUser();
		if ($id)
		{
			if ($user->authorise('core.edit', 'com_edocman.category.' . $id))
			{
				return true;
			}

			if ($user->authorise('core.edit.own', 'com_edocman.category.' . $id))
			{
				$item = $this->getModel()->getData();

				return $item->created_user_id == $user->id;
			}

			return false;
		}
		else
		{
			return parent::allowEdit($data);
		}
	}
}