<?php
/**
 * @package     Shika
 * @subpackage  com_tjlms
 *
 * @author      Techjoomla <extensions@techjoomla.com>
 * @copyright   Copyright (C) 2009 - 2020 Techjoomla. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;

use Joomla\CMS\Uri\Uri;
use Joomla\CMS\Router\Route;
use Joomla\CMS\Language\Text;
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Layout\LayoutHelper;

HTMLHelper::addIncludePath(JPATH_COMPONENT . '/helpers/html');
HTMLHelper::_('bootstrap.tooltip');
HTMLHelper::_('behavior.multiselect');
HTMLHelper::_('bootstrap.modal', 'lessonTypeModal');
HTMLHelper::_('formbehavior.chosen', '.multipleAuthors', null, array('placeholder_text_multiple' => Text::_('JOPTION_SELECT_AUTHOR')));

$listOrder     = $this->state->get('list.ordering');
$listDirn      = $this->escape($this->state->get('list.direction'));
$filter_format = $this->state->get('filter.format');
$saveOrder     = $listOrder == 'a.ordering';

if ($saveOrder)
{
	$saveOrderingUrl = 'index.php?option=com_tjlms&task=lessons.saveOrderAjax&tmpl=component';
	HTMLHelper::_('sortablelist.sortable', 'lessonsList', 'adminForm', strtolower($listDirn), $saveOrderingUrl);
}

$input = JFactory::getApplication()->input;
$cid = $input->getInt('cid');
$mid = $input->getInt('mid');
?>

<div class="<?php echo COM_TJLMS_WRAPPER_DIV; ?> tjBs3">
	<button type="button" class="close" onclick="closebackendPopup(1);" data-dismiss="modal" aria-hidden="true">×</button>
	<!-- Header Title -->
	<h2 class="mt-3"><?php echo Text::_('List of Lessons'); ?></h2>

	<!-- Instruction -->
	<p class="alert alert-info"><?php echo Text::_('Click on a lesson title to select a lesson.'); ?></p>

	<form action="<?php echo Route::_('index.php?option=com_tjlms&view=lessons&layout=existing_lesson&tmpl=component&cid=' . $cid . "&mid=" . $mid); ?>" method="post" name="adminForm" id="adminForm">
		<?php
			ob_start();
			include JPATH_BASE . '/components/com_tjlms/layouts/header.sidebar.php';
			$layoutOutput = ob_get_contents();
			ob_end_clean();
			echo $layoutOutput;

			echo LayoutHelper::render('joomla.searchtools.default', array('view' => $this));
		?>

		<div class="clearfix mb-10"> </div>

		<?php if (empty($this->items)) : ?>
			<div class="alert alert-no-items">
				<?php echo Text::_('JGLOBAL_NO_MATCHING_RESULTS'); ?>
			</div>
		<?php else : ?>

			<div class="table-responsive">
				<table class="table table-striped" id="lessonsList">
					<thead>
						<tr>
							<th><?php echo HTMLHelper::_('searchtools.sort', 'COM_TJLMS_MANAGELESSONS_NAME', 'a.title', $listDirn, $listOrder);?></th>
							<th><?php echo HTMLHelper::_('searchtools.sort', 'COM_TJLMS_MANAGELESSONS_AUTHOR', 'a.created_by', $listDirn, $listOrder);?></th>
							<th><?php echo HTMLHelper::_('searchtools.sort', 'COM_TJLMS_MANAGELESSONS_VIEW_START_DATE', 'a.start_date', $listDirn, $listOrder);?></th>
							<th><?php echo HTMLHelper::_('searchtools.sort', 'COM_TJLMS_MANAGELESSONS_VIEW_END_DATE', 'a.end_date', $listDirn, $listOrder);?></th>
							<th><?php echo HTMLHelper::_('searchtools.sort', 'COM_TJLMS_MANAGELESSONS_VIEW_LESSON_FORMAT', 'a.format', $listDirn, $listOrder);?></th>
						</tr>
					</thead>
					<tfoot>
						<?php
							if(isset($this->items[0]))
							{
								$colspan = count(get_object_vars($this->items[0]));
							}
							else
							{
								$colspan = 10;
							}
						?>
						<tr>
							<td colspan="<?php echo $colspan ?>">
								<?php echo $this->pagination->getListFooter(); ?>
							</td>
						</tr>
					</tfoot>
					<tbody>
						<?php
						foreach ($this->items as $i => $item)
						{
							$createdBy = $item->created_by;
							$createdByName = ($this->showUserOrUsername == 'name')?$item->name:$item->username;
							?>

							<tr class="row<?php echo $i % 2; ?>" >
								<td class="has-context">
									<div class="pull-left break-word">
										<input type="hidden" class="lesson_id" name="lesson_id" value="<?php echo trim($item->id) ?>" />
										<a href="javascript:void(0)" onclick="confirmLessonSelection('<?php echo  $item->id;?>', '<?php echo $cid;?>', '<?php echo $mid;?>');" title="<?php Text::_('JACTION_EDIT') ;  ?>">
											<?php echo $item->title; ?>
										</a>
									</div>
								</td>
								<td><?php echo $createdByName; ?></td>
								<td><?php echo $item->start_date; ?></td>
								<td><?php echo $item->end_date; ?></td>
								<td><?php echo $item->format; ?></td>
							</tr>

							<?php
						}
						?>
					</tbody>
				</table>
			</div>
		<?php endif; ?>

		<input type="hidden" name="task" value="" />
		<input type="hidden" name="boxchecked" value="0" />
		<?php echo HTMLHelper::_('form.token'); ?>
	</form>

	<!-- Select Lesson Type-->
	<?php
		$lessonTypeModalData = array(
			'selector' => 'selectLessonType',
			'params'   => array(
				'title' => Text::_('COM_TJLMS_MODUELS_PICK_LESSONTYPE')
			),
			'body' => $this->loadTemplate('lessontypesmodal'),
		);
		echo LayoutHelper::render('libraries.html.bootstrap.modal.main', $lessonTypeModalData);
	?>
</div>

<script>
	function confirmLessonSelection(lessonId, cid, mid) {
		if (confirm("<?php echo Text::_('COM_TJLMS_COURSE_LESSON_CONFIRM_SELECTION'); ?>")) {
			tjlmsAdmin.lesson.addToCourse(lessonId, cid, mid);
		}
	}
	
	function closebackendPopup(donotload)
	{
		if (donotload == '1')
		{
		parent.SqueezeBox.close();
		}
		else
		{
		window.parent.location.reload();
		}
	}
</script>
