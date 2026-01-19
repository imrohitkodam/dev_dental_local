<?php
/**
 * @version        1.6.1
 * @package        Joomla
 * @subpackage     EDocman
 * @author         Tuan Pham Ngoc
 * @copyright      Copyright (C) 2011 - 2015 Ossolution Team
 * @license        GNU/GPL, see LICENSE.php
 */

defined('_JEXEC') or die();

class plgContentEDocmanCategory extends JPlugin
{
	/**
	 * Method is called by the view
	 *
	 * @param    object         The article object.  Note $article->text is also available
	 * @param    object         The article params
	 * @param    int            The 'page' number
	 */
	function onContentPrepare($context, &$article, &$params, $limitstart)
	{
		error_reporting(0);
		$app = JFactory::getApplication();
		if ($app->getName() != 'site')
		{
			return true;
		}
		if (strpos($article->text, 'edocmancategory') === false)
		{
			return true;
		}
		$regex         = "#{edocmancategory (\d+)}#s";
		$article->text = preg_replace_callback($regex, array(&$this, '_renderEDocmanCategory'), $article->text);

		return true;
	}

	/**
	 * Replace the text with the event detail
	 *
	 * @param array $matches
	 */
	function _renderEDocmanCategory(&$matches)
	{
		require_once JPATH_ADMINISTRATOR . '/components/com_edocman/libraries/rad/loader.php';
		EDocmanHelper::loadLanguage();
		$request = array('view' => 'category', 'id' => (int) $matches[1], 'limit' => 0, 'content_plugin' => 1, 'Itemid' => EDocmanHelper::getItemid());
		$input   = new OSInput($request);
		$config  = array(
			'default_controller_class' => 'EDocmanController',
			'default_view'             => 'categories',
			'class_prefix'             => 'EDocman'
		);
		ob_start();
		//Initialize the controller, execute the task and perform redirect if needed
		OSController::getInstance('com_edocman', $input, $config)
			->execute()
			->redirect();

		return ob_get_clean();
	}
}