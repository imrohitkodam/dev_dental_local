<?php
/**
 * @copyright    Copyright (C) 2009-2016 ACYBA SAS - All rights reserved..
 * @license        GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 */
defined('_JEXEC') or die('Restricted access');

class plgAcymailingPayplans extends JPlugin{

	function __construct(&$subject, $config){
		parent::__construct($subject, $config);
		if(!isset($this->params)){
			$plugin = JPluginHelper::getPlugin('acymailing', 'payplans');
			$this->params = new acyParameter($plugin->params);
		}
	}

	function onAcyDisplayFilters(&$type, $context = "massactions"){

		if($this->params->get('displayfilter_'.$context, true) == false) return;

		$db = JFactory::getDBO();
		$db->setQuery("SELECT `title`,`plan_id` FROM #__payplans_plan ORDER BY `ordering` ASC");
		$allPlans = $db->loadObjectList();
		if(empty($allPlans)) return;

		$firstGroup = new stdClass();
		$firstGroup->title = JText::_('ACY_ALL');
		$firstGroup->plan_id = 0;
		array_unshift($allPlans, $firstGroup);

		$type['payplans'] = 'Payplans';

		$inoperator = acymailing_get('type.operatorsin');
		$inoperator->js = ' onchange="countresults(__num__)"';
		$statuspayplans = array();
		$statuspayplans[] = JHTML::_('select.option', '', JText::_('ALL_STATUS'));
		$statuspayplans[] = JHTML::_('select.option', '1601', JText::_('COM_PAYPLANS_STATUS_SUBSCRIPTION_ACTIVE'));
		$statuspayplans[] = JHTML::_('select.option', '1602', JText::_('COM_PAYPLANS_STATUS_SUBSCRIPTION_HOLD'));
		$statuspayplans[] = JHTML::_('select.option', '1603', JText::_('COM_PAYPLANS_STATUS_SUBSCRIPTION_EXPIRED'));

		$return = '<div id="filter__num__payplans">';
		$return .= $inoperator->display("filter[__num__][payplans][type]").' '.JHTML::_('select.genericlist', $statuspayplans, "filter[__num__][payplans][status]", 'class="inputbox" size="1" onchange="countresults(__num__)"').' '.JHTML::_('select.genericlist', $allPlans, "filter[__num__][payplans][plan]", 'class="inputbox" size="1" onchange="countresults(__num__)"', 'plan_id', 'title');
		$return .= '<br /><input type="text" name="filter[__num__][payplans][signup_date_inf]" onchange="countresults(__num__)" value="" onclick="displayDatePicker(this,event)"/> < '.JText::_('CREATED_DATE').' < <input type="text" name="filter[__num__][payplans][signup_date_sup]" onchange="countresults(__num__)" value="" onclick="displayDatePicker(this,event)" />';
		$return .= '<br /><input type="text" name="filter[__num__][payplans][expiration_inf]" onchange="countresults(__num__)" value="'.date('Y-m-d').'" onclick="displayDatePicker(this,event)" /> < '.JText::_('EXPIRY_DATE').' < <input type="text" name="filter[__num__][payplans][expiration_sup]" onchange="countresults(__num__)" value="" onclick="displayDatePicker(this,event)" />';

		$return .= '</div>';
		return $return;
	}

	function onAcyProcessFilterCount_payplans(&$query, $filter, $num){
		$this->onAcyProcessFilter_payplans($query, $filter, $num);
		return JText::sprintf('SELECTED_USERS', $query->count());
	}

	function onAcyProcessFilter_payplans(&$query, $filter, $num){
		$db = JFactory::getDBO();
		$lj = "`#__payplans_subscription` as payplans$num ON payplans$num.`user_id` = sub.`userid`";
		if(!empty($filter['plan'])) $lj .= " AND payplans$num.`plan_id` = ".(int)$filter['plan'];
		if(!empty($filter['status'])) $lj .= " AND payplans$num.`status` = ".intval($filter['status']);
		if(!empty($filter['signup_date_inf'])){
			$filter['signup_date_inf'] = acymailing_replaceDate($filter['signup_date_inf']);
			if(is_numeric($filter['signup_date_inf'])) $filter['signup_date_inf'] = date('Y-m-d H:i', $filter['signup_date_inf']);
			$lj .= " AND payplans$num.`subscription_date` > ".$db->Quote($filter['signup_date_inf']);
		}
		if(!empty($filter['signup_date_sup'])){
			$filter['signup_date_sup'] = acymailing_replaceDate($filter['signup_date_sup']);
			if(is_numeric($filter['signup_date_sup'])) $filter['signup_date_sup'] = date('Y-m-d H:i', $filter['signup_date_sup']);
			$lj .= " AND payplans$num.`subscription_date` < ".$db->Quote($filter['signup_date_sup']);
		}
		if(!empty($filter['expiration_inf'])){
			$filter['expiration_inf'] = acymailing_replaceDate($filter['expiration_inf']);
			if(is_numeric($filter['expiration_inf'])) $filter['expiration_inf'] = date('Y-m-d H:i', $filter['expiration_inf']);
			$lj .= " AND (payplans$num.`expiration_date` > ".$db->Quote($filter['expiration_inf'])." OR payplans$num.`expiration_date` = '0000-00-00 00:00:00')";
		}
		if(!empty($filter['expiration_sup'])){
			$filter['expiration_sup'] = acymailing_replaceDate($filter['expiration_sup']);
			if(is_numeric($filter['expiration_sup'])) $filter['expiration_sup'] = date('Y-m-d H:i', $filter['expiration_sup']);
			$lj .= " AND payplans$num.`expiration_date` < ".$db->Quote($filter['expiration_sup'])." AND payplans$num.`expiration_date` > '0000-00-00 00:00:00'";
		}

		$query->leftjoin['payplans_'.$num] = $lj;
		$query->where['member'] = 'sub.userid > 0';

		$operator = ($filter['type'] == 'IN') ? 'IS NOT NULL' : "IS NULL";
		$query->where[] = "payplans$num.`user_id` ".$operator;
	}

}//endclass