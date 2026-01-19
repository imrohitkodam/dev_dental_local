<?php
/**
 * @version        1.9.7
 * @package        Joomla
 * @subpackage     EDocman
 * @author         Tuan Pham Ngoc
 * @copyright      Copyright (C) 2011 - 2018 Ossolution Team
 * @license        GNU/GPL, see LICENSE.php
 */
defined('_JEXEC') or die('');
?>
<table class="table table-striped">
    <thead>
        <tr>
            <th>
                <?php echo JText::_('EDOCMAN_TOP_DOWNLOADS'); ?>
            </th>
            <th>
                <?php echo JText::_('EDOCMAN_DOWNLOADS'); ?>
            </th>
        </tr>
    </thead>
    <tbody>
	<?php
	if (count($this->topDownloadDocuments)) 
	{
		for ($i = 0, $n = count($this->topDownloadDocuments); $i < $n; $i++) 
		{
			$row = $this->topDownloadDocuments[$i];
			$link		= JRoute::_('index.php?option=com_edocman&task=document.edit&id=' . $row->id);
		?>
			<tr>
				<td>
					<span class="editlinktip hasTip" title="<?php echo JText::_('Edit Document'); ?>::<?php echo $this->escape($row->title); ?>">
						<a href="<?php echo $link; ?>">
                            <img src="<?php echo JUri::root()?>components/com_edocman/assets/images/icons/32x32/<?php echo EDocmanHelper::getFileExtension($row)?>.png" style="width:16px;" />&nbsp;<?php echo $this->escape($row->title); ?>
						</a>
					</span>					
				</td>
				<td>
					<?php echo (int) $row->downloads; ?>
				</td>
			</tr>
	<?php
		}
	} 
	else 
	{
	?>
	<tr>
		<td colspan="2">
			<?php echo JText::_('EDOCMAN_NO_DOCUMENTS'); ?>
		</td>
	</tr>
	<?php
	}
	?>
    </tbody>
</table>