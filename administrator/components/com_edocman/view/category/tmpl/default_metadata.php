<?php
/**
 * @version        1.9.7
 * @package        Joomla
 * @subpackage     EDocman
 * @author         Tuan Pham Ngoc
 * @copyright      Copyright (C) 2011 - 2018 Ossolution Team
 * @license        GNU/GPL, see LICENSE.php
 */

defined('_JEXEC') or die;
?>
<table width="100%" class="adminform">
	<tr>
		<td class="key">
			<?php echo $this->form->getLabel('metadesc'); ?>
		</td>
		<td>
			<?php echo $this->form->getInput('metadesc'); ?>
		</td>
	</tr>
	<tr>
		<td class="key">
			<?php echo $this->form->getLabel('metakey'); ?>
		</td>
		<td>
			<?php echo $this->form->getInput('metakey'); ?>
		</td>
	</tr>
	<?php foreach($this->form->getGroup('metadata') as $field): ?>
		<?php if ($field->hidden): ?>
			<tr>
				<td colspan="2">
					<?php echo $field->input; ?>
				</td>
			</tr>					
		<?php else: ?>
			<tr>
				<td class="key">
					<?php echo $field->label; ?>
				</td>
				<td>
					<?php echo $field->input; ?>
				</td>
			</tr>			
		<?php endif; ?>
	<?php endforeach; ?>
</table>
