<?php
/**
* @package		EasyBlog
* @copyright	Copyright (C) 2010 - 2019 Stack Ideas Sdn Bhd. All rights reserved.
* @license		GNU/GPL, see LICENSE.php
* EasyBlog is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/
defined('_JEXEC') or die('Unauthorized Access');

$sectionTitle = JText::_('COM_EB_BLOCK_ACCORDION_DEFAULT_TITLE');
?>
<div class="eb-composer-fieldset">
	<div class="eb-composer-fieldset-header">
		<strong><?php echo JText::_('COM_EB_BLOCKS_ACCORDION_SECTIONS'); ?></strong>
	</div>
	<div class="eb-composer-fieldset-content">
		<div class="o-form-group">
			<?php echo $this->html('grid.listbox', 'control', array($sectionTitle), array('attributes' => 'data-accordion-control', 'min' => 1, 'toggleDefault' => true, 'customHTML' => $sectionTitle)); ?>
		</div>
	</div>
</div>
