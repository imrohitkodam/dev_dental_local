<?php
/**
* @package		PayPlans
* @copyright	Copyright (C) Stack Ideas Sdn Bhd. All rights reserved.
* @license		GNU/GPL, see LICENSE.php
* PayPlans is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/
defined('_JEXEC') or die('Unauthorized Access');

class PPDateAbstract
{
	var $_date = false;
	var $_offset = 0;

	public $date = null;

	public function __get($key)
	{
		if (isset($this->date->$key)) {
			return $this->date->$key;
		}
	}

	public function __call($method, $args)
	{
		return call_user_func_array([$this->date, $method], $args);
	}

	/**
	 * Creates a new instance of JDate representing a given date.
	 *
	 * Accepts RFC 822, ISO 8601 date formats as well as unix time stamps.
	 * If not specified, the current date and time is used.
	 *
	 * @param mixed $date optional the date this JDate will represent.
	 * @param int $tzOffset optional the timezone $date is from
	 */
	public function __construct($date = 'now', $tzOffset = null)
	{
		$this->date = new JDate($date, $tzOffset);

		if ($date === 'now' || empty($date)) {
			$this->_date = strtotime(gmdate("M d Y H:i:s", time()));
			return;
		}

		if ($tzOffset instanceof DateTimeZone) {
			$offset = $tzOffset->getOffset($this->date);
			$tzOffset = $offset / 3600;
		}

		$tzOffset *= 3600;

		if (is_numeric($date)) {
			$this->_date = $date - $tzOffset;
			return;
		}

		if (preg_match('~(?:(?:Mon|Tue|Wed|Thu|Fri|Sat|Sun),\\s+)?(\\d{1,2})\\s+([a-zA-Z]{3})\\s+(\\d{4})\\s+(\\d{2}):(\\d{2}):(\\d{2})\\s+(.*)~i',$date,$matches)) {
			$months = Array(
				'jan' => 1, 'feb' => 2, 'mar' => 3, 'apr' => 4,
				'may' => 5, 'jun' => 6, 'jul' => 7, 'aug' => 8,
				'sep' => 9, 'oct' => 10, 'nov' => 11, 'dec' => 12
			);
			$matches[2] = strtolower($matches[2]);
			if (! isset($months[$matches[2]])) {
				return;
			}
			$this->_date = mktime(
				$matches[4], $matches[5], $matches[6],
				$months[$matches[2]], $matches[1], $matches[3]
			);
			if ($this->_date === false) {
				return;
			}

			if ($matches[7][0] === '+') {
				$tzOffset = 3600 * substr($matches[7], 1, 2)
					+ 60 * substr($matches[7], -2);
			} elseif ($matches[7][0] === '-') {
				$tzOffset = -3600 * substr($matches[7], 1, 2)
					- 60 * substr($matches[7], -2);
			} else {
				if (strlen($matches[7]) === 1) {
					$oneHour = 3600;
					$ord = ord($matches[7]);
					if ($ord < ord('M')) {
						$tzOffset = (ord('A') - $ord - 1) * $oneHour;
					} elseif ($ord >= ord('M') && $matches[7] !== 'Z') {
						$tzOffset = ($ord - ord('M')) * $oneHour;
					} elseif ($matches[7] == 'Z') {
						$tzOffset = 0;
					}
				}
				switch ($matches[7]) {
					case 'UT':
					case 'GMT': $tzOffset = 0;
				}
			}
			$this->_date -= $tzOffset;
			return;
		}

		if (preg_match('~(\\d{4})-(\\d{2})-(\\d{2})[T\s](\\d{2}):(\\d{2}):(\\d{2})(.*)~', $date, $matches)) {
			$this->_date = mktime(
				$matches[4], $matches[5], $matches[6],
				$matches[2], $matches[3], $matches[1]
			);
			if ($this->_date === false) {
				return;
			}
			if (isset($matches[7][0])) {
				if ($matches[7][0] === '+' || $matches[7][0] === '-') {
					$tzOffset = 60 * (
						substr($matches[7], 0, 3) * 60 + substr($matches[7], -2)
					);
				} elseif ($matches[7] === 'Z') {
					$tzOffset = 0;
				}
			}
			$this->_date -= $tzOffset;
			return;
		}
		$this->_date = (strtotime($date) === -1) ? false : strtotime($date);
		if ($this->_date) {
			$this->_date -= $tzOffset;
		}
	}

	/**
	 * Set the date offset (in hours)
	 *
	 * @access public
	 * @param float The offset in hours
	 */
	public function setOffset($offset) {
		$this->_offset = 3600 * $offset;
	}

	/**
	 * Get the date offset (in hours)
	 *
	 * @access public
	 * @return integer
	 */
	public function getOffset() {
		return ((float) $this->_offset) / 3600.0;
	}

	/**
	 * Gets the date as an RFC 822 date.
	 *
	 * @return a date in RFC 822 format
	 * @link http://www.ietf.org/rfc/rfc2822.txt?number=2822 IETF RFC 2822
	 * (replaces RFC 822)
	 */
	public function toRFC822($local = false)
	{
		$date = ($local) ? $this->_date + $this->_offset : $this->_date;
		$date = ($this->_date !== false) ? date('D, d M Y H:i:s', $date).' +0000' : null;
		return $date;
	}

	/**
	 * Gets the date as an ISO 8601 date.
	 *
	 * @return a date in ISO 8601 (RFC 3339) format
	 * @link http://www.ietf.org/rfc/rfc3339.txt?number=3339 IETF RFC 3339
	 */
	public function toISO8601($local = false)
	{
		$date   = ($local) ? $this->_date + $this->_offset : $this->_date;
		$offset = $this->getOffset();
		$offset = ($local && $this->_offset) ? sprintf("%+03d:%02d", $offset, abs(($offset-intval($offset))*60) ) : 'Z';
		$date   = ($this->_date !== false) ? date('Y-m-d\TH:i:s', $date).$offset : null;
		return $date;
	}

	/**
	 * Gets the date as in MySQL datetime format
	 *
	 * @return a date in MySQL datetime format
	 * @link http://dev.mysql.com/doc/refman/4.1/en/datetime.html MySQL DATETIME
	 * format
	 */
	public function toMySQL($local = false, $format = '')
	{
		// $date = ($local) ? $this->_date + $this->_offset : $this->_date;
		// // in 64-bit sytem timestamp is in negative range       
		// $date = ($this->_date !== false && $this->_date > 0) ? $this->_strftime($format, $date) : null;
		// return $date;

		if ($format) {
			return $this->toFormat($format, $local);
		}

		return $this->toSql($local);
	}


	// public function toSql($local = false, JDatabaseDriver $db = null)
	// {
	// 	return $this->toMySQL($local);
	// }

	/**
	 * Gets the date as UNIX time stamp.
	 *
	 * @return a date as a unix time stamp
	 */
	// public function toUnix($local = false)
	// {
	// 	$date = null;
	// 	if ($this->_date !== false) {
	// 		$date = ($local) ? $this->_date + $this->_offset : $this->_date;
	// 	}
	// 	return $date;
	// }

	/**
	 * Gets the date in a specific format
	 *
	 * Returns a string formatted according to the given format. Month and weekday names and
	 * other language dependent strings respect the current locale
	 *
	 * @param string $format  The date format specification string (see {@link PHP_MANUAL#strftime})
	 * @return a date in a specific format
	 */
	public function toFormat($format = '%Y-%m-%d %H:%M:%S', $local = false)
	{
		// $date = ($this->_date !== false) ? $this->_strftime($format, $this->_date + $this->_offset) : null;
		// return $date;


		$format = $this->_convertStrftimeFormat($format);
		return $this->format($format, $local);
	}

	/**
	 * Translates needed strings in for JDate::toFormat (see {@link PHP_MANUAL#strftime})
	 *
	 * @access protected
	 * @param string $format The date format specification string (see {@link PHP_MANUAL#strftime})
	 * @param int $time Unix timestamp
	 * @return string a date in the specified format
	 */
	public function _convertStrftimeFormat($format)
	{
		$strftimeMap = [
			// day
			'%a' => 'D', // 00, Sun through Sat
			'%A' => 'l', // 01, Sunday through Saturday
			'%d' => 'd', // 02, 01 through 31
			'%e' => 'j', // 03, 1 through 31
			'%j' => 'z', // 04, 001 through 366
			'%u' => 'N', // 05, 1 for Monday through 7 for Sunday
			'%w' => 'w', // 06, 1 for Sunday through 7 for Saturday

			// week
			'%U' => 'W', // 07, Week number of the year with Sunday as the start of the week
			'%V' => 'W', // 08, ISO-8601:1988 week number of the year with Monday as the start of the week, with at least 4 weekdays as the first week
			'%W' => 'W', // 09, Week number of the year with Monday as the start of the week

			// month
			'%b' => 'M', // 10, Jan through Dec
			'%B' => 'F', // 11, January through December
			'%h' => 'M', // 12, Jan through Dec, alias of %b
			'%m' => 'm', // 13, 01 for January through 12 for December

			// year
			'%C' => '', // 14, 2 digit of the century, year divided by 100, truncated to an integer, 19 for 20th Century
			'%g' => 'y', // 15, 2 digit of the year going by ISO-8601:1988 (%V), 09 for 2009
			'%G' => 'o', // 16, 4 digit version of %g
			'%y' => 'y', // 17, 2 digit of the year
			'%Y' => 'Y', // 18, 4 digit version of %y

			// time
			'%H' => 'H', // 19, hour, 00 through 23
			'%I' => 'h', // 20, hour, 01 through 12
			'%l' => 'g', // 21, hour, 1 through 12
			'%M' => 'i', // 22, minute, 00 through 59
			'%p' => 'A', // 23, AM or PM
			'%P' => 'a', // 24, am or pm
			'%r' => 'h:i:s A', // 25, = %I:%M:%S %p, 09:34:17 PM
			'%R' => 'H:i', // 26, = %H:%M, 21:34
			'%S' => 's', // 27, second, 00 through 59
			'%T' => 'H:i:s', // 28, = %H:%M:%S, 21:34:17
			'%X' => 'H:i:s', // 29, Based on locale without date
			'%z' => 'O', // 30, Either the time zone offset from UTC or the abbreviation (depends on operating system)
			'%Z' => 'T', // 31, The time zone offset/abbreviation option NOT given by %z (depends on operating system)

			// date stamps
			'%c' => 'Y-m-d H:i:s', // 32, Date and time stamps based on locale
			'%D' => 'm/d/y', // 33, = %m/%d/%y, 02/05/09
			'%F' => 'Y-m-d', // 34, = %Y-%m-%d, 2009-02-05
			'%s' => '', // 35, Unix timestamp, same as time()
			'%x' => 'Y-m-d', // 36, Date stamps based on locale

			// misc
			'%n' => '\n', // 37, New line character \n
			'%t' => '\t', // 38, Tab character \t
			'%%' => '%'  // 39, Literal percentage character %
		];

		$dateMap = [
			// day
			'd', // 01, 01 through 31
			'D', // 02, Mon through Sun
			'j', // 03, 1 through 31
			'l', // 04, Sunday through Saturday
			'N', // 05, 1 for Monday through 7 for Sunday
			'S', // 06, English ordinal suffix, st, nd, rd or th
			'w', // 07, 0 for Sunday through 6 for Saturday
			'z', // 08, 0 through 365

			// week
			'W', // 09, ISO-8601 week number of the year with Monday as the start of the week

			// month
			'F', // 10, January through December
			'm', // 11, 01 through 12
			'M', // 12, Jan through Dec
			'n', // 13, 1 through 12
			't', // 14, Number of days in the month, 28 through 31

			// year
			'L', // 15, 1 for leap year, 0 otherwise
			'o', // 16, 4 digit of the ISO-8601 year number. This has the same value as Y, except that it follows ISO week number (W)
			'Y', // 17, 4 digit of the year
			'y', // 18, 2 digit of the year

			// time
			'a', // 19, am or pm
			'A', // 20, AM or PM
			'B', // 21, Swatch Internet time 000 through 999
			'g', // 22, hour, 1 through 12
			'G', // 23, hour, 0 through 23
			'h', // 24, hour, 01 through 12
			'H', // 25, hour, 00 through 23
			'i', // 26, minute, 00 through 59
			's', // 27, second, 00 through 59
			'u', // 28, microsecond, date() always generate 000000

			// timezone
			'e', // 29, timezone identifier, UTC, GMT
			'I', // 30, 1 for Daylight Saving Time, 0 otherwise
			'O', // 31, +0200
			'P', // 32, +02:00
			'T', // 33, timezone abbreviation, EST, MDT
			'Z', // 34, Timezone offset in seconds, -43200 through 50400

			// full date/time
			'c', // 35, ISO-8601 date, 2004-02-12T15:19:21+00:00
			'r', // 36, RFC 2822 date, Thu, 21 Dec 2000 16:01:07 +0200
			'U'  // 37, Seconds since the Unix Epoch
		];

		foreach ($strftimeMap as $key => $value) {
			$format = str_replace( $key, $value, $format );
		}

		return $format;
	}


	/**
	 * Translates needed strings in for JDate::toFormat (see {@link PHP_MANUAL#strftime})
	 *
	 * @access protected
	 * @param string $format The date format specification string (see {@link PHP_MANUAL#strftime})
	 * @param int $time Unix timestamp
	 * @return string a date in the specified format
	 */
	public function _strftime($format, $time)
	{
		if (strpos($format, '%a') !== false) {
			$format = str_replace('%a', $this->_dayToString(date('w', $time), true), $format);
		}

		if (strpos($format, '%A') !== false) {
			$format = str_replace('%A', $this->_dayToString(date('w', $time)), $format);
		}

		if (strpos($format, '%b') !== false) {
			$format = str_replace('%b', $this->_monthToString(date('n', $time), true), $format);
		}

		if (strpos($format, '%B') !== false) {
			$format = str_replace('%B', $this->_monthToString(date('n', $time)), $format);
		}

		$date = strftime($format, $time);
		return $date;
	}

	/**
	 * Translates month number to string
	 *
	 * @access protected
	 * @param int $month The numeric month of the year
	 * @param bool $abbr Return the abreviated month string?
	 * @return string month string
	 */
	public function _monthToString($month, $abbr = false)
	{
		switch ($month)
		{
			case 1:  return $abbr ? JText::_('JANUARY_SHORT') : JText::_('JANUARY');
			case 2:  return $abbr ? JText::_('FEBRUARY_SHORT') : JText::_('FEBRUARY');
			case 3:  return $abbr ? JText::_('MARCH_SHORT') : JText::_('MARCH');
			case 4:  return $abbr ? JText::_('APRIL_SHORT') : JText::_('APRIL');
			case 5:  return $abbr ? JText::_('MAY_SHORT') : JText::_('MAY');
			case 6:  return $abbr ? JText::_('JUNE_SHORT') : JText::_('JUNE');
			case 7:  return $abbr ? JText::_('JULY_SHORT') : JText::_('JULY');
			case 8:  return $abbr ? JText::_('AUGUST_SHORT') : JText::_('AUGUST');
			case 9:  return $abbr ? JText::_('SEPTEMBER_SHORT') : JText::_('SEPTEMBER');
			case 10: return $abbr ? JText::_('OCTOBER_SHORT') : JText::_('OCTOBER');
			case 11: return $abbr ? JText::_('NOVEMBER_SHORT') : JText::_('NOVEMBER');
			case 12: return $abbr ? JText::_('DECEMBER_SHORT') : JText::_('DECEMBER');
		}
	}

	/**
	 * Translates day of week number to string
	 *
	 * @access protected
	 * @param int $day The numeric day of the week
	 * @param bool $abbr Return the abreviated day string?
	 * @return string day string
	 */
	public function _dayToString($day, $abbr = false)
	{
		switch ($day)
		{
			case 0: return $abbr ? JText::_('SUN') : JText::_('SUNDAY');
			case 1: return $abbr ? JText::_('MON') : JText::_('MONDAY');
			case 2: return $abbr ? JText::_('TUE') : JText::_('TUESDAY');
			case 3: return $abbr ? JText::_('WED') : JText::_('WEDNESDAY');
			case 4: return $abbr ? JText::_('THU') : JText::_('THURSDAY');
			case 5: return $abbr ? JText::_('FRI') : JText::_('FRIDAY');
			case 6: return $abbr ? JText::_('SAT') : JText::_('SATURDAY');
		}
	}
	
	
	public function add($time)
	{
		if (is_numeric($time)) {
			throw new Exception(JText::_('COM_PP_TIME_CAN_NOT_NUMERIC'), 500);
		}
	
		$this->_date += $time ;
		return $this;
	}
	
	public function toArray()
	{
		return $this->toMySQL();
	}

	public function toString()
	{
		return $this->toMySQL();
	}

	public function bind($timestamp)
	{
		//XITODO : check if strinf or timetamp, and work accordingly
		// gives negative number on 64-bit machine so check here for timestamp
		 if($timestamp === '0000-00-00 00:00:00'){
			$this->_date = false;
		 }
		 else{
			$this->_date = strtotime($timestamp);
		 }

		return $this;
	}
}
