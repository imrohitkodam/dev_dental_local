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
 * EParameter
 *
 * @package     Extly.Library
 * @subpackage  HTML
 * @since       11.1
 */
class EParameter
{
	/**
	 * getComponentParam.
	 *
	 * @param   string  $option   Params
	 * @param   string  $key      Params
	 * @param   string  $default  Params
	 *
	 * @return	string.
	 */
	public static function getComponentParam($option, $key, $default = null)
	{
		jimport('joomla.application.component.helper');

		if (F0FDispatcher::isCliAdmin())
		{
			$params = JComponentHelper::getParams($option);
		}
		else
		{
			$app = JFactory::getApplication();
			$params = $app->getParams($option);
		}

		return $params->get($key, $default);
	}

	/**
	 * getUserOffset
	 *
	 * @return	DateTimeZone.
	 */
	public static function getUserOffset()
	{
		$userTz = JFactory::getUser()->getParam('timezone');
		$timeZone = JFactory::getConfig()->get('offset');

		if ($userTz)
		{
			$timeZone = $userTz;
		}

		return $timeZone;
	}

	/**
	 * getTimeZone - Returns the userTime zone if the user has set one, or the global config one
	 *
	 * @return	DateTimeZone.
	 */
	public static function getTimeZone()
	{
		return new DateTimeZone(self::getUserOffset());
	}

	/**
	 * getTimeZoneOffset
	 *
	 * @return	int
	 */
	public static function getTimeZoneOffset()
	{
		$tz = self::getTimeZone();
		$date = JFactory::getDate();
		$date->setTimezone($tz);

		return $date->getOffsetFromGMT();
	}

	/**
	 * convertLocalUTC
	 *
	 * @param   string  $strdate  Params
	 *
	 * @return	string
	 */
	public static function convertLocalUTC($strdate = null)
	{
		$tz = self::getTimeZone();
		$date = JFactory::getDate($strdate, $tz);

		return $date->toSql();
	}

	/**
	 * convertUTCLocal
	 *
	 * @param   string  $strdate  Params
	 *
	 * @return	string
	 */
	public static function convertUTCLocal($strdate = null)
	{
		$tz = self::getTimeZone();
		$date = JFactory::getDate($strdate);
		$date->setTimezone($tz);

		return $date->format(JText::_('COM_AUTOTWEET_DATE_FORMAT'), true);
	}

	/**
	 * getDateTimeParts
	 *
	 * @param   string  $strdate  Params
	 *
	 * @return	string
	 */
	public static function getDateTimeParts($strdate)
	{
		return explode(' ', $strdate);
	}

	/**
	 * getDateTimeParts
	 *
	 * @param   string  $strdate  Params
	 *
	 * @return	string
	 */
	public static function getDatePart($strdate)
	{
		list($date, $time) = self::getDateTimeParts($strdate);

		return $date;
	}

	/**
	 * getDateTimeParts
	 *
	 * @param   string  $strdate  Params
	 *
	 * @return	string
	 */
	public static function getTimePart($strdate)
	{
		list($date, $time) = self::getDateTimeParts($strdate);

		return $time;
	}

	/**
	 * getLanguageSef
	 *
	 * @return	string
	 */
	public static function getLanguageSef()
	{
		$languages = JLanguageHelper::getLanguages('lang_code');
		$lang_code = JFactory::getLanguage()->getTag();

		$lang_sef = null;

		if (array_key_exists($lang_code, $languages))
		{
			$lang_sef = $languages[$lang_code]->sef;
		}

		return $lang_sef;
	}

	/**
	 * getExpiration
	 *
	 * @return	int
	 */
	public static function getExpiration()
	{
		$cachetime = JFactory::getConfig()->get('cachetime');
		$now = JFactory::getDate()->toUnix();

		return ($now - ($cachetime * 60));
	}
}
