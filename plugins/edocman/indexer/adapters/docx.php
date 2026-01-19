<?php
/**
 * @version		1.5.0
 * @package		Joomla
 * @subpackage	Doc Indexer
 * @author  Tuan Pham Ngoc
 * @copyright	Copyright (C) 2010 Ossolution Team
 * @license		GNU/GPL, see LICENSE.php
 */
class Docx2Text {
	/**
	 * Extract text from a document
	 *
	 * @param string $userDoc
	 * @return string
	 */	
	function getText($userDoc) {
		//$userDoc = str_replace('.docx', '.doc') ;
		//if (!file_exists($userDoc))
			//return '';
		/*
		$fileHandle = fopen($userDoc, "r"); 
		$line = @fread($fileHandle, filesize($userDoc)); 
		$lines = explode(chr(0x0D),$line); 
		$outtext = ""; 
		foreach($lines as $thisline) {
			$pos = strpos($thisline, chr(0x00)); 
			if (($pos !== FALSE)||(strlen($thisline)==0)) {
				$outtext .= $thisline." ";
			} else {
				$outtext .= $thisline." "; 
			}			
		}
		$outtext = preg_replace("/[^a-zA-Z0-9\s\,\.\-\n\r\t@\/\_\(\)]/","",$outtext); 
		*/
		        $striped_content = '';
        $content = '';
        $zip = zip_open($userDoc);
        if (!$zip || is_numeric($zip)) return false;
        while ($zip_entry = zip_read($zip)) {
            if (zip_entry_open($zip, $zip_entry) == FALSE) continue;
            if (zip_entry_name($zip_entry) != "word/document.xml") continue;
            $content .= zip_entry_read($zip_entry, zip_entry_filesize($zip_entry));
            zip_entry_close($zip_entry);
        }// end while
 
        zip_close($zip);
        $content = str_replace('</w:r></w:p></w:tc><w:tc>', " ", $content);
        $content = str_replace('</w:r></w:p>', "\r\n", $content);
        $outtext = strip_tags($content);
		return $outtext ;
	}
}
?>