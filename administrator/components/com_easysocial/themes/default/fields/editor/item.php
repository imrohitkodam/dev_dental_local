<?php
/**
* @package		EasySocial
* @copyright	Copyright (C) 2010 - 2016 Stack Ideas Sdn Bhd. All rights reserved.
* @license		GNU/GPL, see LICENSE.php
* EasySocial is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/
defined('_JEXEC') or die('Unauthorized Access');
?>
<div class="o-form-group control-group-custom hover-panel"  data-fields-editor-page-item data-appid="<?php echo $appid; ?>"
	<?php if (isset($fieldid)) { ?>
	data-id="<?php echo $fieldid;?>"
	<?php } ?>
>
	<div data-fields-editor-page-item-handle class="item-handle"></div>

	<div class="t-text--right custom-fields-control hover-panel-show">
		
		<div class="btn-group">
			<a href="javascript:void(0);" class="btn btn-es-default-o btn-sm" data-edit>
				<i class="fa fa-pencil"></i>
			</a>
			
			<a href="javascript:void(0);" class="btn btn-es-default-o btn-sm" data-move>
				<i class="fa fa-exchange"></i>
			</a>
		</div>

		<a href="javascript:void(0);" class="btn btn-es-danger-o btn-sm" data-delete>
			<i class="fa fa-trash"></i>
		</a>

		<div class="pull- custom-label-app">
			<i class="<?php echo $app->getParams()->get('icon') ? $app->getParams()->get('icon') : 'icon-field-' . $app->element;?>"></i>
			<span><?php echo $app->title; ?></span>
		</div>
	</div>

	<div data-fields-editor-page-item-content style="width:100%" >
		<?php if ($app->id) { ?>
			<?php echo $output; ?>
		<?php } else { ?>
			<div class="alert alert-danger"><?php echo JText::_('COM_EASYSOCIAL_FIELDS_INVALID_APP'); ?></div>
		<?php } ?>
	</div>
</div>
