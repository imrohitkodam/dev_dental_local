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

use Joomla\CMS\Factory;
use Joomla\CMS\Uri\Uri;
use Joomla\CMS\Language\Text;

$lang = Factory::getLanguage();
$extension = 'com_jticketing';
$base_dir = JPATH_SITE;
$language_tag = 'en-GB';
$reload = true;
$lang->load($extension, $base_dir, $language_tag, $reload);

$document = Factory::getDocument();
$document->addStyleSheet(Juri::root() . 'plugins/tjevent/' . $this->_name . '/' . $this->_name . '/assets/css/jtcategory.css');
$document->addStyleSheet(Uri::root() . 'media/com_jticketing/css/artificiers.css');
$document->addStyleSheet(Uri::root() . 'media/com_jticketing/css/jticketing.css');

?>

<div class="tjlms-wrapper">
 	<?php
	if ($lessonData->lesson_status == 'completed')
	{
		?>
 		<div class="center alert alert-success text-center event_con">
			<i class="fa fa-thumbs-o-up" aria-hidden="true"></i>
			<span class="center"><?php echo Text::_("PLG_TJEVENT_EVENT_THANK_YOU");?></span>
		</div>
 		<?php
	}
	else
	{
		?>
 	 	<div class="center alert alert-info event_con" >
		<?php echo Text::sprintf('PLG_JTCATEGORY_LESSON_COMPLETE_PREQUISITES', $params->numberOfEvents);?>
		</div>
 		<?php
	}
		?>


</div>

<div id="jtwrap">
	<?php
	if (empty($this->items))
	{
	?>
		<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 pull-right alert alert-info jtleft"><?php echo Text::_('COM_JT_NOT_FOUND_EVENT');?></div>
	<?php
	}
	else
	{
	?>
		<div class="row">
			<?php
				ob_start();
				//$locationView = JPATH_BASE . '/components/com_jticketing/views/events/tmpl/default_pin.php';
				$locationView = JPATH_BASE . '/plugins/tjevent/jtcategory/layouts/default_pin.php';
				include $locationView;
				$locationView = ob_get_contents();
				ob_end_clean();
				echo $locationView; ?>
		</div>
	<?php
	}
	?>
</div>
