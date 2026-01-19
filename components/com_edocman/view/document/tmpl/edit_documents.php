<?php
/**
 * @version        1.11.0
 * @package        Joomla
 * @subpackage     EDocman
 * @author         Tuan Pham Ngoc
 * @copyright	   Copyright (C) 2011 - 2018 Ossolution Team
 * @license        GNU/GPL, see LICENSE.php
 */
defined('_JEXEC') or die;
$bootstrapHelper   = $this->bootstrapHelper;
$rowFluidClass     = $bootstrapHelper->getClassMapping('row-fluid');
$span12Class       = $bootstrapHelper->getClassMapping('span12');
$span6Class        = $bootstrapHelper->getClassMapping('span6');
?>
<form action="<?php echo JRoute::_('index.php?option=com_edocman&task=upload.store_documents'); ?>" method="post" name="adminForm" id="adminForm">
    <div class="<?php echo $rowFluidClass?>">
        <div class="<?php echo $span6Class?>">
            <?php
            $heading = JText::_('EDOCMAN_EDIT_UPLOADED_DOCUMENTS_INFORMATION');
            ?>
            <h1 class="edocman-page-heading"><?php echo $heading; ?></h1>
        </div>
        <div class="<?php echo $span6Class?> toolbarbuttons">
            <a href="<?php echo JUri::root()?>" class="btn btn-warning" title="<?php echo JText::_('EDOCMAN_CANCEL_UPLOAD');?>"><?php echo JText::_('EDOCMAN_CANCEL'); ?></a>
            <input type="submit" class="btn btn-success" value="<?php echo JText::_('EDOCMAN_SAVE'); ?>"  />
        </div>
    </div>
	<table class="adminlist table-stripped edit_documents">
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
	<input type="hidden" name="category_id"     value="<?php echo $this->category_id; ?>" />
	<input type="hidden" name="task"            value="upload.store_documents" />
	<?php echo JHtml::_('form.token'); ?>
</form>