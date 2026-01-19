<?php
/**
 * JComments plugin for Edocman
 * @package    plg_edocman_jcomments
 * @subpackage Plugin
 * @link       http://www.joomdonation.com
 * @license    GNU/GPL, see LICENSE.php
 */
 defined( '_JEXEC' ) or die( 'Restricted access' );

// no direct access
defined('_JEXEC') or die;

jimport('joomla.plugin.plugin');

class plgEdocmanJComments extends JPlugin
{
	public function __construct(&$subject, $config)
	{
		parent::__construct($subject, $config);
		JPlugin::loadLanguage('plg_edocman_jcomments', JPATH_ADMINISTRATOR);
	}

	public function onEdocmanCommentsBlock(&$item, &$params, $limitstart)
	{
		$result = '';

        	$commentsAPI = JPATH_SITE . '/components/com_jcomments/jcomments.php';
	 	if (is_file($commentsAPI)) {
	 		require_once($commentsAPI);
 			$result = JComments::show($item->id, 'com_edocman', $item->title);
		}
		return $result;
	}

	public function onEdocmanCommentsCounter(&$item, &$params, $limitstart)
	{
		$result = '';

	       	$commentsAPI = JPATH_SITE . '/components/com_jcomments/jcomments.php';
	 	if (is_file($commentsAPI)) {
	 		require_once($commentsAPI);
			$count = JComments::getCommentsCount($item->id, 'com_edocman');

	 		if ($count == 0) {
	 			$link = $item->link.'#addcomments';
	 			$text = JText::_('PLG_EDOCMAN_JCOMMENTS_LINK_ADD_COMMENT');
			} else {
	 			$link = $item->link.'#comments';
	 			$text = JText::sprintf('PLG_EDOCMAN_JCOMMENTS_LINK_READ_COMMENTS', $count);
	 		}

	 		$anchor_css = $this->params->get('anchor_css');
	 		$class = empty($anchor_css) ? '' : ' class="' . $anchor_css . '"';

	 		$result = '<a href="' . $link . '"' . $class . ' title="' . $text . '">' . $text . '</a>';
		}
		return $result;
	}

	public function onAfterEdocmanSave(&$item, $isNew)
	{
	        if ($this->params->get('autosubscribe')) {
			if (!empty($item->id) && !empty($item->created_by) && $isNew) {
		        	$commentsAPI = JPATH_SITE . '/components/com_jcomments/jcomments.php';
			 	if (is_file($commentsAPI)) {
			 		require_once($commentsAPI);
					require_once(JPATH_SITE . '/components/com_jcomments/jcomments.subscription.php');
					$manager = JCommentsSubscriptionManager::getInstance();
					$manager->subscribe($item->id, 'com_edocman', $item->created_by);
				}
			}
		}
		return true;		
	}
}