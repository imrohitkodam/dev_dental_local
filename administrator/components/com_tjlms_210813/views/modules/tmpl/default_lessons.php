<?php
/**
 * @version    SVN: <svn_id>
 * @package    Tjlms
 * @author     Techjoomla <extensions@techjoomla.com>
 * @copyright  Copyright (c) 2009-2015 TechJoomla. All rights reserved.
 * @license    GNU General Public License version 2 or later.
 */

// No direct access.
defined('_JEXEC') or die;
// Depends on jQuery UI
JHtml::_('jquery.ui', array('core', 'sortable'));
JHtml::_('bootstrap.modal', 'lessonTypeModal');


$input = JFactory::getApplication()->input;

foreach($this->moduleData as $moduleData)
{
	$modUnpubClass = '';

	if ($moduleData->state != 1)
	{
		$modUnpubClass = 'modUnpublish';
	}
?>
	<!--Here the LI represents all modules in a particular course-->
	<li id="modlist_<?php	echo	$moduleData->id;	?>" class=" module_lessons mod_outer mb-20" data-js-id='course-module' data-js-itemid="<?php echo	$moduleData->id;?>">
		<div class="row-fluid tjlms_module <?php echo $modUnpubClass; ?>" >
			<div class="content-li   span10">
				<i class="icon-menu moduleSortingHandler" title="<?php echo JText::_('COM_TJLMS_SORT_MODULE'); ?>"></i>
				<i class="icon-book icon-white"></i><span class="tjlms_module_title"><?php	echo $moduleData->name;	?></span>
			</div>
			<div class="tjlms-actions btn-group non-accordian span2" >
				<div class="module-functionality-icons row-fluid" >
					<input type="hidden" data-js-id="module-state" value="<?php echo $moduleData->state;?>"/>
					<!--STATE MODULE BUTTON-->
					<a class="tjlms_module__changestate hide" data-js-id="change-module-state" title="<?php echo ($moduleData->state == 0) ? JText::_('COM_TJLMS_MODULE_CHANGE_STATE_PUBLISH') : JText::_('COM_TJLMS_MODULE_CHANGE_STATE_UNPUBLISH'); ?>">
						<i class="<?php echo ($moduleData->state == 1) ? 'icon-publish' : 'icon-unpublish';?>"></i>
					</a>

					<!--EDIT MODULE BUTTON-->
					<a class="tjlms_module__edit hide" title="<?php echo JText::_('COM_TJLMS_EDIT_MODULE'); ?>" data-js-id="edit-module">
						<span class="icon-edit"></span>
					</a>

					<!--DELETE MODULE BUTTON-->
					<a class="tjlms_module__delete hide" data-js-id="delete-module" title="<?php echo JText::_('COM_TJLMS_MODULE_DELET'); ?>">
						<span class="icon-trash"></span>
					</a>

				</div>
			</div>
		</div>
		<div class="module-edit-form editing" data-js-id="edit-module-form">
			<?php
				$moduleHtml='';
				$modId = $moduleData->id;
				$modName = $moduleData->name;
				$modState = $moduleData->state;
				$modImage = $moduleData->image;
				$modDescription = $moduleData->description;
				$courseId	= $this->course_id;
				$tjlmshelperObj	=	new comtjlmsHelper();
				$layout = $tjlmshelperObj->getViewpath('com_tjlms','modules','module','ADMIN','ADMIN');
				ob_start();
				include($layout);
				$moduleHtml.= ob_get_contents();
				ob_end_clean();
				echo $moduleHtml;
			?>
		</div>
		<!--UL containing all lessons of a modules-->
		<ul id="curriculum-lesson-ul_<?php echo $moduleData->id; ?>" class="LessonsInModule" data-js-id='lessons_container' class="connectedSortable curriculum-lesson-ul">
			<?php
			if(!empty($moduleData->lessons))
			{
				foreach($moduleData->lessons as $m_lesson)
				{
					$m_lesson_status_class	=	'unpublished';
					if(!$m_lesson->media_id)
						$m_lesson_status_class	=	'no_content';
					else if($m_lesson->state == 1)
						$m_lesson_status_class	=	'published';
					else
						$m_lesson_status_class	=	'unpublished';

				?>
					<!--LI for lessons-->
					<li id="lessonlist_<?php echo $m_lesson->id;?>" class="curriculum-lesson-li <?php echo $m_lesson_status_class;?>" onchange="sortLessons(this.id,<?php	echo $moduleData->id;	?>)" style="padding: 0.4em;" data-js-id="module-lesson" data-js-itemid="<?php echo $m_lesson->id;?>">
						<ul class="unstyled_list non-sortable-lesson-li">
							<li class="lesson_main_li non-sortable-lesson-li">
								<i class="icon-menu lessonSortingHandler" title="<?php echo JText::_('COM_TJLMS_SORT_LESSON'); ?>"></i>
								<?php if($m_lesson->format != ''){ ?>

									<img class="" alt="<?php echo $m_lesson->format; ?>" title="<?php echo $m_lesson->format; ?>" src="<?php echo JUri::root().'media/com_tjlms/images/default/icons/'.$m_lesson->format.'.png';?>"/>

								<?php }else{ ?>

									<img class="" src="<?php echo JUri::root().'media/com_tjlms/images/default/icons/noformat.png';?>"/>

								<?php } ?>
								<span class="title"><?php echo $m_lesson->title;?></span>
								<div class="tjlms-actions" style="float:right; ">
									<?php
										if (in_array($m_lesson->format, array('quiz', 'exercise', 'feedback')))
										{
											$editLink = "index.php?option=com_tmt&view=test&layout=edit&id=" . $m_lesson->media_source . "&lid=" . $m_lesson->id . "&cid=" . $m_lesson->course_id ."&mid=" . $m_lesson->mod_id;
										}
										else
										{
											$editLink = "index.php?option=com_tjlms&view=lesson&layout=edit&id=" . $m_lesson->id . "&cid=" . $m_lesson->course_id . "&mid=" . $m_lesson->mod_id;
										}

									?>
									<!--EDIT LESSON BUTTON-->
									<a class="tjlms_lesson__edit hide" title="<?php echo JText::_('COM_TJLMS_EDIT_LESSON');?>" href="<?php echo $editLink;?>">
										<span class="icon-edit"></span>
									</a>
									<!--DELETE LESSON BUTTON-->
									<a class="tjlms_lesson__delete hide" data-js-id="delete-lesson" title="<?php if($m_lesson->format != 'tmtQuiz') echo JText::_('COM_TJLMS_LESSON_DELET'); else echo JText::_('COM_TJLMS_QUIZ_DELET'); ?>">
										<span class="icon-trash"></span>
									</a>

								</div>
								<div style="clear:both"></div>
							</li>

						</ul>

					</li>
		<?php
				}
			}
			?>

		</ul><!--UI for lessons ends-->

		<div class="row-fluid module_lessons__actions">
			<?php
				$this->cid = $this->course_id;
				$this->mid = $moduleData->id;
			?>
			<ul>
				<li class="span6">
					<span data-js-attr="add-lesson" data-toggle="modal" data-target="#lessonTypeModal<?php echo $this->mid;?>" class="action btn btn-add-lesson" title="<?php echo JText::_('COM_TJLMS_TITLE_ADD_LESSON'); ?>">
					<span class="icon-new icon-white mr-5" aria-hidden="true"></span>
					<?php echo JText::_('COM_TJLMS_TITLE_ADD_LESSON'); ?>
					</span>
				</li>

				<li class="span6">
					<span data-js-attr="add-existing-lesson" onclick="tjLmsCommon.loadPopup('index.php?option=com_tmt&view=tests&layout=modal&tmpl=component&cid=<?php echo $this->cid; ?>&mid=<?php echo $this->mid; ?>')" class="action btn btn-add-lesson" title="<?php echo JText::_('COM_TJLMS_ADD_EXISTQUIZ'); ?>">

						<span class="icon-new icon-white mr-5" aria-hidden="true"></span>
						<?php echo JText::_('COM_TJLMS_ADD_EXISTQUIZ'); ?>
					</span>
				</li>
			</ul>

			<?php
			echo JHtml::_(
					'bootstrap.renderModal',
					'lessonTypeModal' . $this->mid,
					array(
						'title'  => JText::_('COM_TJLMS_MODUELS_PICK_LESSONTYPE'),
					),
					$this->loadTemplate('lessontypesmodal')
				); ?>

		</div>
	</li><!--LI for each module ends-->
<?php
}
