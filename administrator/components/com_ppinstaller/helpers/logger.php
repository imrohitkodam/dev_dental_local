<?php
/**
* @copyright	Copyright (C) 2009 - 2011 Ready Bytes Software Labs Pvt. Ltd. All rights reserved.
* @license		http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
* @package		PayPlans Installer
* @contact 		shyam@readybytes.in
*/
// No direct access.
defined('_JEXEC') or die;

class PpinstallerHelperLogger {	
	/**
	 * Maintain logs
	 * @param $msg=> msg for log file
	 * @param $extra=> any other extra parameter or messages
	 */
	public static function log($msg, $extra= null) 
	{
		static $logger = null;
		$string =  PHP_EOL.date("F j, Y, g:i a")
				  .PHP_EOL."$msg".PHP_EOL;
		if(!empty($extra)) {
			$string .= var_export($extra,true).PHP_EOL;
		}
		
		if(empty($logger))
			$logger = new PpinstallerLogger();
		
		$logger->log($string);
	}
	
}

class PpinstallerLogger {
	static private $string = null;

	/** 
	 * Write into file
	 * @param $string => content 
	 * @param $filePath =>file path
	 */
	private function write($string = null, $filePath=PPINSTALLER_LOGGER_PATH) 
	{
		if(empty($string)){
			$string = self::$string;
		}

		//open file in append mode
		$fp = fopen($filePath, 'a');
		// file will acquire an exclusive lock (writer). 
		flock($fp, LOCK_EX);
		//write info file
	    fwrite($fp, $string);
		// release a lock from file
	    flock($fp, LOCK_UN);
	}
	
	public function log($msg){
		self::$string .= $msg;
	}
	
	function __destruct(){
		self::write(self::$string);
	}
}