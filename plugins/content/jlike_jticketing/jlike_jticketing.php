<?php
defined ( '_JEXEC' ) or die ( 'Restricted access' );
/**
 * @package		jLike
 * @author 		Techjoomla http://www.techjoomla.com
 * @copyright 	Copyright (C) 2011-2012 Techjoomla. All rights reserved.
 * @license 	GNU/GPL v2 http://www.gnu.org/licenses/gpl-2.0.html
 *
 */

// Import library dependencies
jimport ( 'joomla.plugin.plugin' );
require_once(JPATH_SITE.'/components/com_jlike/helper.php');

//load language file
$lang =  JFactory::getLanguage();
$lang->load('plg_jlike_jticketing', JPATH_ADMINISTRATOR);

class plgContentjlike_jticketing extends JPlugin {


	function onBeforeDisplaylike($context, $event, $show_comments=-1, $show_like_buttons=0)
	{
		$app=JFactory::getApplication();
		if($app->getName()!='site'){
			return;
		}
		$html='';
		$app = JFactory::getApplication ();
		if ($app->scope != 'com_jticketing') {
			return;
		}

		//Check view & layout to show comments
		$input=JFactory::getApplication()->input;
		$view=$input->get('view','','STRING');
		$layout=$input->get('layout','','STRING');

		//Not to show anything related to commenting
		if($show_comments!=-1)
		{
			$show_comments=-1;
			$jlike_comments = $this->params->get('jlike_comments');

			if($jlike_comments)
			{
				if($view=='event')
				{
					//show comments
					$show_comments=1;
				}
			}
		}


		JRequest::setVar ( 'data', json_encode ( array ('cont_id' => $event['eventid'], 'element' => $context, 'title' => $event['title'], 'url' => $event['url'],'plg_name'=>'jlike_jticketing','show_comments'=>$show_comments, 'show_like_buttons'=>$show_like_buttons ) ) );

		require_once(JPATH_SITE.'/'.'components/com_jlike/helper.php');

		$jlikehelperObj=new comjlikeHelper();
		$html = $jlikehelperObj->showlike();

		return $html;
   }

	function getOwnerDetails($cont_id)
	{
		$db=JFactory::getDBO();
		$query="SELECT e.created_by FROM #__jticketing_events as e WHERE e.id=".$cont_id;
		$db->setQuery($query);
		return $created_by=$db->loadResult();
	}
}
