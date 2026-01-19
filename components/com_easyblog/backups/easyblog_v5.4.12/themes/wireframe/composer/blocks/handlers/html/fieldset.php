<?php
/**
* @package      EasyBlog
* @copyright    Copyright (C) 2010 - 2020 Stack Ideas Sdn Bhd. All rights reserved.
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
        <strong><?php echo JText::_('COM_EASYBLOG_COMPOSER_BLOCKS_HTML');?></strong>
    </div>
    <div class="eb-composer-fielset-content">
        <pre data-eb-composer-blocks-html-pre></pre>
    </div>
</div>

<div class="eb-composer-fieldset">
	<div class="eb-composer-fieldset-header">
		<strong><?php echo JText::_('COM_EB_COMPOSER_BLOCKS_HTML_AMP_IMAGE_SIZE'); ?></strong>
	</div>

	<div class="eb-composer-fieldset-content o-form-horizontal">
		<?php echo $this->html('composer.field', 'composer.textbox', 'imageWidth', 'COM_EASYBLOG_COMPOSER_FIELDS_WIDTH', '', 'data-html-width'); ?>
	</div>

	<div class="eb-composer-fieldset-content o-form-horizontal">
		<?php echo $this->html('composer.field', 'composer.textbox', 'imageHeight', 'COM_EASYBLOG_COMPOSER_FIELDS_HEIGHT', '', 'data-html-height'); ?>
	</div>
</div>
