<?php

/**
 * @package     Extly.Library
 * @subpackage  lib_extly - Extly Framework
 *
 * @author      Extly, CB. <team@extly.com>
 * @copyright   Copyright (C) 2007 - 2017 Extly, CB. All rights reserved.
 * @license     http://www.gnu.org/licenses/gpl-3.0.html GNU/GPL
 * @link        http://www.extly.com http://support.extly.com
 */
// No direct access
defined('_JEXEC') or die('Restricted access');

/**
 * Logger for debugging info.
 *
 * @package     Extly.Library
 * @subpackage  HTML
 * @since       11.1
 */
class ELog
{
	const LOG_LEVEL_OFF = 0;

	const LOG_MODE_LOGFILE = 0;
	const LOG_MODE_LOGFILE_SCREEN = 1;
	const LOG_MODE_SCREEN = 2;

	const LOG_FILE = 'xt-logging.log';

	// 0 = off, 1 = errors only, 2 = errors and warnings, 3 = all
	protected $level = 0;

	// 0 = to logfile only, 1 = to logfile and on screen
	protected $mode = 0;

	// Joomla JLog
	protected $logger = null;

	/**
	 * ELog
	 *
	 * @param   int  $log_level  Param
	 * @param   int  $log_mode   Param
	 */
	public function __construct($log_level = self::LOG_LEVEL_OFF, $log_mode = self::LOG_MODE_LOGFILE)
	{
		$this->level = (int) $log_level;
		$this->mode = (int) $log_mode;

		if ( ($log_level) && ($this->isFileMode()) )
		{
			$config = array(
							'text_file' => self::LOG_FILE
			);

			if (EXTLY_J3)
			{
				jimport('joomla.log.logger.formattedtext');
				$this->logger = new JLogLoggerFormattedtext($config);
			}
			else
			{
				jimport('joomla.log.loggers.formattedtext');
				$this->logger = new JLoggerFormattedText($config);
			}
		}
	}

	/**
	 * getInstance - JUST FOR COMPATIBILITY
	 *
	 * @param   int  $log_level  Param
	 * @param   int  $log_mode   Param
	 *
	 * @return	object
	 */
	public static function getInstance($log_level = self::LOG_LEVEL_OFF, $log_mode = self::LOG_MODE_LOGFILE)
	{
		return new ELog($log_level, $log_mode);
	}

	/**
	 * log
	 *
	 * @param   string  $status   Param
	 * @param   string  $comment  Param
	 * @param   object  &$data    Param
	 *
	 * @return	object
	 */
	public function log($status, $comment, &$data = null)
	{
		$log_result = false;

		if ($this->logThisStatus($status))
		{
			if ($data)
			{
				$comment .= ' - ' . print_r($data, true);
			}

			if ($this->isFileMode())
			{
				if (empty($this->logger))
				{
					JFactory::getApplication()->enqueueMessage('ELog: Logger not initialized, entry not written to logfile.', 'error');
				}
				else
				{
					$entry = new JLogEntry($comment, $status);
					$this->logger->addEntry($entry);
				}
			}

			if ($this->isScreenMode())
			{
				$message = 'Extly Log: ' . htmlspecialchars($comment);
				$this->showMessage($message, $status);
			}
		}

		return $log_result;
	}

	/**
	 * isLogging
	 *
	 * @return	boolean
	 */
	public function isLogging()
	{
		return $this->level;
	}

	/**
	 * isFileMode()
	 *
	 * @return	boolean
	 */
	public function isFileMode()
	{
		return ( ($this->mode == self::LOG_MODE_LOGFILE)
				|| ($this->mode == self::LOG_MODE_LOGFILE_SCREEN) );
	}

	/**
	 * isScreenMode()
	 *
	 * @return	boolean
	 */
	public function isScreenMode()
	{
		return ( ($this->mode == self::LOG_MODE_SCREEN)
				|| ($this->mode == self::LOG_MODE_LOGFILE_SCREEN) );
	}

	/**
	 * getLoggedFile
	 *
	 * @return	boolean
	 */
	public static function getLoggedFile()
	{
		$log_path = JFactory::getConfig()->get('log_path');

		// Build the full path to the log file.
		$path = $log_path . '/' . self::LOG_FILE;

		if (file_exists($path))
		{
			return $path;
		}
		else
		{
			return null;
		}
	}

	/**
	 * getLoggedUrl
	 *
	 * @return	string
	 */
	public static function getLoggedUrl()
	{
		if ($path = self::getLoggedFile())
		{
			return str_replace(JPATH_ROOT . '/', JUri::root(), $path);
		}
		else
		{
			return null;
		}
	}

	/**
	 * showMessage
	 *
	 * @param   string  $message  Param
	 * @param   int     $class    Param
	 *
	 * @return	string
	 */
	public static function showMessage($message, $class = JLog::ERROR)
	{
		$class = self::getLogClass($class);

		if (defined('EXTLY_CRONJOB_RUNNING'))
		{
			fwrite(STDOUT, $class . ' - ' . $message . "\n");
		}
		else
		{
			JFactory::getApplication()->enqueueMessage(JText::_($message), $class);
		}
	}

	/**
	 * logThisStatus
	 *
	 * @param   int  $status  Param
	 *
	 * @return	bool
	 */
	protected function logThisStatus($status)
	{
		return (($this->level) && ($status <= (int) $this->level));
	}

	/**
	 * getLogClass
	 *
	 * @param   int  $status  Param
	 *
	 * @return	string
	 */
	protected static function getLogClass($status)
	{
		switch ($status)
		{
			case JLog::INFO:
				return 'notice';
			case JLog::WARNING:
				return 'warning';
			case JLog::ERROR:
				return 'error';
			default:
				return 'message';
		}
	}
}
