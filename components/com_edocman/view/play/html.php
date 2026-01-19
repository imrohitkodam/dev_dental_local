<?php
/**
 * @version        1.9.0
 * @package        Joomla
 * @subpackage     EDocman
 * @author         Dang Thuc Dam
 * @copyright	   Copyright (C) 2011-2017 Ossolution Team
 * @license        GNU/GPL, see LICENSE.php
 */

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die();

class EDocmanViewPlayHtml extends OSViewHtml
{

	function display()
	{
		$db = JFactory::getDbo();
		$user = JFactory::getUser();
		$config = EDocmanHelper::getConfig();
		
		$file = array();
		$file['user']   = $user;
		$uri 			= JFactory::getURI();
		$document		= JFactory::getDocument();
		$fileId			= JFactory::getApplication()->input->get('id', 0, 'int');
        $model			= $this->getModel();
        $file           = $model->getDocumentDetails($fileId);
        $file_url       = $file->filename;
        $fileExt        = EDocmanHelper::getFileExtension($file);

        $model->copyMediaToTmpDirectory($file->id,$file->filename,$file->original_filename);

		if ($file_url != "") {
			$canPlay	= 1;
			if ($canPlay == 1) {
				$original_filename = explode(".",$file->original_filename);
				$original_filename_ext = $original_filename[count($original_filename) - 1];
				$t['playfilewithpath']	    = JUri::root()."tmp/edocman/".md5($file->id)."/".$file->id.".".$original_filename_ext;
				$t['playerpath']			= JURI::base().'components/com_edocman/assets/flowplayer/';
				$t['playerwidth']			= '100%';
				$t['playerheight']		    = '100%';
				$t['html5_play']			= 1;
			} else {
				echo JText::_('EDOCMAN_ERROR_NO_CORRECT_FILE_TO_PLAY_FOUND');exit;
			}
		} else {
			echo JText::_('EDOCMAN_ERROR_NO_FILE_TO_PLAY_FOUND');exit;
		}
		
		$t['filetype']	= $fileExt;
		if ($fileExt == 'mp3') {
			$t['filetype'] 		= 'mp3';
			$t['playerheight']	= 30;
		} else if ($fileExt == 'ogg') {
			$t['filetype'] 		= 'ogg';
			$t['playerheight']	= 30;
		}

		$this->userId				= $user->id;
        $this->file                 = $file;
        $this->t                    = $t;
		parent::display();
	}
}