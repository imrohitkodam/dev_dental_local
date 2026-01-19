<?php
/**
 * @version        1.0
 * @package        Joomla
 * @subpackage     EDocman
 * @author         Tuan Pham Ngoc
 * @copyright      Copyright (C) 2011 - 2015 Ossolution Team
 * @license        GNU/GPL, see LICENSE.php
 */

defined('_JEXEC') or die('Restricted access');
require_once JPATH_ROOT . '/plugins/edocman/dropbox/vendor/autoload.php';
require_once JPATH_ROOT . '/components/com_edocman/helper/helper.php';

/*
Version 1
use League\Flysystem\Dropbox\DropboxAdapter;
use League\Flysystem\Filesystem;
use Dropbox\Client;
*/

//version 2
use League\Flysystem\Filesystem;
use Srmklive\Dropbox\Client\DropboxClient;
use Srmklive\Dropbox\Adapter\DropboxAdapter;

class plgEDocmanDropbox extends JPlugin
{

	/***
	 * @var Filesystem
	 */
	private $filesytem;


	public function __construct(& $subject, $config)
	{
		parent::__construct($subject, $config);

	}

	/**
	 * Get file system object
	 *
	 * @return Filesystem
	 */
	private function getFileSystem()
	{
		if (!$this->filesytem)
		{
			$client  = new DropboxClient($this->params->get('access_token'));
			$adapter = new DropboxAdapter($client);
			$this->filesytem = new Filesystem($adapter);
		}
		return $this->filesytem;
	}

	/**
	 * Create folder on dropbox if needed
	 *
	 * @param $context
	 * @param $row
	 * @param $isNew
	 */
	public function onCategoryAfterSave($context, $row, $isNew)
	{
        $config = EDocmanHelper::getConfig();
		$filesystem = $this->getFileSystem();

        $root_path = $this->params->get('dropbox_path');
        $root_path = "edocman"."/".$root_path;
        if (!$filesystem->has($root_path))
        {
            $filesystem->createDir($root_path);
        }
        if(substr($root_path,strlen($root_path)-1) == "/"){
            $root_path = substr($root_path,0,strlen($root_path)-1);
        }

        if($config->activate_herachical_folder_structure) {
            $path = $row->path;
            if ($path != "") {
                if(substr($path,0,1) == "/"){
                    $path = substr($path,1);
                }
                $category_path = $root_path . "/" . $path;
                if (!$filesystem->has($category_path)) {
                    $filesystem->createDir($category_path);
                }
            }
        }
	}

    /**
     * This function is used to upload documents into Dropbox through Batchupload tool
     * @param $savedFilename
     * @param $file
     * @param $path
     * @return bool
     */
    public function onDocumentBatchUpload($savedFilename, $file, $path){
        $config = EDocmanHelper::getConfig();
        $root_path = $this->params->get('dropbox_path');
        $root_path = "edocman"."/".$root_path;
        if(substr($root_path,strlen($root_path)-1) == "/"){
            $root_path = substr($root_path,0,strlen($root_path)-1);
        }

        $filesystem = $this->getFileSystem();
        if ($path != "") {
            $category_path = $root_path . "/" . $path;
            if (!$filesystem->has($category_path)) {
                $filesystem->createDir($category_path);
            }
        } else {
            $category_path = $root_path;
        }

        $stream = fopen($file['tmp_name'], 'r+');

        if ($category_path)
        {
            $filePath = $category_path . '/' . $savedFilename;
        }

        $filesystem = $this->getFileSystem();

        if ($filesystem->has($filePath))
        {
            $filesystem->updateStream($filePath, $stream);
            $filesize = $filesystem->getSize($filePath);
        }
        else
        {
            $filesystem->writeStream($filePath, $stream);
            $filesize = $filesystem->getSize($filePath);
        }

        $return = array();
        $return[0] = true;
        $return[1] = $filesize;
        return $return;
    }

