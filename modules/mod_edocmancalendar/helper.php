<?php
/*------------------------------------------------------------------------
# mod_edocmancalendar - Edocman Calendar
# ------------------------------------------------------------------------
# author    Ossolution
# Copyright (C) 2018 www.joomdonation.com. All Rights Reserved.
# @license - http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
# Websites: https://www.joomdonation.com
# Technical Support:  Forum - https://www.joomdonation.com/forum
-------------------------------------------------------------------------*/

// no direct access
defined('_JEXEC') or die;

include_once JPATH_SITE.'/components/com_edocman/helper/route.php';
include_once JPATH_SITE.'/components/com_edocman/helper/helper.php';
require_once JPATH_ADMINISTRATOR . '/components/com_edocman/libraries/rad/loader.php';
require_once JPATH_ROOT.'/components/com_edocman/helper/file.class.php' ;

class modEdocmanCalendarHelper{
    public static function getCal(&$params) {
		$cal = new JObject();
		
		$curmonth=(int) JRequest::getVar('month',($params->get("defmonth")?$params->get("defmonth"):date('n')));
		$curyear=(int) JRequest::getVar('year',($params->get("defyear")?$params->get("defyear"):date('Y')));
		
		$dayofmonths=array(31,(!($curyear%400)?29:(!($curyear%100)?28:(!($curyear%4)?29:28)) ), 31, 30, 31, 30, 31, 31, 30, 31, 30, 31);
		
		$dayofmonth = $dayofmonths[$curmonth-1];
		$day_count = 1;
		$num = 0;

		$weeks = array();
		for($i = 0; $i < 7; $i++) {
			$a=floor((14-$curmonth)/12);
			$y=$curyear-$a;
			$m=$curmonth+12*$a-2;
			$dayofweek=($day_count+$y+floor($y/4)-floor($y/100)+floor($y/400)+floor((31*$m)/12)) % 7;
			$dayofweek = $dayofweek - 1 - $params->get("firstday");
			if($dayofweek <= -1) $dayofweek =$dayofweek + 7;


			if($dayofweek == $i)
			{
				$weeks[$num][$i] = $day_count.' 0';
				$day_count++;
			}
			else
			{
				$weeks[$num][$i] = ($dayofmonths[$curmonth!=1?($curmonth-2):(11)]-($dayofweek-1-$i)).' 1';
			}
		}

		while(true) {
			$num++;
			for($i = 0; $i < 7; $i++)
			{
				if ($day_count > $dayofmonth) {
					$weeks[$num][$i] = ($day_count-$dayofmonths[$curmonth-1]).' 1';
				} elseif ($day_count <= $dayofmonth) {
					$weeks[$num][$i] = $day_count.' 0';
				}
				$day_count++;
	  
				if($day_count > $dayofmonth && $i==6) break;
			}
			if($day_count > $dayofmonth && $i==6) break;
		}
		
		if (!$params->get('ajaxed')) {
			$ajaxed = 0;	
		} else {
			$ajaxed = 1;	
		}
		
		$monthname = 'EDOCMAN_MONTHNAME_' . $params->get( "submonthname", 0 ) . '_' . $curmonth;
		$monthname = modEdocmanCalendarHelper::encode($monthname,$params->get('encode'),$ajaxed);
		
		$cal->items = modEdocmanCalendarHelper::getList($params, $curmonth, $curyear);

		$cal->weeks = $weeks;
		$cal->curmonth = $curmonth;
		$cal->curyear = $curyear;
		$cal->monthname = $monthname;
		$cal->dayofmonths = $dayofmonths;
		$cal->ajaxed = $ajaxed;
		
		return $cal;
    }

	public static function getList(&$params, $curmonth, $curyear){
		$db = JFactory::getDbo();
		$jinput						= JFactory::getApplication()->input;
		$categoryIds				= $params->get('categoryIds','');
		$type						= $params->get('module_type', 1);
		$linkType					= $params->get('link_type', 0);
		$includeChildrenCategories  = $params->get('show_child_category_articles', 0);
		$count						= $params->get('count',10);
		$ordering					= $params->get('ordering','c_dsc');
		$usedate					= $params->get('usedate', 'created');
		$user = JFactory::getUser();
		
		JLoader::register('EDocmanModelCategory', JPATH_ROOT . '/components/com_edocman/model/category.php');
		$model = OSModel::getInstance('Category', 'EDocmanModel')
			->reset()
			->limitstart(0)
			->limit($count);
		switch($ordering){
			case "c_dsc":
				$model->set('filter_order','tbl.created_time');
				$model->set('filter_order_Dir','desc');
			break;
			case "m_dsc":
				$model->set('filter_order','tbl.modified_time');
				$model->set('filter_order_Dir','desc');
			break;
			case "p_dsc":
				$model->set('filter_order','tbl.publish_up');
				$model->set('filter_order_Dir','desc');
			break;
		}
		$model->set('filter_category_ids',$categoryIds);
		if ($includeChildrenCategories)
		{
			$model->setIncludeChildren(true);
		}

		$startDateRange = $curyear . '-' . $curmonth . '-01 00:00:00';
		$endDateRange   = $curyear . '-' . ($curmonth + 1) . '-01 00:00:00';
		if ( $curmonth == 12 ) {
			$endDateRange = ($curyear + 1) . '-01-01 00:00:00';
		}
		$model->set('filter_startdate',$startDateRange);
		$model->set('filter_enddate',$endDateRange);
		$model->set('filter_datetype',$usedate);

		$items        = $model->getData();
		foreach ($items as &$item) {
			$category	= EdocmanHelper::getDocumentCategory($item->id);
			$catId		= $category->id;
			$Itemid		= EDocmanHelperRoute::getDocumentMenuId($item->id,$catId,$jinput->getInt('Itemid',0));
			$item->link = JRoute::_('index.php?option=com_edocman&view=document&id='.$item->id.'&catid='.$catId.'&Itemid='.$Itemid);
			switch ($usedate) {
				case 'publish':
					$item->day = JHtml::_('date',strtotime($item->publish_up), 'j');
					$calitems[$item->day][] = $item;
				break;
				case 'modified':
					$item->day = JHtml::_('date',strtotime($item->modified), 'j');
					$calitems[$item->day][] = $item;
				break;
				default:
					$item->day = JHtml::_('date',strtotime($item->created_time), 'j');
					$calitems[$item->day][] = $item;
				break;
			}
		}
		return $calitems;
	}

	public static function encode($text,$encode,$ajaxed) {
		if ($encode!='UTF-8' && $ajaxed) { 
			$text=iconv("UTF-8", $encode, JText::_($text));
		}
		else {
			$text=JText::_($text);
		}
		return $text;
    }
}
