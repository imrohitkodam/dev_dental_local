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

require_once(__DIR__ . '/abstract.php');

class PPDate extends PPDateAbstract
{
	const INVOICE_FORMAT = '%A %d %b, %Y';
	const SUBSCRIPTION_PAYMENT_FORMAT = '%d %b %Y';
	const SUBSCRIPTION_PAYMENT_FORMAT_HOUR = '%d %b %Y %R%p';
	const YYYY_MM_DD_FORMAT = '%Y-%b-%d';
	const YYYY_MM_DD_FORMAT_WITHOUT_COMMA = '%Y%n%d';
	const YYYY_MM_DD_HH_MM = '%Y-%m-%d %H:%M';

	public function __construct($date = 'now', $tzOffset = null)
	{
		parent::__construct($date);

		// if its a boolean type, we need to add timzone.
		if ($tzOffset && is_bool($tzOffset)) {
			$tz = $this->getTimezone();
			$this->setTimezone($tz);
			return $this;
		}

		if ($tzOffset && $tzOffset instanceof DateTimeZone) {
			parent::__construct($date, $tzOffset);
			return $this;
		}

		if ($tzOffset && !is_bool($tzOffset)) {
			$this->setOffset($tzOffset);
			return $this;
		}

		return $this;
	}


	/**
	 * @param mixed $date optional the date this PPDate will represent.
	 * @param int $tzOffset optional the timezone $date is from
	 * 
	 * @return PPDate
	 */
	public static function factory($date = 'now', $tzOffset = null)
	{
		$newdate = new self($date, $tzOffset);
		return $newdate;
	}

	/**
	 * Method to retrieve user / site timezone.
	 *
	 * @since	4.1.0
	 * @access	public
	 */
	public function getTimezone()
	{
		jimport('joomla.form.formfield');


		$jConfig = JFactory::getConfig();

		// $user = JFactory::getUser();

		// // temporary ignore the dst in joomla 1.6
		// if ($user->id != 0) {
		// 	$userTZ = $user->getParam('timezone');
		// }

		// if (empty($userTZ)) {
		// 	$userTZ = $jConfig->get('offset');
		// }

		// for date we always follow site's timezone.
		$userTZ = $jConfig->get('offset');
		$newTZ = new DateTimeZone($userTZ);

		return $newTZ;
	}

	
	/**
	 * Expiration time should be in the format of YYMMDDHHMMSS
	 *
	 * @since	4.0.0
	 * @access	public
	 */
	public function addExpiration($expirationTime)
	{
		$timerElements = ['year', 'month', 'day', 'hour', 'minute', 'second'];
		$date = date_parse($this->toString());
		
		if ($this->_date == false) {
			return $this;
		}

		$count = count($timerElements);

		$this->_date = false;

		$testExpiryTime = (int) $expirationTime;

		if ($testExpiryTime != 0) {
			for ($i=0; $i<$count ; $i++) {
				$date[$timerElements[$i]] += intval(PPJString::substr($expirationTime, $i*2, 2), 10);
			}

			$this->_date = $this->makeUnixTime($date);

			// when initial jdate, its safer to pass in the datetime string instead of unix timestamp
			// due to the way jdate handle diffently the timezone when the param is a unix or datetime string.
			// in this case, we need to pass in datetime string.

			// $tmpDate = new JDate($this->_date);
			// parent::__construct($tmpDate->toSql());

			parent::__construct($this->_date);
		}

		return $this;
	}


	/**
	 * Taking date in array format and return unix timestamp
	 *
	 * @since	4.1.0
	 * @access	public
	 */
	private function makeUnixTime($date)
	{
		// when locale is false, we need to run the mktime in GMT mode, which is the gmmktime.
		// 1. first get the existing php timezone. 
		// 2. if there is timezone set in php, we need to use gmmktime.

		// $php_tz = date_default_timezone_get();

		// $newtimestamp = mktime($date['hour'], $date['minute'], $date['second'], $date['month'], $date['day'], $date['year']);

		// if (!$locale && $php_tz && $php_tz != 'UTC') {
		// 	$newtimestamp = gmmktime($date['hour'], $date['minute'], $date['second'], $date['month'], $date['day'], $date['year']);
		// }


		$newtimestamp = gmmktime($date['hour'], $date['minute'], $date['second'], $date['month'], $date['day'], $date['year']);
		return $newtimestamp;
	}
	
	public function subtractExpiration($expirationTime)
	{
		$timerElements = ['year', 'month', 'day', 'hour', 'minute', 'second'];
		$date = date_parse($this->toString());
		
		$count = count($timerElements);

		for ($i=0; $i<$count ; $i++) {
			//XITODO : convert to integer before adding
			$date[$timerElements[$i]] -=   PPJString::substr($expirationTime, $i*2, 2);
		}
		
		$this->_date = $this->makeUnixTime($date);

		parent::__construct($this->_date);

		return $this;
	}

	/**
	 * Returns the date with locale enabled.
	 *
	 * @since	4.1.0
	 * @access	public
	 */
	public function toDisplay($format = '%Y-%m-%d %H:%M:%S')
	{
		$format = $this->_convertStrftimeFormat($format);
		return $this->format($format, true);
	}

	/**
	 * Returns the lapsed time since NOW
	 *
	 * @since	4.0.0
	 * @access	public
	 */
	public function toLapsed()
	{
		$now = PP::date();
		$time = $now->toUnix(true) - $this->toUnix(true);

		$tokens = [
			31536000 => 'COM_PP_LAPSED_YEARS_COUNT',
			2592000 => 'COM_PP_LAPSED_MONTHS_COUNT',
			604800 => 'COM_PP_LAPSED_WEEKS_COUNT',
			86400 => 'COM_PP_LAPSED_DAYS_COUNT',
			3600 => 'COM_PP_LAPSED_HOURS_COUNT',
			60 => 'COM_PP_LAPSED_MINUTES_COUNT',
			1 => 'COM_PP_LAPSED_SECONDS_COUNT'
		];

		if ($time === 0) {
			return JText::_('COM_PP_LAPSED_NOW');
		}

		foreach ($tokens as $unit => $key) {
			if ($time < $unit) {
				continue;
			}

			$units = floor($time / $unit);

			$text = PP::string()->computeNoun($key , $units);
			$text = JText::sprintf($text , $units);

			return $text;
		}

	}
}
