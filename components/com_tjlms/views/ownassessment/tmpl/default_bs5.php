<?php
/**
 * @version     1.0.0
 * @package     com_tmt
 * @copyright   Copyright (C) 2013. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 * @author      Techjoomla <contact@techjoomla.com> - http://techjoomla.com
 */

defined('_JEXEC') or die;
use Joomla\CMS\Factory;
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Layout\LayoutHelper;
use Joomla\CMS\Uri\Uri;
use Joomla\CMS\Router\Route;

HTMLHelper::addIncludePath(JPATH_COMPONENT . '/helpers/html');

?>
<style>
    li::first-letter {
   /* change first letter to uppercase */
   text-transform: uppercase;
}

td::first-letter {
   /* change first letter to uppercase */
   text-transform: uppercase;
}
    </style>
<div class="tjlms-wrapper tjBs3">
    <div class="row">
        <h2><?php echo Text::_("COM_TJLMS_OWN_ASSESSMENTS")?></h2>
    </div>
    
    <form action="" method="post" name="adminForm" id="adminForm" class='form-validate'>
    <?php  ob_start();
			include JPATH_BASE . '/components/com_tjlms/layouts/header.sidebar.php';
			$layoutOutput = ob_get_contents();
			ob_end_clean();
			echo $layoutOutput;
        echo LayoutHelper::render('joomla.searchtools.default', array('view' => $this)); ?>

	    <input type="hidden" name="filter_order" value="<?php echo $listOrder; ?>" />
	    <input type="hidden" name="filter_order_Dir" value="<?php echo $listDirn; ?>" />
        <div class="container-fluid">
            <div id="ownassessment" name="ownassessment">
                 <?php if (empty($this->items)) : ?> 
				    <div class="row">
					    <div class="alert alert-no-items">
						    <?php echo Text::_('JGLOBAL_NO_MATCHING_RESULTS');?>
					    </div>
				    </div>
			    <?php else: ?> 
                <div class="row">
                    <div class="tjlms-tbl">
                        <div class="table-responsive">
                            <table class="table table-bordered tjlms-table tbl-align" width="100%">
                                <thead>
                                    <tr>
                                        <th class="center border-top-blue greyish">
                                            <?php echo Text::_("COM_TJLMS_UESR_NAME"); ?>
                                        </th> 
                                        <th class="center border-top-blue greyish">
										    <?php echo HTMLHelper::_('grid.sort',  'COM_TJLMS_COURSE_NAME_LABEL', 'c.title', $listDirn, $listOrder); ?>
									    </th>
                                        <th class="center border-top-blue greyish">
                                            <?php echo HTMLHelper::_('grid.sort',  'COM_TJLMS_LESSON_NAME', 'l.title', $listDirn, $listOrder)?>
                                        </th>
                                        <th class="center border-top-blue greyish">
                                            <?php echo HTMLHelper::_('grid.sort',  'COM_TJLMS_LESSON_TYPE', 'l.format', $listDirn,$listOrder)?>
                                        </th>
                                        <th class="center border-top-blue greyish">
                                            <?php echo HTMLHelper::_('grid.sort','COM_TJLMS_ATTEMPTS_NO', 'lt.attempt' ,$listDirn, $listOrder)?>
                                        </th>
                                        <th class="center border-top-blue greyish">
                                            <?php echo HTMLHelper::_('grid.sort','COM_TJLMS_ASSESSMENT_START_DATE', 'lt.timestart',$listDirn, $listOrder)?>
                                        </th>  
                                        <th class="center border-top-blue greyish">
                                            <?php echo HTMLHelper::_('grid.sort','COM_TJLMS_ASSESSMENT_END_DATE', 'lt.timeend', $listDirn, $listOrder)?>
                                        </th> 
                                        <th class="center border-top-blue greyish">
                                        <?php echo HTMLHelper::_('grid.sort','COM_TJLMS_LESSON_STATUS', 'lt.lesson_status', $listDirn, $listOrder)?>
                                        </th> 
                                        <th class="center border-top-blue greyish">
                                            <?php echo HTMLHelper::_('grid.sort','COM_TJLMS_SCORE','lt.score', $listDirn , $listOrder)?>
                                        </th> 
                                        <th class="center border-top-blue greyish">
                                            <?php echo Text::_("COM_TJLMS_ASSESSMENTS_ACTION")?>
                                        </th> 
                                    </tr>
                                </thead>   
                                <tbody>
                                        <?php
                                            foreach ($this->items as $review_deatils) {
                                        ?>
                                        <tr>
                                            <td class = center>
                                                <?php 
                                                    echo $review_deatils->user_name;
                                                 ?>
                                            </td>
                                            <td class = center>
                                                <?php 
                                                    echo $review_deatils->courseTitle;
                                                ?>
                                            </td>
                                            <td class = center>
									            <?php
										            echo $review_deatils->title;
									            ?>
									        </td>
                                            <td class = center>
									            <?php
										            echo $review_deatils->format;
									            ?>
									        </td>
                                            <td class = center>
									            <?php
									            	echo $review_deatils->attempt;
									            ?>
									        </td>
                                            <td class = center>
									            <?php
									            	echo $review_deatils->timestart;
									            ?>
									        </td>
                                            <td class = center>
									            <?php
									            	echo $review_deatils->timeend;
									            ?>
									        </td>
                                            <td class = center>
									            	<?php 
                                                        echo $review_deatils->lesson_status;
                                                    ?>
									        </td>
                                            <td class = center>
									            <?php
									            	echo $review_deatils->score;
									            ?>
                                            </td>
                                            <td class=center>
                                            <a class="user-score cursorpointer" target="_blank" onclick="window.open('<?php echo Route::_(Uri::root() . 'index.php?option=com_tmt&view=answersheet&tmpl=component&adminKey=' . $this->adminKey . '&id=' . $review_deatils->test_id . '&ltId=' . $review_deatils->lessonTrackId . '&candid_id=' . $review_deatils->user_id . '&isAdmin=0', false) ?>', 'mywin', 'left=20, top=20, width=1200, height=800, toolbar=1, resizable=0');" >
                                            Report
                                            </a>
									        </td>   
                                        </tr> 
                                   <?php }?>
                                </tbody>    
                            </table>
                            <?php if (JVERSION >= '3.0'): ?>
					            <?php echo $this->pagination->getListFooter(); ?>
				            <?php else: ?>
					        <div class="pager">
						        <?php echo $this->pagination->getListFooter(); ?>
					        </div>
				            <?php endif; ?>
                        </div>
                    </div>
                </div>
                    <?php endif ;?> 
            </div>
        </div>
    </form>    
</div>

