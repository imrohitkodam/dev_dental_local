<?php
/**
 * @package	AcyMailing for Joomla!
 * @version	5.8.1
 * @author	acyba.com
 * @copyright	(C) 2009-2017 ACYBA S.A.R.L. All rights reserved.
 * @license	GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 */
defined('_JEXEC') or die('Restricted access');
?><?php

class acyfrontHelper{

}

function acyCheckAccessList(){
	$listid = acymailing_getVar('int', 'listid');
	if(empty($listid)) return false;
	$listClass = acymailing_get('class.list');
	$myList = $listClass->get($listid);
	if(empty($myList->listid)) die('Invalid List');
	$currentUserid = acymailing_currentUserId();
	if(!empty($currentUserid) && $currentUserid == (int)$myList->userid) return true;
	if(empty($currentUserid) || $myList->access_manage =='none') return false;
	if($myList->access_manage != 'all'){
		if(!acymailing_isAllowed($myList->access_manage)) return false;
	}
	return true;
}

function acyCheckEditUser(){
	$listid = acymailing_getVar('int', 'listid');
	$subid = acymailing_getCID('subid');

	if(empty($subid)) return true;

	$status = acymailing_loadResult('SELECT status FROM #__acymailing_listsub WHERE subid='.intval($subid).' AND listid = '.intval($listid));
	if(empty($status)) return false;

	return true;
}

function acyCheckEditNewsletter($edit = true){
	$mailid = acymailing_getCID('mailid');

	$listClass = acymailing_get('class.list');
	$lists = $listClass->getFrontendLists();
	$frontListsIds = array();
	foreach($lists as $oneList){
		$frontListsIds[] = $oneList->listid;
	}

	if(empty($mailid)) return true;

	$db = JFactory::getDBO();

	$mail = acymailing_loadObject('SELECT * FROM `#__acymailing_mail` WHERE mailid = '.intval($mailid));
	if(empty($mail->mailid)) return false;
	$config = acymailing_config();
	if($edit AND !$config->get('frontend_modif',1) AND acymailing_currentUserId() != $mail->userid) return false;
	if($edit AND !$config->get('frontend_modif_sent',1) AND !empty($mail->senddate)) return false;

	$result = acymailing_loadResult('SELECT `mailid` FROM `#__acymailing_listmail` WHERE `mailid` = '.intval($mailid).' AND `listid` IN ('.implode(',',$frontListsIds).')');
	if(empty($result) && acymailing_currentUserId() != $mail->userid) return false;

	return true;
}
