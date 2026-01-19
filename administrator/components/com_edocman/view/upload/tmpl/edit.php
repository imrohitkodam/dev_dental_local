<?php
/**
 * @version        1.9.10
 * @package        Joomla
 * @subpackage     EDocman
 * @author         Tuan Pham Ngoc
 * @copyright	   Copyright (C) 2011 - 2018 Ossolution Team
 * @license        GNU/GPL, see LICENSE.php
 */
defined('_JEXEC') or die;
JToolBarHelper::title(JText::_( 'EDOCMAN_BATCH_UPLOAD_STEP_2' ));
JToolBarHelper::save('store_documents', 'Finish'); 
?>
<form action="<?php echo JRoute::_('index.php?option=com_edocman&view=upload&layout=edit'); ?>" method="post" name="adminForm" id="adminForm">
	<table class="adminlist table table-stripped">
		<thead>
			<tr>
				<th>
					<?php echo JText::_('EDOCMAN_FILE'); ?>
				</th>
				<th>
					<?php echo JText::_('EDOCMAN_TITLE'); ?>
				</th>
				<th>
					<?php echo JText::_('EDOCMAN_DESCRIPTION'); ?>
				</th>
			</tr>
		</thead>
		<tbody>
			<?php 
				$files              = $this->files;
				$originalFiles      = $this->originalFiles;
                $filesizes          = $this->filesizes;
                $fileid             = $this->fileid;
				for ($i = 0, $n = count($files) ; $i < $n; $i++)				
				{			
					$originalFile   = $originalFiles[$i];
					$file           = $files[$i];
                    $filesize       = $filesizes[$i];
                    $fileid         = $fileid[$i];
				?>
					<tr>
						<td>
							<?php echo $originalFiles[$i]; ?>
							<input type="hidden" name="file[]" value="<?php echo $file; ?>" />
							<input type="hidden" name="original_file[]" value="<?php echo $originalFile; ?>" />
                            <input type="hidden" name="filesize[]" value="<?php echo $filesize; ?>" />
                            <input type="hidden" name="fileid[]" value="<?php echo $fileid; ?>" />
						</td>
						<td>
							<input type="text" name="title[]" value="<?php echo $originalFile; ?>" class="input-large" />
						</td>
						<td>
							<textarea rows="5" cols="60" name="description[]"></textarea>
						</td>
					</tr>
				<?php
				}
			?>
		</tbody>
	</table>
	<input type="hidden" name="category_id"     value="<?php echo $this->state->category_id; ?>" />
	<input type="hidden" name="published"       value="<?php echo $this->state->published; ?>" />
    <input type="hidden" name="accesspicker"    value="<?php echo $this->state->accesspicker; ?>" />
	<input type="hidden" name="access"          value="<?php echo $this->state->access; ?>" />
    <input type="hidden" name="groups"          value="<?php echo implode(",",array_filter($this->state->groups)); ?>" />
	<input type="hidden" name="task"            value="" />
	<?php echo JHtml::_('form.token'); ?>
</form>