	/**
	 * Process file upload
	 *
	 * @param $row
	 * @param $isNew
	 * @param $file
	 * @param $path
	 * @param $fileName
	 *
	 * @return bool
	 */
	public function onDocumentUpload($row, $isNew, $file, $path, $fileName, $categoryId)
	{
        $config = EDocmanHelper::getConfig();
        $db = JFactory::getDbo();
		if($isNew == 1){
			$category_id = $categoryId;
		}else{
			$db->setQuery("Select category_id from #__edocman_document_category where document_id = '$row->id' and is_main_category = '1'");
			$category_id = $db->loadResult();
		}
        $db->setQuery("Select * from #__edocman_categories where id = '$category_id'");
        $category = $db->loadObject();
        $path = $category->path;

        $root_path = $this->params->get('dropbox_path');
        $root_path = "edocman"."/".$root_path;
        if(substr($root_path,strlen($root_path)-1) == "/"){
            $root_path = substr($root_path,0,strlen($root_path)-1);
        }

        $filesystem = $this->getFileSystem();

        if (!$filesystem->has($root_path))
        {
            $filesystem->createDir($root_path);
        }
        if($config->activate_herachical_folder_structure) {
            if ($path != "") {
                $category_path = $root_path . "/" . $path;
                if (!$filesystem->has($category_path)) {
                    $filesystem->createDir($category_path);
                }
            } else {
                $category_path = $root_path;
            }
        }else{
            $category_path = $root_path;
        }


		$stream = fopen($file['tmp_name'], 'r+');

		if ($category_path)
		{
			$filePath = $category_path . '/' . $fileName;
		}
		else
		{
			$filePath = $fileName;
		}
		$filesystem = $this->getFileSystem();

		if ($filesystem->has($filePath))
		{
			$filesystem->updateStream($filePath, $stream);
            //get file size
            $filesize = $filesystem->getSize($filePath);
		}
		else
		{
			$filesystem->writeStream($filePath, $stream);
            $filesize = $filesystem->getSize($filePath);
		}
        $query = $db->getQuery(true);
        $query->clear();
        $query->update('#__edocman_documents')->set('file_size="'.$filesize.'"')->where('id="'.$row->id.'"');
        $db->setQuery($query);
        $db->execute();

		$data['file_size'] = $filesize;

		return true;
	}

	/**
	 * Process file upload
	 *
	 * @param $row
	 * @param $isNew
	 * @param $file
	 * @param $path
	 * @param $fileName
	 *
	 * @return bool
	 */
	public function onFilesizeUpload($row, $isNew, $path, $fileName)
	{
        $config = EDocmanHelper::getConfig();
        $db = JFactory::getDbo();
        $db->setQuery("Select category_id from #__edocman_document_category where document_id = '$row->id' and is_main_category = '1'");
        $category_id = $db->loadResult();
        $db->setQuery("Select * from #__edocman_categories where id = '$category_id'");
        $category = $db->loadObject();
        $path = $category->path;

        $root_path = $this->params->get('dropbox_path');
        $root_path = "edocman"."/".$root_path;
        if(substr($root_path,strlen($root_path)-1) == "/"){
            $root_path = substr($root_path,0,strlen($root_path)-1);
        }

        $filesystem = $this->getFileSystem();

        if (!$filesystem->has($root_path))
        {
            $filesystem->createDir($root_path);
        }
        if($config->activate_herachical_folder_structure) {
            if ($path != "") {
                $category_path = $root_path . "/" . $path;
                if (!$filesystem->has($category_path)) {
                    $filesystem->createDir($category_path);
                }
            } else {
                $category_path = $root_path;
            }
        }else{
            $category_path = $root_path;
        }

		if ($category_path)
		{
			$filePath = $category_path . '/' . $fileName;
		}
		else
		{
			$filePath = $fileName;
		}
		$filesystem = $this->getFileSystem();

		if ($filesystem->has($filePath))
		{
			//$filesystem->updateStream($filePath, $stream);
            //get file size
            $filesize = $filesystem->getSize($filePath);
		}
		else
		{
			//$filesystem->writeStream($filePath, $stream);
            $filesize = $filesystem->getSize($filePath);
		}
       
		return $filesize;
	}

	/**
	 * Get the document stream used for processing download
	 *
	 * @param $row
	 *
	 * @return array
	 */
	public function onGetDocumentFile($filename)
	{
        $root_path = $this->params->get('dropbox_path');
        $root_path = "edocman"."/".$root_path;
		if(substr($root_path,strlen($root_path)-1) != "/"){
            $root_path .= "/";
        }
		$filename   = $root_path.$filename;
		$filesystem = $this->getFileSystem();
		if ($filesystem->has($filename))
		{
			return array(
				'stream'            => $filesystem->readStream($filename),
				//'Content-Type'      => $filesystem->getMimetype($filename),
				'Content-Length'    => $filesystem->getSize($filename),
				'modification-date' => $filesystem->getTimestamp($filename)
			);
		}
	}

    /**
     * Remove file from DropBox
     * @param $task
     * @param $row
     */
    public function onDocumentBeforeDelete($task,$row){
        $filename = $row->filename;
        $root_path = $this->params->get('dropbox_path');
        $root_path = "edocman"."/".$root_path;
		if(substr($root_path,strlen($root_path)-1) != "/"){
            $root_path .= "/";
        }
        $filename   = $root_path.$filename;
        $filesystem = $this->getFileSystem();
        if($filesystem->has($filename)){
            $filesystem->delete($filename);
        }
    }
}