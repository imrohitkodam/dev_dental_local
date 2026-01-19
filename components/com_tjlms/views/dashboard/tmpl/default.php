<?php
defined('_JEXEC') or die('Restricted access');


jimport('joomla.html.pane');
JHTML::_('behavior.modal');
$this->tjlmsFrontendHelper = new comtjlmsHelper;
?>

<div class="techjoomla-bootstrap">
	<div class="com_tjlms_content">
		<table class="table table-bordered table-striped ">
			<thead>
				<tr>
					<th width="1%" class=center ><?php echo JText::_( 'COM_TJLMS_COURSE_LESSON' ); ?></th>
					<th width="1%" class=center><?php echo JText::_( 'COM_TJLMS_ATTEMPTS' ); ?></th>
					<th width="10%" class=center><?php echo JText::_( 'COM_TJLMS_SCORE' ); ?></th>
					<th width="10%" class=center><?php echo JText::_( 'COM_TJLMS_LESSON_STATUS' ); ?></th>
				</tr>
			</thead>
			<?php
			if(!empty($this->row))
			{
					$link =	'index.php?option=com_tjlms&view=reports&layout=attempts&tmpl=component';
					?>
					<?php

						foreach($this->row as $lesson_row)
						{
							$link .= '&lesson_id='.$lesson_row['id'];
							$detailed_attempts_report_link = $this->tjlmsFrontendHelper->tjlmsRoute($link,false);
							?>
							<tr>
								<td width="25%"><?php echo $lesson_row['name']?></td>
								<td class=center>
									<a href="<?php echo $detailed_attempts_report_link; ?>" rel="{size: {x: 700, y: 500}, handler:'iframe'}"  class="modal">
										<?php echo $lesson_row['attempts'] ?>
									</a>
								</td>
								<td class=center><?php echo $lesson_row['score'];?></td>
								<td class=center><?php echo  $lesson_row['lesson_status'];?></td>
							</tr>
							<?php /*foreach($lesson_row['attempts'] as $attempt_row){ ?>
							<tr>
								<td> </td>
								<td class=center>
										<?php echo $attempt_row->attempt?>
								</td>
								<td class=center><?php echo $attempt_row->score;?></td>
								<td class=center><?php echo $attempt_row->lesson_status;?></td>

							</tr>
							<?php } */?>
							<?php
						}?>


				<?php
			}
			else
				echo "No Data Found";
			?>
		</table>
	</div>
</div>


