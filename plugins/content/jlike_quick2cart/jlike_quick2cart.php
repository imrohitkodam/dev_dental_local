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
//jlike_quick2cart
//load language file
$lang =  JFactory::getLanguage();
$lang->load('plg_jlike_quick2cart', JPATH_ADMINISTRATOR);

class plgContentjlike_quick2cart extends JPlugin {


	function onBeforeDisplaylike($context, $data)
	{
		$app=JFactory::getApplication();
		if($app->getName()!='site'){
			return;
		}
		$html='';
		$app = JFactory::getApplication ();
		if ($app->scope != 'com_quick2cart') {
			return;
		}

		//Check view & layout to show comments
		$input=JFactory::getApplication()->input;
		$view=$input->get('view','','STRING');
		$layout=$input->get('layout','','STRING');

		//Not to show anything related to commenting
		$show_comments=-1;
		$jlike_comments = $this->params->get('jlike_comments');

		if($jlike_comments)
		{
			if($view=='productpage' && $layout =='default')
			{
				//show comments
				$show_comments=1;
			}
		}
		$show_like_buttons=1;
		JRequest::setVar ( 'data', json_encode ( array ('cont_id' => $data['product_id'], 'element' => $context, 'title' => $data['title'], 'url' => $data['url'],'plg_name'=>'jlike_quick2cart','show_comments'=>$show_comments,'show_like_buttons'=>$show_like_buttons ) ) );

		require_once(JPATH_SITE.'/'.'components/com_jlike/helper.php');

		$jlikehelperObj=new comjlikeHelper();
		$html = $jlikehelperObj->showlike();
		return $html;
   }

	function getOwnerDetails($cont_id)
	{
		$db=JFactory::getDBO();

		$query="SELECT s.owner
				FROM #__kart_items AS i
				LEFT JOIN #__kart_store AS s ON i.store_id = s.id
				WHERE i.item_id =".$cont_id;

		$db->setQuery($query);
		return $created_by=$db->loadResult();
	}
}
