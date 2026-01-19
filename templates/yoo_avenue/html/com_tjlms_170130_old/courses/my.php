<?php
/**
 * @version    SVN: <svn_id>
 * @package    Com_Tjlms
 * @copyright  Copyright (C) 2005 - 2014. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE.txt
 * Shika is free software. This version may have been modified pursuant
 * to the GNU General Public License, and as distributed it includes or
 * is derivative of works licensed under the GNU General Public License or
 * other free or open source software licenses.
 */

// No direct access
defined('_JEXEC') or die;

if (JVERSION >= '3.0')
{
	JHtml::_('bootstrap.tooltip');
	JHtml::_('behavior.multiselect');
	JHtml::_('formbehavior.chosen', 'select');
}

$document =	JFactory::getDocument();

$input = JFactory::getApplication()->input;
$defaultclass = !($input->get('course_cat','','STRING')) ? "class='catvisited'" : '';
$active_cat = '';
$course_cat = $input->get('course_cat', '', 'INT');
$filter_menu_category = $this->state->get('filter.menu_category');
$tjlmsparams = $this->tjlmsparams;

$category_listHTML = '';

$renderer	= $document->loadRenderer('module');
$modules = JModuleHelper::getModules( 'tjlms_category' );

ob_start();

foreach ($modules as $module)
{
	$category_listHTML .=  $renderer->render($module);
}

ob_get_clean();

$filters = '';
$renderer	= $document->loadRenderer('module');
$modules = JModuleHelper::getModules( 'tjlms_filters' );

ob_start();
foreach ($modules as $module)
{
	$filters =  $renderer->render($module);
}

ob_get_clean();

foreach ($this->course_cats as $ind => $cat)
{

	if (isset($course_cat) && !empty($course_cat))
	{
		if ($course_cat == $cat->value)
		{
			$catclass = "class='catvisited'" ;
			$active_cat = $cat->text;
		}
	}
	else if (isset($filter_menu_category))
	{

		if ($filter_menu_category == $cat->value)
		{
			$catclass = "class='catvisited'" ;
			$active_cat = $cat->text;
		}
	}

}
$comtjlmshelper = new comtjlmshelper();

