<?php
/**
 * @version         1.9.7
 * @package        Joomla
 * @subpackage     EDocman
 * @author         Tuan Pham Ngoc
 * @copyright      Copyright (C) 2011 - 2018 Ossolution Team
 * @license        GNU/GPL, see LICENSE.php
 */

// No direct access.
defined('_JEXEC') or die; ?>

<?php echo JHtml::_('sliders.panel',JText::_('JGLOBAL_FIELDSET_PUBLISHING'), 'publishing-details'); ?>

<fieldset class="panelform">
	<ul class="adminformlist">

		<li><?php echo $this->form->getLabel('created_user_id'); ?>
		<?php echo $this->form->getInput('created_user_id'); ?></li>

		<?php if (intval($this->item->created_time)) : ?>
			<li><?php echo $this->form->getLabel('created_time'); ?>
			<?php echo $this->form->getInput('created_time'); ?></li>
		<?php endif; ?>

		<?php if ($this->item->modified_user_id) : ?>
			<li><?php echo $this->form->getLabel('modified_user_id'); ?>
			<?php echo $this->form->getInput('modified_user_id'); ?></li>

			<li><?php echo $this->form->getLabel('modified_time'); ?>
			<?php echo $this->form->getInput('modified_time'); ?></li>
		<?php endif; ?>

	</ul>
</fieldset>
