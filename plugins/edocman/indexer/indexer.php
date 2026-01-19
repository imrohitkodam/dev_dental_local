<?php
/**
 * @version		1.0
 * @package		Joomla
 * @subpackage	Event Booking
 * @author  Tuan Pham Ngoc
 * @copyright	Copyright (C) 2010 Ossolution Team
 * @license		GNU/GPL, see LICENSE.php
 */
defined( '_JEXEC' ) or die( 'Restricted access' );
class plgEDocmanIndexer extends JPlugin
{	
	public function __construct(& $subject, $config)
	{
		parent::__construct($subject, $config);		
	}
	/**
	 * Index EDocman document
	 * @param  $context
	 * @param Object $row Document object
	 * @param Boolean $isNew
	 */		
	public function onDocumentAfterSave($context, $row, $isNew) {
		$config = EDocmanHelper::getConfig() ;
		if (file_exists($config->documents_path.'/'.$row->filename)) {
			jimport('joomla.filesystem.file');
			$ext = strtolower(JFile::getExt($row->filename)) ;
			if ($ext == 'pdf' || $ext == 'doc' || $ext == 'docx') 
			{
				require_once dirname(__FILE__).'/adapter.php' ;
				$content = IndexerAdapter::getText($config->documents_path.'/'.$row->filename);					
				$row->indexed_content = $content ;
				$row->store();
			}
		}		
	}
}	