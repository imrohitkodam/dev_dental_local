<?php
/**
 * @package    Tjlms
 * @copyright  Copyright (C) 2005 - 2018. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE.txt
 * Shika is free software. This version may have been modified pursuant
 * to the GNU General Public License, and as distributed it includes or
 * is derivative of works licensed under the GNU General Public License or
 * other free or open source software licenses.
 */

defined('_JEXEC') or die('Restricted access');
use Joomla\CMS\Factory;
use Joomla\CMS\Uri\Uri;
use Joomla\CMS\Plugin\PluginHelper;
use Joomla\Registry\Registry;
use Joomla\CMS\Language\Text;
use Joomla\CMS\HTML\HTMLHelper;

$document = Factory::getDocument();
$document->addscript(Uri::root(true) . '/media/com_jticketing/vendors/js/jquery.countdown.min.js');
$document->addscript(Uri::root(true) . '/media/com_jticketing/js/jticketing.js');

// Give access to enter into online event
$plugin = PluginHelper::getPlugin('tjevents', 'plug_tjevents_adobeconnect');
$params = new Registry($plugin->params);

$this->beforeEventStartTime = $params->get('show_em_btn', '5');
$this->showAdobeButton      = 0;
$currentDate = Factory::getDate()->toSql();

JLoader::register('JticketingCommonHelper', JPATH_SITE . '/components/com_jticketing/helpers/common.php');

JticketingCommonHelper::getLanguageConstant();

if ($event->online_events == 1)
{
	$time     = strtotime($event->startdate);
	$time     = $time - ($this->beforeEventStartTime * 60);
	$current  = strtotime($today);
	$date     = date("Y-m-d H:i:s", $time);
	$datetime = strtotime($date);

	if ($event->created_by == $this->userid)
	{
		$eventDetails['isboughtEvent'] = 1;
	}

	if ($datetime < $current  or $this->userid == $event->created_by)
	{
		$this->showAdobeButton = 1;
	}
}

$lesson_url = $tjlmshelperObj->tjlmsRoute("index.php?option=com_tjlms&view=lesson&lesson_id=" . $lesson->id . "&tmpl=component", false);
?>
<link rel="stylesheet" type="text/css"  href="<?php echo Uri::root(true) . '/plugins/tjevent/'
. $this->_name . '/' . $this->_name . '/assets/css/jtevents.css';?>"></link>

<div class="event-count pl-15" id="event-countdown<?php echo $mediaDetails->source; ?>"></div>

<?php
if ($event->online_events == 1 && $eventDetails['isboughtEvent'] == 1 && $event->enddate > $today && $this->params->get('detail_page') == 1)
{
	if ($this->showAdobeButton == 1)
	{
	?>
		<button type="button" class="btn btn-info enable"
		id="jt-enterMeeting<?php echo $mediaDetails->source; ?>"
		data-loading-text="<i class='fa fa-spinner fa-spin '></i> Loading..">
		<?php echo Text::_('PLG_TJEVENT_MEETING_BUTTON');?>
		</button>
	<?php
	}
	elseif ($this->showAdobeButton == 0)
	{
		?>
		<span class="tool-tip" data-bs-toggle="tooltip" data-placement="top"
		title="<?php echo Text::sprintf('COM_JT_MEETING_ACCESS', $this->beforeEventStartTime);?>">
			<button class="btn btn-info com_jticketing_button" disabled="disabled"><?php echo Text::_('PLG_TJEVENT_MEETING_BUTTON');?></button>
		</span>
	<?php
	}
}

$currentDate = HTMLHelper::date($currentDate, 'Y-m-d H:i:s');
$startDate = HTMLHelper::date($event->startdate, 'Y-m-d H:i:s');
$endDate = HTMLHelper::date($event->enddate, 'Y-m-d H:i:s');

?>
<script>
	var jticketing_baseurl = "<?php echo Uri::root();?>";
	var eventId = "<?php echo $event->id; ?>";
	var currentDate ="<?php echo $currentDate;?>";
	var startDate = "<?php echo $startDate;?>";
	var endDate = "<?php echo $endDate;?>";

	jtCounter.jtCountDown("event-countdown<?php echo $mediaDetails->source; ?>", startDate, endDate, currentDate);
</script>
