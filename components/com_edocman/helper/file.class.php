<?php
/**
 * @version     1.7.6
 * @category	EDOCman
 * @package		EDOCman 1.7.5
 * @copyright	Copyright (C) 2008 - 2016 Osolution team. All rights reserved.
 * @license		GNU GPLv2 <http://www.gnu.org/licenses/old-licenses/gpl-2.0.html>
 * @link     	http://www.joomdonation.com
 */
defined('_JEXEC') or die('Restricted access');
require_once (JPATH_ROOT . '/components/com_edocman/helper/mime.class.php');
jimport('joomla.filesystem.path');

class EDocman_File
{

	/**
	 *
	 * @access public
	 * @var string
	 */
	var $path = null;

	/**
	 *
	 * @access public
	 * @var string
	 */
	var $name = null;

	/**
	 *
	 * @access public
	 * @var string
	 */
	var $mime = null;

	/**
	 *
	 * @access public
	 * @var string
	 */
	var $ext = null;

	/**
	 *
	 * @access public
	 * @var string
	 */
	var $size = null;

	/**
	 *
	 * @access public
	 * @var string
	 */
	var $date = null;

	/**
	 *
	 * @access private
	 * @var string
	 */
	var $_err = null;

    var $id = null;

	function EDocman_File($id,$name, $path)
	{
        $db = JFactory::getDbo();
        if($id > 0){
            $this->id = $id;
            $query = $db->getQuery(true);
            $query->clear();
            $query->select('file_size')->from('#__edocman_documents')->where("id = '$id'");
            $db->setQuery($query);
            $file_size = $db->loadResult();
        }
		$path = JPath::clean($path);
		if (!is_dir($path))
		{
			$path = dirname($path);
			// Make sure there's a trailing slash in the path
			$path = JPath::clean($path);
		}
		
		$this->name = trim($name);
		$this->path = $path;
        //if($file_size == "") {
        if(file_exists($this->path . '/' . $this->name))
        {
            $this->size = @filesize($this->path . '/' . $this->name);
            $this->size = EDocman_File::getSize();
        }
        //}else{
            //$this->size = $file_size;
            //$this->size = EDocman_File::getSize();
        //}
        $db->setQuery("Update #__edocman_documents set file_size = '".$this->size."' where id = '$id'");
        $db->execute();
		$this->mime = EDocman_MIME_Magic::filenameToMIME($this->id,$this->name, false);
		$this->ext = $this->getExtension();
		$this->date = EDocman_File::getDate();
        $this->fileicon = $this->returnFileIcon();
	}

	/**
	 * Get file size
	 *
	 * Gets the file size and convert it to friendly format
	 * 
	 * @param
	 *        	void
	 * @return string Returns filesize in a friendly format.
	 */
	function getSize()
	{
		$kb = 1024;
		$mb = 1024 * $kb;
		$gb = 1024 * $mb;
		$tb = 1024 * $gb;
		
		$size = $this->size;

		if ($size)
		{
			if ($size < $kb)
			{
				$final = round($size, 2);
				$file_size = $final . ' ' . 'Byte';
			}
			elseif ($size < $mb)
			{
				$final = round($size / $kb, 2);
				$file_size = $final . ' ' . 'KB';
			}
			elseif ($size < $gb)
			{
				$final = round($size / $mb, 2);
				$file_size = $final . ' ' . 'MB';
			}
			elseif ($size < $tb)
			{
				$final = round($size / $gb, 2);
				$file_size = $final . ' ' . 'GB';
			}
			else
			{
				$final = round($size / $tb, 2);
				$file_size = $final . ' ' . 'TB';
			}
		}
		else
		{
			if ($size == 0)
			{
				$file_size = 'EMPTY';
			}
			else
			{
				$file_size = 'ERROR';
			}
		}
		return $file_size;
	}

	/**
	 * Gets the extension of a file
	 * 
	 * @return string The file extension
	 */
	function getExtension()
	{
        if($this->name != "") {
            $dotpos = strrpos($this->name, ".");
            if ($dotpos < 1)
                return "unk";
            return substr($this->name, $dotpos + 1);
        }else{ //in case user store document in different hosting, get file extension
            $db = JFactory::getDbo();
            $query = $db->getQuery(true);
            $query->clear();
            $query->select('document_url')->from('#__edocman_documents')->where("id = '$this->id'");
            $db->setQuery($query);
            $document_url = $db->loadResult();
            $dotpos = strrpos($document_url, ".");
            if ($dotpos < 1)
                return "unk";
            return substr($document_url, $dotpos + 1);
        }
	}

	function returnFileIcon()
    {
        $file_ext = $this->getExtension();
        $mapArr = array('pdf','jpg','png','gif','jpeg','avi','m4v','mov','mp4','flv','ogv','mp3','ogg','doc', 'docx', 'ppt', 'pptx', 'zip', 'rar', 'zipx', 'csv', 'xls','xlsx','exe','html');
        $iconArr = array('edicon edicon-file-pdf',
                        'edicon edicon-image',
                        'edicon edicon-image',
                        'edicon edicon-image',
                        'edicon edicon-image',
                        'edicon edicon-film','edicon edicon-film','edicon edicon-film','edicon edicon-film','edicon edicon-film','edicon edicon-film',
                        'edicon edicon-file-music','edicon edicon-file-music',
                        'edicon edicon-file-word','edicon edicon-file-word',
                        'edicon edicon-libreoffice','edicon edicon-libreoffice',
                        'edicon edicon-file-zip','edicon edicon-file-zip','edicon edicon-file-zip',
                        'edicon edicon-file-excel','edicon edicon-file-excel','edicon edicon-file-excel',
                        'edicon edicon-file-empty','edicon edicon-sphere'
        );
        if(in_array($file_ext,$mapArr)){
            $key = array_search($file_ext,$mapArr);
            return $iconArr[$key];
        }else{
            return "edicon edicon-folder-open";
        }
    }

	function getDate($type = 'm')
	{
		$app = JFactory::getApplication();
		$offset = $app->getCfg('offset');
		
		$date = '';
		
		switch ($type)
		{
			case 'm':
				$date = filemtime($this->path . '/' . $this->name);
				break;
			case 'a':
				$date = fileatime($this->path . '/' . $this->name);
				break;
			case 'c':
				$date = filectime($this->path . '/' . $this->name);
				break;
		}
		return strftime("%A %B %d, %Y %H:%M:%S", $date + ($offset * 60 * 60));
	}
}