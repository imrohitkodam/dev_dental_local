<?php
/**
 * @version        1.9.7
 * @package        Joomla
 * @subpackage     EDocman
 * @author         Tuan Pham Ngoc
 * @copyright      Copyright (C) 2011 - 2018 Ossolution Team
 * @license        GNU/GPL, see LICENSE.php
 */

// No direct access
defined('_JEXEC') or die;

/**
 * View to edit
 */
class EdocmanViewDocumentHtml extends OSViewItem
{
	protected function prepareView()
	{
		parent::prepareView();
		$this->config = EDocmanHelper::getConfig();
		$db    = JFactory::getDbo();
		$query = $db->getQuery(true);
		$query->select('count(extension_id)');
		$query->from('#__extensions');
		$query->where('`element` like "indexer" and `folder` like "edocman" and enabled=1');
		$db->setQuery($query);
		$count = $db->loadResult();
		$row  = $this->item;
		//print_r($row);
		$ext = strtolower(JFile::getExt($row->filename)) ;
		if ($ext == 'pdf' || $ext == 'doc' || $ext == 'docx'){
			if(($count > 0) and (JFolder::exists(JPATH_ROOT.'/plugins/edocman/indexer'))){
				$this->indexer = 1;
			}else{
				$this->indexer = 0;
			}
		}else{
			$this->indexer = 0;
		}
        #Plugin support
        JPluginHelper::importPlugin('edocman');
        $results = JFactory::getApplication()->triggerEvent('onEditDocument', array($this->item));
        $this->plugins  = $results;
        $this->bootstrapHelper = new EDocmanHelperBootstrap(EDocmanHelperHtml::getAdminBootstrapHelper());
	}	
}
