<?php
/**
 * Edocman title slider
 *
 * @package 	Edocman title slider
 * @subpackage 	Edocman title slider
 * @version   	1.0
 * @author    	Dang Thuc Dam
 * @copyright 	Copyright (C) 2010 - 2016 www.gopiplus.com, LLC
 * @license   	http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 only
 *
 * http://www.joomdonation.com/
 */
// no direct access
defined('_JEXEC') or die;

class modEdocmanSlider
{
	public static function loadScripts(&$params)
	{
		$doc = JFactory::getDocument();
		$ats_jquery = $params->get("ats_jquery","yes");	
		if($ats_jquery == "yes")	
		{
			$doc->addScript(JURI::Root(true).'/modules/mod_edocman_title_slider/js/jquery.min.js');
		}
		$doc->addScript(JURI::Root(true).'/modules/mod_edocman_title_slider/js/jquery.cycle.all.min.js');
		$doc->addStyleSheet(JURI::Root(true).'/modules/mod_edocman_title_slider/css/mod_edocman_title_slider.css');
	}
	
	public static function getDocumentList(&$params)
	{
      	$db			= JFactory::getDBO();
		$user		= JFactory::getUser();
		
		$option		= JRequest::getCmd('option');
		$view		= JRequest::getCmd('view');
		
		$temp		= JRequest::getString('id');
		$temp		= explode(':', $temp);
		$id			= $temp[0];
		
		$ats_count 		= (int) $params->get('ats_count', 5);
		$ats_ordering 	= $params->get('ats_ordering');
		$ats_recent 	= $params->get('ats_recent');
		$ats_cat 		= $params->get('ats_cat', 1);
		$ats_sccart		= $params->get('ats_show_child_category_articles', 0);
		$ats_catixc		= $params->get('ats_catixc');
		$current		= 1;
		
		$app = JFactory::getApplication();

		$nullDate = $db->getNullDate();

		$date = JFactory::getDate();
		$now = $date->toSql();
		
		$where		= 'a.published = 1'
			. ' AND ( a.publish_up = '.$db->Quote($nullDate).' OR a.publish_up <= '.$db->Quote($now).' )'
			. ' AND ( a.publish_down = '.$db->Quote($nullDate).' OR a.publish_down >= '.$db->Quote($now).' )'
			;
		
		if ( $ats_recent ) :
			$where .= ' AND DATEDIFF('.$db->Quote($now).', a.created) < ' . $ats_recent;
		endif;
		
		if ($app->getLanguageFilter()) 
		{
			$where .= ' AND a.language in ('.$db->quote(JFactory::getLanguage()->getTag()).','.$db->quote('*').')';
		}

		switch ($ats_ordering)
		{
			case 'random':
				$ordering		= ' ORDER BY rand()';
				break;
			case 'h_asc':
				$ordering		= ' ORDER BY a.hits ASC';
				break;
			case 'h_dsc':
				$ordering		= ' ORDER BY a.hits DESC';
				break;
			case 'm_dsc':
				$ordering		= ' ORDER BY a.modified DESC, a.created DESC';
				break;
			case 'order':
				$ordering		= ' ORDER BY a.ordering ASC';
				break;
            case 'd_desc':
                $ordering		= ' ORDER BY a.downloads DESC';
                break;
            case 'm_desc':
                $ordering		= ' ORDER BY a.downloads ASC';
                break;
			case 'c_dsc':
			default:
				$ordering		= ' ORDER BY a.created_time DESC';
				break;
		}
		
		$joins = ' LEFT JOIN #__edocman_document_category AS cc ON cc.document_id = a.id';

        $catid = $the_id = $catCondition = '';
		
		if ($option == 'com_edocman' && $view == 'category' && $ats_cat == 1 )
		{
	    	$catid = $id;
		}
				   
        if ( $option == 'com_edocman' && $view == 'document' && $id )
		{
                $the_id = $id;
   
                if ($current == 0) 
				{
                    $where .= ' AND a.id!='.$the_id;
                }
				//if ( $ats_cat == 1 )
				//{
					//$catid = $article->catid;
				//}

        }
		if ($catid) 
		{
			$catCondition .= ' AND (cc.category_id='. $catid;
			if ($ats_sccart)
			{
                $categories = EDocmanHelper::getChildrenCategories($catid);
				if ($categories)
				{
					foreach($categories as $category)
					{
						if($category->id > 0){
							$catCondition .= ' OR cc.id='. $category->id;
						}
					}
				}
			}
			$catCondition .= ')';
		}
		
		if ( !empty($ats_catixc[0]) )
		{
                $catCondition .= ' AND cc.category_id in (' . implode( ' ,', $ats_catixc ) . ')';
        }

        $usergroup          = $user->groups;
        $usergroupArr       = array();
        $usergroupSql       = "";
        if(count($usergroup) > 0){
            foreach ($usergroup as $group){
                $usergroupArr[] = " (groups='$group' OR groups LIKE '$group,%' OR groups LIKE '%,$group,%' OR groups LIKE '%,$group') AND data_type = '1'";
            }
            $usergroupSql = implode(" OR ",$usergroupArr);
            $usergroupSql = " a.id in (Select item_id from #__edocman_levels where $usergroupSql) ";
            $usergroupSql = " OR (a.user_ids = '' AND a.accesspicker = '1' AND $usergroupSql ) ";
        }

		$query = 'SELECT a.*, cc.category_id ' .
			' FROM #__edocman_documents AS a' .
			$joins  .
			' WHERE '. $where .
			' AND ( a.access IN ('.implode(',', $user->getAuthorisedViewLevels()).')'.
            $usergroupSql .
            ' ) '.
			$catCondition .
			$ordering;
			
		$db->setQuery($query, 0, $ats_count);
		$rows = $db->loadObjectList();
		
		$items	= array();
		$i = 0;
        $activeItemid = JRequest::getInt('Itemid');
		foreach ( $rows as &$row ) 
		{
            $Itemid = EDocmanHelperRoute::getDocumentMenuId($row->id, $row->category_id, $activeItemid);
		    $row->title = htmlspecialchars( $row->title );
            if ( $the_id != $row->id or $current != 2 ) 
			{
                $link               = JRoute::_('index.php?option=com_edocman&view=document&id='.$row->id.'&catid='.$row->category_id.'&Itemid='.$Itemid);
				$items[$i]			= new stdClass;
				$items[$i]->links 	=  $link;
				$items[$i]->title	= $row->title;
				$i++;
			}
		}
      return $items;
    }
}
?>