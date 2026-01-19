<?php
/**
 * @version        1.7.0
 * @package        Joomla
 * @subpackage     Edocman
 * @author         Tuan Pham Ngoc
 * @copyright      Copyright (C) 2011 - 2015 Ossolution Team
 * @license        GNU/GPL, see LICENSE.php
 */
defined('_JEXEC') or die;

class modEDocmanSearchHelper
{
	public static function categoryList($categoryId,$input_style)
	{
		$user   = JFactory::getUser();
		$db     = JFactory::getDbo();
		$userId = $user->id;
		$query  = $db->getQuery(true);
		$query->select('id, title, parent_id');
		$query->from('#__edocman_categories AS a');
		$query->where('published=1');
		$query->where("((a.user_ids = '' AND a.access IN (" . implode(',', $user->getAuthorisedViewLevels()) . ")) OR a.user_ids='$userId' OR a.user_ids LIKE '$userId,%' OR a.user_ids LIKE '%,$userId,%' OR a.user_ids LIKE '%,$userId')");
		$query->order('ordering');
		$db->setQuery($query);
		$rows     = $db->loadObjectList();
		$children = array();
		// first pass - collect children
		if (count($rows))
		{
			foreach ($rows as $v)
			{
				$pt   = $v->parent_id;
				$list = @$children[$pt] ? $children[$pt] : array();
				array_push($list, $v);
				$children[$pt] = $list;
			}
		}
		$list      = JHtml::_('menu.treerecurse', 0, '', array(), $children, 9999);
		$options   = array();
		$options[] = JHtml::_('select.option', 0, JText::_('EDOCMAN_SELECT_CATEGORY'));
		if (count($list))
		{
			foreach ($list as $row)
			{
				$options[] = JHtml::_('select.option', $row->id, $row->treename);
			}
		}

		return JHtml::_('select.genericlist', $options, 'filter_category_id', array(
			'option.text.toHtml' => false,
			'list.attr'          => 'class="'.$input_style.' edocman_search_category"  ',
			'option.text'        => 'text',
			'option.key'         => 'value',
			'list.select'        => (int) $categoryId
		));
	}
}