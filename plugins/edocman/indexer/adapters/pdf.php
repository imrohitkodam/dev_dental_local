<?php
/**
 * Get content of PDF file
 * @author Tuan-Hien
 *
 */
class PDF2Text {
		function getText($file) {			
			$os = strtoupper(substr(PHP_OS, 0, 3)) ;
			$binariesFolder = JPATH_PLUGINS.'/edocman/indexer/binaries/' ;
			switch ($os) {
				case 'WIN' :
					$command = $binariesFolder."windows/pdftotext.exe -enc UTF-8 \"$file\" -";
					$handle2 = popen($command." 2>&1", 'r');
					break ;					
				case 'LIN' :
					if (file_exists("/usr/bin/pdftotext")){
						// hopefully this will fix some of the freebsd errors.
						$handle2 = popen("/usr/bin/pdftotext -enc UTF-8 \"$file\" - 2>&1", 'r');
					}else{
						$handle2 = popen($binariesFolder. "linux/pdftotext -enc UTF-8 \"$file\" - 2>&1", 'r');
					}	
					break ;
				case 'FRE' : //BSD
					$command = $binariesFolder."bsd/pdftotext -enc UTF-8 \"$file\" -";
					$handle2 = popen($command." 2>&1", 'r');
					break ;
				case 'DAR' : //Mac OS
					$command = $binariesFolder."mac/pdftotext -enc UTF-8 \"$file\" -";
					$handle2 = popen($command." 2>&1", 'r');
					break ;		
				default :
					JFactory::getApplication()->redirect('index.php', JText::_('This kind of server is not supported'));
					break ;							
			}			
			$contents = '';
			if($handle2){
				while (!feof($handle2)) {
					set_time_limit(0);
					$contents .= fread($handle2, 8192);
					ob_flush();
				}
			}
			// removes the "Error: PDF version 1.6 -- xpdf supports version 1.5 (continuing anyway)"
			if (strpos( $contents, "Error: PDF version 1.6") === false){
				// it's ok don't do a thing.
			} else {
				$contents = substr($contents, 71);
			}
												
			return $contents ;
		}		
	}
