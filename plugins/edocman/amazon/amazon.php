<?php
/**
 * @version        1.0
 * @package        Joomla
 * @subpackage     EDocman
 * @author         Dang Thuc Dam
 * @copyright      Copyright (C) 2011 - 2017 Ossolution Team
 * @license        GNU/GPL, see LICENSE.php
 */

defined('_JEXEC') or die('Restricted access');
require_once JPATH_ROOT . '/plugins/edocman/amazon/vendor/aws/aws-autoloader.php';
require_once JPATH_ROOT . '/components/com_edocman/helper/helper.php';

class plgEDocmanAmazon extends JPlugin
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
        $app = JFactory::getApplication();
		if (!$this->filesytem)
		{
            $accountId      = $this->params->get('accountId','');
            $access_key     = $this->params->get('access_key','');
            $secret_key     = $this->params->get('secret_key','');
			$bucketregion	= $this->params->get('bucketregion','');
            if(($access_key != "") && ($secret_key != "")){
                $otp = array();
                $otp['key'] = $access_key;
                $otp['secret'] = $secret_key;
                $otp['signature'] = 'v4';
                $otp['region'] = $bucketregion;
                $this->filesytem = Aws\S3\S3Client::factory($otp);
            }

			/*
			//another solution
			$this->filesytem = Aws\S3\S3Client::factory(array(
			  'version' => 'latest',
			  'region'  => $bucketregion,
			  'credentials' => array(
			    'key' => $access_key,
			    'secret'  => $secret_key,
			  )
			));
			*/
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
        $root_path = $this->params->get('bucketname','');
        if(substr($root_path,strlen($root_path)-1) == "/") {
            $root_path = substr($root_path, 0, strlen($root_path) - 1);
        }

        if($config->activate_herachical_folder_structure) {
            $path = strtolower($row->path);
            if ($path != "") {
                if(substr($path,0,1) == "/"){
                    $path = substr($path,1);
                }
                $path = EDocmanHelper::cleanPath($path);
                //$path = str_replace("/","",$path);
                //check if root folder is already exists
                //$result = $s3->list_objects($bucket->bucket_name, array("prefix" => $current, "delimiter" => "/", "max-keys" => 5000));
                $existed = 0;
                $result = $filesystem->getIterator('ListObjects', array(
                    'Bucket' => $root_path,
                    'Prefix' => $path.'/',
                ));

                foreach ($result as $object) {
                    if (isset($object['Key'])) {
                        $existed=1;
                        break;
                    }
                }

                if ($existed == 0) {
                    $path = EDocmanHelper::cleanPath($path);
                    $filename = $path . '/index.html';
                    $filename = EDocmanHelper::cleanPath($filename);
                    $secureFile = JPATH_ROOT.'/components/com_edocman/index.html';
                    $content = file_get_contents($secureFile);

                    // Upload an object to Amazon S3
                    $opts = array(
                        'Bucket' => $root_path,
                        'Key'    => $filename,
                        'Body'   => $content,
                        'ACL'    => 'public-read-write',
                    );

                    // Upload an object to Amazon S3
                    $result = $filesystem->putObject($opts);

                    // We can poll the object until it is accessible
                    $filesystem->waitUntil('ObjectExists', array(
                        'Bucket' => $root_path,
                        'Key'    => $filename
                    ));
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
        $root_path = $this->params->get('bucketname');
        if(substr($root_path,strlen($root_path)-1) == "/"){
            $root_path = substr($root_path,0,strlen($root_path)-1);
        }

        $filesystem = $this->getFileSystem();
        if ($path != "") {
            //$category_path = $root_path . "/" . $path;
            if($config->activate_herachical_folder_structure) { //create path if it isn't exist
                //$path = $category_path;
                if ($path != "") {
                    if(substr($path,0,1) == "/"){
                        $path = substr($path,1);
                    }
                    $path = strtolower(EDocmanHelper::cleanPath($path));
                    //$path = str_replace("/","",$path);
                    //check if root folder is already exists
                    $existed = 0;
                    $result = $filesystem->getIterator('ListObjects', array(
                        'Bucket' => $root_path,
                        'Prefix' => $path.'/',
                    ));

                    foreach ($result as $object) {
                        if (isset($object['Key'])) {
                            $existed=1;
                            break;
                        }
                    }
                    if ($existed == 0) {
                        $path = EDocmanHelper::cleanPath($path);
                        $filename = $path . '/index.html';
                        $filename = EDocmanHelper::cleanPath($filename);
                        $secureFile = JPATH_ROOT.'/components/com_edocman/index.html';
                        $content = file_get_contents($secureFile);

                        // Upload an object to Amazon S3
                        $opts = array(
                            'Bucket' => $root_path,
                            'Key'    => $filename,
                            'Body'   => $content,
                            'ACL'    => 'public-read-write',
                        );

                        // Upload an object to Amazon S3
                        $result = $filesystem->putObject($opts);

                        // We can poll the object until it is accessible
                        $filesystem->waitUntil('ObjectExists', array(
                            'Bucket' => $root_path,
                            'Key'    => $filename
                        ));
                    }
                }
				$category_path = $path;
            }
        } else {
            $category_path = $root_path;
        }
        $stream = fopen($file['tmp_name'], 'r+');

        if ($category_path)
        {
            $filePath = $category_path . '/' . $savedFilename;
            $filePath = EDocmanHelper::cleanPath($filePath);
        }

        // Upload an object to Amazon S3
        $opts = array(
            'Bucket' => $root_path,
            'Key'    => $filePath,
            'Body'   => $stream,
            'ACL'    => 'public-read-write',
        );

		$result = $filesystem->putObject($opts);

		// We can poll the object until it is accessible
		$filesystem->waitUntil('ObjectExists', array(
			'Bucket' => $root_path,
			'Key'    => $filePath
		));

        $return = array();
        $return[0] = true;
        $return[1] = $file['size'];
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
	public function onDocumentUpload($row, $isNew, $file, $path, $fileName)
	{
        $config = EDocmanHelper::getConfig();
        $filesystem = $this->getFileSystem();
        $db = JFactory::getDbo();
        if($row->id > 0) {
            $db->setQuery("Select category_id from #__edocman_document_category where document_id = '$row->id' and is_main_category = '1'");
            $category_id = $db->loadResult();
            $db->setQuery("Select * from #__edocman_categories where id = '$category_id'");
            $category = $db->loadObject();
            $path = $category->path;
        }
        $root_path = $this->params->get('bucketname');
        if(substr($root_path,strlen($root_path)-1) == "/"){
            $root_path = substr($root_path,0,strlen($root_path)-1);
        }
        $path = strtolower($path);
        if($config->activate_herachical_folder_structure) { //create path if it isn't exist
            if ($path != "") {
                if(substr($path,0,1) == "/"){
                    $path = substr($path,1);
                }
                $path = EDocmanHelper::cleanPath($path);
                $existed = 0;
                $result = $filesystem->getIterator('ListObjects', array(
                    'Bucket' => $root_path,
                    'Prefix' => $path.'/',
                ));
                foreach ($result as $object) {
                    if (isset($object['Key'])) {
                        $existed=1;
                        break;
                    }
                }
                if ($existed == 0) {
                    $path = EDocmanHelper::cleanPath($path);
                    $efilename = $path . '/index.html';
                    $efilename = EDocmanHelper::cleanPath($efilename);
                    $secureFile = JPATH_ROOT.'/components/com_edocman/index.html';
                    $content = file_get_contents($secureFile);

                    // Upload an object to Amazon S3
                    $opts = array(
                        'Bucket' => $root_path,
                        'Key'    => $efilename,
                        'Body'   => $content,
                        'ACL'    => 'public-read-write',
                    );

                    // Upload an object to Amazon S3
                    $result = $filesystem->putObject($opts);

                    // We can poll the object until it is accessible
                    $filesystem->waitUntil('ObjectExists', array(
                        'Bucket' => $root_path,
                        'Key'    => $efilename
                    ));
                }
                $category_path = $path;
            }
        } else {
            $category_path = '';
        }
        $stream = fopen($file['tmp_name'], 'r+');
        $savedFilename = $fileName;
        if ($category_path != '')
        {
            $filePath = $category_path . '/' . $savedFilename;
            $filePath = EDocmanHelper::cleanPath($filePath);
        }else{
			$filePath = $savedFilename;
            $filePath = EDocmanHelper::cleanPath($filePath);
		}

        // Upload an object to Amazon S3
        $opts = array(
            'Bucket' => $root_path,
            'Key'    => $filePath,
            'Body'   => $stream,
            'ACL'    => 'public-read-write',
        );
        $result = $filesystem->putObject($opts);

        if($row->id > 0) {
            $query = $db->getQuery(true);
            $query->clear();
            $query->update('#__edocman_documents')->set('file_size="' . $file['size'] . '"')->where('id="' . $row->id . '"');
            $db->setQuery($query);
            $db->execute();
        }

		$data['file_size'] = $file['size'];
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
        $filesize = 0;
        $config = EDocmanHelper::getConfig();
        $db = JFactory::getDbo();
		if($row->id > 0){
			$db->setQuery("Select category_id from #__edocman_document_category where document_id = '$row->id' and is_main_category = '1'");
			$category_id = $db->loadResult();
			$db->setQuery("Select * from #__edocman_categories where id = '$category_id'");
			$category = $db->loadObject();
			$path = $category->path;
		}

        $root_path = $this->params->get('bucketname');
        if(substr($root_path,strlen($root_path)-1) == "/"){
            $root_path = substr($root_path,0,strlen($root_path)-1);
        }

        $filesystem = $this->getFileSystem();

		if($config->activate_herachical_folder_structure) { //create path if it isn't exist
            if ($path != "") {
                if(substr($path,0,1) == "/"){
                    $path = substr($path,1);
                }
                $path = EDocmanHelper::cleanPath($path);
                //check if root folder is already exists
                $existed = 0;
                $result = $filesystem->getIterator('ListObjects', array(
                    'Bucket' => $root_path,
                    'Prefix' => $path.'/',
                ));

                foreach ($result as $object) {
                    if (isset($object['Key'])) {
                        $existed=1;
                        break;
                    }
                }
                if ($existed == 0) {
                    $path = EDocmanHelper::cleanPath($path);
                    $filename = $path . '/index.html';
                    $filename = EDocmanHelper::cleanPath($filename);
                    $secureFile = JPATH_ROOT.'/components/com_edocman/index.html';
                    $content = file_get_contents($secureFile);

                    // Upload an object to Amazon S3
                    $opts = array(
                        'Bucket' => $root_path,
                        'Key'    => $filename,
                        'Body'   => $content,
                        'ACL'    => 'public-read-write',
                    );

                    // Upload an object to Amazon S3
                    $result = $filesystem->putObject($opts);

                    // We can poll the object until it is accessible
                    $filesystem->waitUntil('ObjectExists', array(
                        'Bucket' => $root_path,
                        'Key'    => $filename
                    ));
                }
                $category_path = $path;
            }
        } else {
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

		$filePath = EdocmanHelper::cleanPath($filePath);

		$item = $filesystem->getObject(
			array(
				'Bucket' => $root_path,
				'Key'    => $filePath
		));

        $filesize = $item['ContentLength'];
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
		jimport('joomla.filesystem.folder');
		if(!JFolder::exists(JPATH_ROOT.'/tmp/edocman')){
			JFolder::create(JPATH_ROOT.'/tmp/edocman');
		}
        $filenameRaw = $filename;
        $root_path = $this->params->get('bucketname');
        if(substr($root_path,strlen($root_path)-1) == "/"){
            $root_path = substr($root_path,0,strlen($root_path)-1);
        }

        $filename = JPath::clean($filename, "/");
		$filenameArr = explode("/",$filename);
		if(count($filenameArr) > 1){
			$filename = "";
			for($i=0;$i<count($filenameArr)-1;$i++){
				$filename .= strtolower($filenameArr[$i])."/";
			}
			$filename .= $filenameArr[count($filenameArr)-1];
		}
        //remove first slash
        $file = preg_replace("/^\//", '', $filename);

		$filesystem = $this->getFileSystem();
		$filenamedownload = time().basename($file);
        $item = $filesystem->getObject(
            array(
                'Bucket' => $root_path,
                'Key'    => $file,
                'SaveAs' => JPATH_ROOT.'/tmp/edocman/'.$filenamedownload
            ));
        if($item['ContentLength'] > 0){
            return array(
                'stream'            => fopen(JPATH_ROOT.'/tmp/edocman/'.$filenamedownload,'r'),
                'Content-Type'      => $item['ContentType'],
                'Content-Length'    => $item['ContentLength'],
                'modification-date' => $item['']
            );
            //file exists
            /*
            header("Pragma: public");
            header("Expires: 0");
            header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
            header("Cache-Control: private",false);
            header("Content-Type: ".$item['ContentType']);
            header("Content-Disposition: attachment; filename=".basename($file).";" );
            header("Content-Transfer-Encoding: binary");
            header("Content-Length: ".$item['ContentLength']);
            //send file to browser for download.
            echo $item['body'];
            return null;
            */
        }

        /*
		if ($filesystem->has($filename))
		{
			return array(
				'stream'            => $filesystem->readStream($filename),
				'Content-Type'      => $filesystem->getMimetype($filename),
				'Content-Length'    => $filesystem->getSize($filename),
				'modification-date' => $filesystem->getTimestamp($filename)
			);
		}
        */
	}

    /**
     * Remove file from DropBox
     * @param $task
     * @param $row
     */
    public function onDocumentBeforeDelete($task,$row){
        $filename		= $row->filename;
		$filename = JPath::clean($filename, "/");
        //remove first slash
        $file = preg_replace("/^\//", '', $filename);
		$file = strtolower($file);
        $root_path		= $this->params->get('bucketname');
        if(substr($root_path,strlen($root_path)-1) == "/"){
            $root_path	= substr($root_path,0,strlen($root_path)-1);
        }
        $filesystem		= $this->getFileSystem();
		$item			= $filesystem->getObject(
            array(
                'Bucket' => $root_path,
                'Key'    => $file
            ));
        if($item['ContentLength'] > 0){
            $result		= $filesystem->deleteObject(array(
				'Bucket' => $root_path,
				'Key'    => $file
			));
        }
    }
}