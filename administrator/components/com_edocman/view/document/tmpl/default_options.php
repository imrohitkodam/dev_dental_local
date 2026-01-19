<?php
/**
 * @version        1.9.7
 * @package        Joomla
 * @subpackage     EDocman
 * @author         Tuan Pham Ngoc
 * @copyright      Copyright (C) 2011 - 2018 Ossolution Team
 * @license        GNU/GPL, see LICENSE.php
 */

// No direct access.
defined('_JEXEC') or die; ?>
<table width="100%" class="adminform">
	<tr>
		<td class="key">
			<?php echo $this->form->getLabel('publish_up'); ?>
		</td>
		<td>
			<?php echo $this->form->getInput('publish_up'); ?>
		</td>
	</tr>
	<tr>
		<td class="key">
			<?php echo $this->form->getLabel('publish_down'); ?>
		</td>
		<td>
			<?php echo $this->form->getInput('publish_down'); ?>
		</td>
	</tr>
	<tr>
		<td class="key">
			<?php echo $this->form->getLabel('created_user_id'); ?>
		</td>
		<td>
			<?php echo $this->form->getInput('created_user_id'); ?>
		</td>
	</tr>
	<?php if (intval($this->item->created_time)) : ?>
		<tr>
			<td class="key">
				<?php echo $this->form->getLabel('created_time'); ?>
			</td>
			<td>
				<?php echo $this->form->getInput('created_time'); ?>
			</td>
		</tr>	
	<?php endif; ?>
	<?php if ($this->item->modified_user_id) : ?>
		<tr>
			<td class="key">
				<?php echo $this->form->getLabel('modified_user_id'); ?>
			</td>
			<td>
				<?php echo $this->form->getInput('modified_user_id'); ?>
			</td>
		</tr>	
		<tr>
			<td class="key">
				<?php echo $this->form->getLabel('modified_time'); ?>
			</td>
			<td>
				<?php echo $this->form->getInput('modified_time'); ?>
			</td>
		</tr>				
	<?php endif; ?>
		<tr>
			<td class="key">
				<?php echo $this->form->getLabel('hits'); ?>
			</td>
			<td>
				<?php echo $this->form->getInput('hits'); ?>
			</td>
		</tr>
		<tr>
			<td class="key">
				<?php echo $this->form->getLabel('downloads'); ?>
			</td>
			<td>
				<?php echo $this->form->getInput('downloads'); ?>
			</td>
		</tr>			
</table>	
