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
$lang->load('plg_jlike_jgive', JPATH_ADMINISTRATOR);

class plgContentjlike_jgive extends JPlugin {


	function onBeforeDisplaylike($context, $compaign, $show_comments=-1, $show_like_buttons=0)
	{
		$app=JFactory::getApplication();
		if($app->getName()!='site'){
			return;
		}
		$html='';
		$app = JFactory::getApplication ();
		if ($app->scope != 'com_jgive') {
			return;
		}

		//Check view & layout to show comments
		$input=JFactory::getApplication()->input;
		$view=$input->get('view','','STRING');
		$layout=$input->get('layout','','STRING');

		if($show_comments!=-1)
		{
			//Not to show anything related to commenting
			$show_comments=-1;
			$jlike_comments = $this->params->get('jlike_comments');

			if($jlike_comments)
			{
				if($view=='campaign' && $layout =='single')
				{
					//show comments
					$show_comments=1;
				}
			}
		}

		JRequest::setVar ( 'data', json_encode ( array ('cont_id' => $compaign['campaignid'], 'element' => $context, 'title' => $compaign['title'], 'url' => $compaign['url'],'plg_name'=>'jlike_jgive','show_comments'=>$show_comments, 'show_like_buttons'=>$show_like_buttons ) ) );

		require_once(JPATH_SITE.'/'.'components/com_jlike/helper.php');

		$jlikehelperObj=new comjlikeHelper();
		$html = $jlikehelperObj->showlike();
		return $html;
   }

	function getOwnerDetails($cont_id)
	{
		$db=JFactory::getDBO();
		$query="SELECT c.creator_id FROM #__jg_campaigns as c WHERE c.id=".$cont_id;
		$db->setQuery($query);
		return $created_by=$db->loadResult();
	}
}