?>
<div class="<?php echo COM_TJLMS_WRAPPER_DIV; ?> category-course com_tjlms_content">

	<form name="adminForm" id="adminForm" class="form-validate" method="post">
		<div class="row-fluid tjlms_courses_head ">

			<div class="tjlms_head_title pull-left">

				<h3><?php if ($input->get('assigned', '0' , 'INT') == 1)
				{
					echo (!empty($defaultclass))? JText::_("COM_TJLMS_ALL_ASSIGNED_COURSES"): JText::_("COM_TJLMS_ALL_ASSIGNED_COURSES") . ' : ' .$active_cat;
				}
				else
				{
					echo (!empty($defaultclass))? JText::_("COM_TJLMS_MY_ENROLLED_COURSES"): JText::_("COM_TJLMS_MY_ENROLLED_COURSES"). ' : ' .$active_cat;
				}
				?></h3>
			</div>
		</div>

		<div class="row-fluid tjlms-filters">

			<div  class="span11">
				<?php echo $filters; ?>
			</div>
			<div  class="span1">
				<?php if (JVERSION >= '3.0') : ?>
				<form name="adminForm11" id="adminForm11" class="form-validate" method="post">
						<div class="btn-group pull-right">
							<label for="limit" class="element-invisible">
								<?php echo JText::_('COM_TJLMS_SEARCH_SEARCHLIMIT_DESC'); ?>
							</label>
							<?php echo $this->pagination->getLimitBox(); ?>
						</div>
				</form>
					<?php endif; ?>
			</div>
		</div>

		<div class="row-fluid">

			<?php if($this->tjlmsparams->get('filter_alignment','','STRING') == 'left'): ?>
			<div class="span3 hidden-phone hidden-tablet">

				<!-- for category list-->
				<?php echo $category_listHTML; ?>
			</div>
			<?php endif; ?>
			<!--show courses-->

			<div class="span9 tjlms-courses">
					<!--<div id="filter-bar" class="btn-toolbar">
						<div class="filter-search btn-group pull-left">
							<input type="text" name="filter_search" id="filter_search" placeholder="<?php //echo JText::_('COM_TJLMS_FILTER_SEARCH_DESC_PRODUCTS'); ?>" value="<?php //echo $this->escape($this->state->get('filter.search')); ?>" class="hasTooltip input-medium" title="<?php //echo JText::_('COM_TJLMS_FILTER_SEARCH_DESC_PRODUCTS'); ?>" />
						</div>

						<div class="btn-group pull-left">
							<button type="submit" class="btn hasTooltip"
							title="<?php echo JText::_('JSEARCH_FILTER_SUBMIT'); ?>">
								<i class="icon-search"></i>
							</button>
							<button type="button" class="btn hasTooltip"
							title="<?php echo JText::_('JSEARCH_FILTER_CLEAR'); ?>"
							onclick="document.id('filter_search').value='';this.form.submit();">
								<i class="icon-remove"></i>
							</button>
						</div>
					</div>-->



				<?php
					$cnt = count($courses = $this->items );

					if (empty($courses))
					{
						echo '<div class="alert alert-info">';
						if(!$this->ol_user->id)
						{
								echo JText::_("COM_TJLMS_NO_DATA_FOR_GUEST");
						}
						else if($this->ifuseradmin)
						{
								echo JText::_("COM_TJLMS_NO_COURSE");
						}
						else if($this->ol_user->id)
						{
								echo JText::_("COM_TJLMS_NO_COURES_FOR_USERACCESS");
						}
						echo '</div>';
					}
				?>

				<?php

				/*to load courses in pin layout or blog layout*/
				if ($this->menuparams->get('layout_to_load','pin','STRING') == 'pin')
				{
					// REDERING FEATURED PRODUCT
					?>
					<div id="pin_container_tjlms_category" >
						<?php
						if(!empty($courses))
						{
							foreach($courses as $data)
							{
								// converting to array
								$data = (array)$data;
								$courseImg	= $this->tjlmsCoursesHelper->getCourseImage($data, 'S_');
								$path = $comtjlmshelper->getViewpath('com_tjlms', 'courses','course');
								ob_start();
								include($path);
								$html = ob_get_contents();
								ob_end_clean();
								echo $html;
							}
						}
					?>
					</div>
					<?php
				}
				else if ($this->menuparams->get('layout_to_load') == 'fixedpin')
				{
					// REDERING FEATURED PRODUCT
					?>
					<div id="pin_container_tjlms_category" >
						<?php
					foreach($courses as $data)
					{
						// converting to array
						$data = (array)$data;
						$courseImg	= $this->tjlmsCoursesHelper->getCourseImage($data,'S_');
						$path = $comtjlmshelper->getViewpath('com_tjlms', 'courses','fixedHeightPinLayout');
						ob_start();
						include($path);
						$html = ob_get_contents();
						ob_end_clean();
						echo $html;
					}
					?>
					</div>
					<?php
				}
				else
				{ ?>
					<div id="tjlms_courses_list">
						<?php echo $this->loadTemplate('list');?>
					</div>
				<?php
				}
				?>
			</div>

			<?php if($this->tjlmsparams->get('filter_alignment','','STRING') == 'right'): ?>
			<div class="span3">

				<!-- for category list-->
				<?php echo $category_listHTML; ?>
			</div>
			<?php endif; ?>
		</div>

		<div class="pager">
				<?php echo $this->pagination->getListFooter(); ?>
		</div>

		<input type="hidden" name="option" value="com_tjlms" />
		<input type="hidden" name="view" value="courses" />
		<input type="hidden" name="task" value="" />
		<input type="hidden" name="controller" value="" />

	</form>
</div>

<!-- setup pin layout script-->
<?php
$random_container = 'pin_container_tjlms_category';
?>

<script type="text/javascript">
	var pin_container_<?php echo $random_container; ?> = 'pin_container_tjlms_category'
</script>

<?php
$comtjlmshelper = new comtjlmshelper();
$view = $comtjlmshelper->getViewpath('com_tjlms', 'courses', 'pinsetup');
ob_start();
include($view);
$html = ob_get_contents();
ob_end_clean();
echo $html;
?>

