<?php
/**
 * @package     TJLms
 * @subpackage  com_shika
 *
 * @author      Techjoomla <extensions@techjoomla.com>
 * @copyright   Copyright (C) 2009 - 2020 Techjoomla. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

// No direct access
defined('_JEXEC') or die;

use Joomla\CMS\Factory;
Use Joomla\Registry\Registry;
use Joomla\CMS\Language\Text;
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Layout\LayoutHelper;
use Joomla\CMS\Helper\ModuleHelper;
use Joomla\CMS\Component\ComponentHelper;
use Joomla\CMS\MVC\Model\BaseDatabaseModel;

HTMLHelper::_('behavior.tooltip');
HTMLHelper::_('behavior.modal');

$data         = $displayData;
$app          = Factory::getApplication();
$comparams    = ComponentHelper::getParams('com_tjlms');
$courseName   = $data['title'];
$allowCreator = $comparams->get('allow_creator', 0);
$user         = Factory::getUser();

BaseDatabaseModel::addIncludePath(JPATH_ADMINISTRATOR . '/components/com_tjlms/models');
$tjlmsModelcourse = BaseDatabaseModel::getInstance('course', 'TjlmsModel', array('ignore_request' => true));
$enrollmentData   = $tjlmsModelcourse->enrollmentStatus((object) $data);
$data             = array_merge((array) $data, (array) $enrollmentData);
$currentDateTime  = Factory::getDate()->toSql();

$template    = $app->getTemplate(true)->template;
$override    = JPATH_SITE . '/templates/' . $template . '/html/layouts/com_tjlms/course/';
$enrolBtnText = Tjlms::Utilities()->enrolBtnText($data['start_date']);

$menuitem   = $app->getMenu()->getActive(); // get the active item
$menuParams = $menuitem->params;

$module       = ModuleHelper::getModule('mod_lms_course_display');
$moduleParams = new Registry($module->params);
?>

<div class="<?php echo $data['pinclass'];?> tjlmspin">
	<div class="thumbnail p-0 br-0 tjlmspin__thumbnail">
		<!--COURSE IMAGE PART-->
		<?php if ($data['start_date'] <= $currentDateTime) { ?>
			<a href="<?php echo  $data['url']; ?>"  class="center">
		<?php } ?>
			<div class="bg-contain bg-repn" title="<?php echo $this->escape($courseName); ?>" style="background:url('<?php echo $data['image'];?>'); background-position: center center; background-size: cover; background-repeat: no-repeat;">
				<!-- Course category -->
				<?php  if ($menuParams['pin_view_category'] || $moduleParams->get('pin_view_category')) {?>
					<span class="tjlmspin__position tjlmspin__cat"><?php echo $this->escape($data['cat']); ?></span>
				<?php } ?>
				<img class='tjlms_pin_image' style="visibility:hidden" src="<?php echo $data['image'];?>" alt="<?php echo  Text::_('TJLMS_IMG_NOT_FOUND') ?>" title="<?php echo $this->escape($courseName); ?>" />
			</div>
		<?php if ($data['start_date'] <= $currentDateTime) { ?>
			</a>
		<?php } ?>

		<!-- Course tags -->
		<?php if ($menuParams['pin_view_tags'] || $moduleParams->get('pin_view_tags'))
		{
			if (!empty($data['course_tags'])) {

				if (JFile::exists($override . 'coursepintags.php'))
				{
					echo LayoutHelper::render('com_tjlms.course.coursepintags', $data);
				}
				else
				{
					echo LayoutHelper::render('course.coursepintags', $data, JPATH_SITE . '/components/com_tjlms/layouts');
				}
			}
		}?>

		<div class="caption tjlmspin__caption">
			<h4 class="tjlmspin__caption_title text-truncate">
				<?php if ($data['start_date'] <= $currentDateTime){ ?>
					<a title="<?php echo $this->escape($courseName); ?>" href="<?php echo  $data['url']; ?>">
						<?php echo $this->escape($courseName); ?>
					</a>
				<?php }
				else {?>
					<div title="<?php echo $this->escape($courseName); ?>">
						<?php echo $this->escape($courseName); ?>
					</div>
				<?php } ?>
			</h4>

			<small class="tjlmspin__caption_desc">
			<?php

			$short_desc_char = $comparams->get('pin_short_desc_char', 50);

			if(strlen($data['short_desc']) >= $short_desc_char)
				echo substr($data['short_desc'], 0, $short_desc_char).'...';
			else
				echo $data['short_desc'];
			?>
			</small>

		</div>
	</div>
</div>
