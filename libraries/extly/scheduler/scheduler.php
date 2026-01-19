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
 * This is the base class for the Extly framework.
 *
 * @package     Extly.Library
 * @subpackage  lib_extly
 * @since       1.0
 */
class Scheduler
{
	/**
	 * getParser.
	 *
	 * @param   string  $unix_mhdmd  Param
	 *
	 * @return	void
	 */
	public static function getParser($unix_mhdmd = null)
	{
		JLoader::import('extly.helpers.cron_expression.FieldInterface');
		JLoader::import('extly.helpers.cron_expression.AbstractField');

		JLoader::import('extly.helpers.cron_expression.CronExpression');
		JLoader::import('extly.helpers.cron_expression.DayOfMonthField');
		JLoader::import('extly.helpers.cron_expression.DayOfWeekField');
		JLoader::import('extly.helpers.cron_expression.FieldFactory');
		JLoader::import('extly.helpers.cron_expression.HoursField');
		JLoader::import('extly.helpers.cron_expression.MinutesField');
		JLoader::import('extly.helpers.cron_expression.MonthField');
		JLoader::import('extly.helpers.cron_expression.YearField');

		return Cron\CronExpression::factory($unix_mhdmd);
	}
}
