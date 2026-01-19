<?php
/**
* @package      EasyBlog
* @copyright    Copyright (C) 2010 - 2019 Stack Ideas Sdn Bhd. All rights reserved.
* @license      GNU/GPL, see LICENSE.php
* EasyBlog is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/
defined('_JEXEC') or die('Unauthorized Access');
?>
<div class="eb-composer-fieldset">
	<div class="eb-composer-fieldset-header">
		<strong><?php echo JText::_('COM_EASYBLOG_BLOCKS_GENERAL_ATTRIBUTES'); ?></strong>
	</div>
	<div class="eb-composer-fieldset-content o-form-horizontal">
		<?php echo $this->html('composer.field', 'form.toggler', 'left', 'Display Left Label', true, 'data-field-left-label'); ?>
		<?php echo $this->html('composer.field', 'form.text', 'left_text', 'Left Label', JText::_('Left')); ?>

		<?php echo $this->html('composer.field', 'form.toggler', 'right', 'Display Right Label', true, 'data-field-right-label'); ?>
		<?php echo $this->html('composer.field', 'form.text', 'right_text', 'Right Label', JText::_('Right'), null, array('attr' => 'data-field-right-text')); ?>
	</div>
</div>