<?php
/*
 * @package Joomla
 * @copyright Copyright (C) 2017 Open Source Matters. All rights reserved.
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.php
 *
 * @component Edocman Component
 * @copyright Copyright (C) Ossolution https://www.joomdonation.com
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 */

defined('_JEXEC') or die();

class EDocmanModelPlay extends EDocmanModelList
{
	function __construct() {
		
		$app	= JFactory::getApplication();
		parent::__construct();
		
	}

	function getDocumentDetails($fileId){
        $db = JFactory::getDbo();
        if(!$fileId){
            return null;
        }else{
            $query = $db->getQuery(true);
            $query->select("*")->from("#__edocman_documents")->where("id = '".$fileId."'");
            $db->setQuery($query);
            $document = $db->loadObject();
            return $document;
        }
    }

	function copyMediaToTmpDirectory($fileid,$filename,$original_filename){
		jimport('joomla.filesystem.folder');
		jimport('joomla.filesystem.file');
		$config = EdocmanHelper::getConfig();
		$documents_path = $config->documents_path;
		//1.Create directory
        if(!JFolder::exists(JPATH_ROOT.'/tmp/edocman')){
            JFolder::create(JPATH_ROOT.'/tmp/edocman');
        }
        $tmpFolderName = md5($fileid);
		if(!JFolder::exists(JPATH_ROOT.'/tmp/edocman/'.$tmpFolderName)){
            JFolder::create(JPATH_ROOT.'/tmp/edocman/'.$tmpFolderName);
        }
        //2.copy file
		$original_filename = explode(".",$original_filename);
		$original_filename_ext = $original_filename[count($original_filename) - 1];
        JFile::copy($documents_path.'/'.$filename,JPATH_ROOT.'/tmp/edocman/'.$tmpFolderName.'/'.$fileid.".".$original_filename_ext);
	}
}
?>