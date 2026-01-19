<?php
/**
 * @version    SVN: <svn_id>
 * @package    Quick2cart
 * @author     Techjoomla <extensions@techjoomla.com>
 * @copyright  Copyright (c) 2009-2015 TechJoomla. All rights reserved.
 * @license    GNU General Public License version 2 or later.
 */

// No direct access.
defined('_JEXEC') or die();

define('GRADESCOES', "1");
define('GRADEHIGHEST', "2");
define('GRADEAVERAGE', "3");
define('GRADESUM', "4");

$params = JComponentHelper::getParams('com_tjlms');

// Global icon constants.
if (JVERSION >= '3.0')
{
	define('LMS_DASHBORD_ICON_ORDERS', "icon-credit");
	define('LMS_DASHBORD_ICON_ITEMS', "icon-bars");
	define('LMS_DASHBORD_ICON_SALES', "icon-chart");
	define('LMS_DASHBORD_ICON_AVG_ORDER', "icon-credit");
	define('LMS_DASHBORD_ICON_ALL_SALES', "icon-chart");
	define('LMS_DASHBORD_ICON_USERS', "icon-users");
	define('LMS_DASHBORD_ICON_COURSE', "icon-book");
	define('LMS_DASHBORD_ICON_COURSE_COMPLETE', "icon-ok");
	define('LMS_DASHBORD_ICON_REVENUE', "icon-briefcase");


	define('TJFIELD_COURSTYPE', $params->get('course_type'));
	define('TJFIELD_BASED_ON', $params->get('based_on'));
	define('TJFIELD_CPD_HOURS', $params->get('cpd_hours'));
	define('TJFIELD_GDC_PRINCIPLE', $params->get('gdc_principle'));
	define('TJFIELD_GDC_HIGHLY_RECOMMENDED_SUBJECT', $params->get('gdc_highly_recommended_subject'));
	define('TJFIELD_TARGET_AUD', $params->get('target_audience'));
	define('TJFIELD_AIMS', $params->get('aims'));
	define('TJFIELD_GDC_RECOMMENDED_TOPIC', $params->get('gdc_recommended_topics'));
	define('TJFIELD_PRESENTER_ONE', $params->get('presenter1'));
	define('TJFIELD_PRESENTER_TWO', $params->get('presenter2'));
	define('TJFIELD_PRESENTER_THREE', $params->get('presenter3'));
	define('TJFIELD_GDC_OBJECTIVE', $params->get('gdc_objective'));
	define('TJFIELD_OUTCOME', $params->get('outcome'));
	define('TJFIELD_REFLECTION', $params->get('reflection'));
	define('TJFIELD_DATE', $params->get('date'));
	define('TJFIELD_TIME', $params->get('time'));
	define('TJFIELD_CONTENT', $params->get('content'));
	define('TJFIELD_DEVELOPMENT_OUTCOMES', $params->get('development_outcomes'));

	// ES Dental Professional- Job title field name
	define('ES_JOB_FIELD_NAME', "tdces_job-title");
	define('ES_GDC_PRN', "tdces_prof-reg");
}
else
{
	define('LMS_DASHBORD_ICON_ORDERS', "icon-shopping-cart");
	define('LMS_DASHBORD_ICON_ITEMS', "icon-gift");
	define('LMS_DASHBORD_ICON_SALES', "icon-briefcase");
	define('LMS_DASHBORD_ICON_AVG_ORDER', "icon-th-large");
	define('LMS_DASHBORD_ICON_ALL_SALES', "icon-briefcase");
	define('LMS_DASHBORD_ICON_USERS', "icon-user");
	define('LMS_DASHBORD_ICON_COURSE', "icon-book");
	define('LMS_DASHBORD_ICON_COURSE_COMPLETE', "icon-ok");
	define('LMS_DASHBORD_ICON_REVENUE', "icon-briefcase");
}
