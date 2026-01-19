<?php
/**
 * @package    Tjlms
 * @copyright  Copyright (C) 2005 - 2014. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE.txt
 * Shika is free software. This version may have been modified pursuant
 * to the GNU General Public License, and as distributed it includes or
 * is derivative of works licensed under the GNU General Public License or
 * other free or open source software licenses.
 */

defined('_JEXEC') or die('Restricted access');
use Joomla\CMS\Language\Text;
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Uri\Uri;

$subformat =!empty($lesson->media['sub_format'])?$lesson->media['sub_format']:'';
$categoryId = '';
$lessonParams['numberOfEvents'] = '';
$myEdit1 = 0;

$jtEventHelper = new JteventHelper;
$category = $jtEventHelper->getEventCategories();

// Add Select Event Category and removed All Category from $category. In event as category we don't need All category option.
$category[0] = HTMLHelper::_('select.option', "0", Text::_('PLG_TJEVENT_SELECT_EVENT_CATEGORY'));

if (!empty($subformat))
{
	$subformat_source_options = explode('.', $subformat);
	$source_plugin = $subformat_source_options[0];
	$source_option = $subformat_source_options[1];

	if (!empty($source_option) && $source_plugin == 'jtcategory')
	{
		$categoryId = $lesson->source;
		$lessonParams = !empty($lesson->media['params'])?json_decode($lesson->media['params'], true):'';
	}
}
elseif (!empty($courseLessons))
{
	foreach ($category as $key => $value)
	{
		if (in_array($value->value, $courseLessons))
		{
			$value->disable = 1;
		}
	}
}

?>
<script>
	var cat_id = "<?php echo $categoryId;?>";
	var lessonParams = '<?php echo $lessonParams['numberOfEvents']; ?>';
	var root_url = '<?php echo Uri::base(); ?>';
</script>

<div class="control-group">
	<label class="control-label" title="<?php echo Text::_("PLG_TJEVENT_JTEVENT_EVENT_CATEGORY_LBL_TITLE"); ?>">
		<?php echo Text::_("PLG_TJEVENT_JTEVENT_EVENT_CATEGORY_LBL"); ?>
	</label>
	<div class="controls">
		<?php echo JHTML::_('select.genericlist', $category, 'lesson_format[jtcategory][category]', 'class = "inputbox required"', 'value', 'text', $categoryId); ?>
		<input type="hidden" id="subformatoption" name="lesson_format[jtcategory][subformatoption]" value="category"/>
		<input type="hidden" id="coursedeatail" name="coursedeatail[jtcategory][subformatoption]" value="<?php echo $courseDetail->type; ?>"/>
		<input type="hidden" id="category_params" name="lesson_format[jtcategory][params]" value=""/>
	</div>
</div>

<div id="eventdiv" class="eventdiv<?php echo $lesson->lesson_id;?>">
	<div class="control-group">
		<label class="control-label" title="<?php echo Text::_('PLG_TJEVENTS_JTCATEGORY_EVENTS_MARK_COMPLETE_DESC'); ?>">
			<?php echo Text::_('PLG_TJEVENTS_JTCATEGORY_EVENTS_MARK_COMPLETE'); ?>
		</label>
		<div class="controls">
			<input type="number" id="complete_mark" value="<?php echo $myEdit1; ?>" name="mark_as_complete" min="1"/>
		</div>
	</div><!--control-group-->
</div><!--eventdiv-->
<script type="text/javascript" src="<?php echo Juri::root() . 'plugins/tjevent/' . $this->_name . '/' . $this->_name .'/assets/js/jtcategory.js';?>"></script>

<style>
#eventdiv{
display: none;
}
</style>