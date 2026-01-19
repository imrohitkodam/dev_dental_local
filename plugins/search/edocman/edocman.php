<?php
/**
 * @version		   1.7.2
 * @package        Edocman
 * @subpackage     Edocman Search plugin
 * @author         Tuan Pham Ngoc
 * @copyright	   Copyright (C) 2015 Ossolution Team
 * @license        GNU/GPL, see LICENSE.php
 */
defined('_JEXEC') or die();

class plgSearchEDocman extends JPlugin
{
	/**
	 *
	 * @return array An array of search areas
	 */
	function onContentSearchAreas()
	{
		static $areas = array('edocman_search' => 'Documents');
		return $areas;
	}

	function onContentSearch($text, $phrase = '', $ordering = '', $areas = null)
	{
		$user   = JFactory::getUser();
		$userId = $user->get('id');
		require_once JPATH_ROOT . '/components/com_edocman/helper/helper.php';
		require_once JPATH_ROOT . '/components/com_edocman/helper/route.php';
		$db = JFactory::getDbo();
		$Itemid = $this->params->get('item_id', 0);
		if (!$Itemid)
		{
			$Itemid = EDocmanHelper::getItemid();
		}
		EDocmanHelper::loadLanguage();
		$user = JFactory::getUser();
		if (is_array($areas))
		{
			if (!array_intersect($areas, array_keys($this->onContentSearchAreas())))
			{
				return array();
			}
		}
		// load plugin params info
		$pluginParams = $this->params;
		$limit = $pluginParams->def('search_limit', 50);
		$text = trim($text);
		if ($text == '')
		{
			return array();
		}
		$section = JText::_('EDOCMAN_DOCUMENTS');
		$wheres = array();
		switch ($phrase)
		{
			case 'exact':
				$text = $db->Quote('%' . $db->escape($text, true) . '%', false);
				$wheres2 = array();
				$wheres2[] = 'a.title LIKE ' . $text;
				$wheres2[] = 'a.short_description LIKE ' . $text;
				$wheres2[] = 'a.description LIKE ' . $text;
				$wheres2[] = 'a.indexed_content LIKE ' . $text;
				$where = '(' . implode(') OR (', $wheres2) . ')';
				break;
			
			case 'all':
			case 'any':
			default:
				$words = explode(' ', $text);
				$wheres = array();
				foreach ($words as $word)
				{
					$word = $db->Quote('%' . $db->escape($word, true) . '%', false);
					$wheres2 = array();
					$wheres2[] = 'a.title LIKE ' . $word;
					$wheres2[] = 'a.short_description LIKE ' . $word;
					$wheres2[] = 'a.description LIKE ' . $word;
					$wheres2[] = 'a.indexed_content LIKE ' . $word;
					$wheres[] = implode(' OR ', $wheres2);
				}
				$where = '(' . implode(($phrase == 'all' ? ') AND (' : ') OR ('), $wheres) . ')';
				break;
		}
		
		switch ($ordering)
		{
			case 'oldest':
				$order = 'a.created_time ASC';
				break;
			case 'alpha':
				$order = 'a.title ASC';
				break;
			case 'newest':
				$order = 'a.created_time DESC';
				break;
			default:
				$order = 'a.ordering ';
		}
		$user = JFactory::getUser();
		$nullDate = $db->quote($db->getNullDate());
		$nowDate  = $db->quote(JFactory::getDate()->toSql());
		$query = 'SELECT a.id, 0 AS cat_id, a.title AS title, a.image, a.short_description, a.description AS text, created_time AS `created`, ' .
			 $db->Quote($section) . ' AS section,' . ' "1" AS browsernav' . ' FROM #__edocman_documents AS a' . ' WHERE (' . $where .
			 ') AND ((a.user_ids = "" AND a.access IN (' . implode(',', $user->getAuthorisedViewLevels()) . ')) OR a.user_ids="'.$userId.'" OR a.user_ids LIKE "'.$userId.',%" OR a.user_ids LIKE "%,'.$userId.',%" OR a.user_ids LIKE "%,'.$userId.'" OR (a.created_user_id='.$userId.' AND a.created_user_id > 0)) AND a.published = 1 AND (a.publish_up = ' . $nullDate . ' OR a.publish_up <= ' . $nowDate . ') AND (a.publish_down = ' . $nullDate . ' OR a.publish_down >= ' . $nowDate . ')' . ' ORDER BY ' . $order;
		$db->setQuery($query, 0, $limit);
		$rows = $db->loadObjectList();
		if (count($rows))
		{
			foreach ($rows as $key => $row)
			{
				$rows[$key]->href = JRoute::_(EDocmanHelperRoute::getDocumentRoute($row->id, 0, $Itemid));
				if (!strlen(strip_tags($rows[$key]->text)))
				{
					$rows[$key]->text = $rows[$key]->short_description;
				}
				if($row->image != ""){
					if(file_exists(JPATH_ROOT.'/media/com_edocman/document/thumbs/'.$row->image)){
						$rows[$key]->image = JUri::root().'media/com_edocman/document/thumbs/'.$row->image;
					}
				}
			}
		}
		return $rows;
	}
}
