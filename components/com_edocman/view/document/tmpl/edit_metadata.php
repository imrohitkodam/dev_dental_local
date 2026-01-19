<?php
/**
 * @version         1.9.7
 * @package        Joomla
 * @subpackage     EDocman
 * @author         Tuan Pham Ngoc
 * @copyright      Copyright (C) 2011 - 2018 Ossolution Team
 * @license        GNU/GPL, see LICENSE.php
 */

defined('_JEXEC') or die;
?>
<ul class="adminformlist">

	<li><?php echo $this->form->getLabel('image'); ?>
	<?php echo $this->form->getInput('image'); ?></li>

	<li><?php echo $this->form->getLabel('access'); ?>
	<?php echo $this->form->getInput('access'); ?></li>

	<li><?php echo $this->form->getLabel('license'); ?>
	<?php echo $this->form->getInput('license'); ?></li>

	<li><?php echo $this->form->getLabel('published'); ?>
	<?php echo $this->form->getInput('published'); ?></li>

	<li><?php echo $this->form->getLabel('metadesc'); ?>
	<?php echo $this->form->getInput('metadesc'); ?></li>

	<li><?php echo $this->form->getLabel('metakey'); ?>
	<?php echo $this->form->getInput('metakey'); ?></li>



	<?php foreach($this->form->getGroup('metadata') as $field): ?>
		<?php if ($field->hidden): ?>
			<li><?php echo $field->input; ?></li>
		<?php else: ?>
			<li><?php echo $field->label; ?>
			<?php echo $field->input; ?></li>
		<?php endif; ?>
	<?php endforeach; ?>
</ul>
