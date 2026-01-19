<?php
/**
* @package		PayPlans
* @copyright	Copyright (C) Stack Ideas Sdn Bhd. All rights reserved.
* @license		GNU/GPL, see LICENSE.php
* PayPlans is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/
defined('_JEXEC') or die('Unauthorized Access');

?>
<dialog>
	<width>800</width>
	<height>600</height>
	<selectors type="json">
	{
		"{closeButton}" : "[data-close-button]",
		"{copy}" : "[data-copy-clipboard]",
		"{clipboardMessage}": "[data-clipboard-message]"
	}
	</selectors>
	<bindings type="javascript">
	{
		"{closeButton} click": function() {
			this.parent.close();
		},

		"{copy} click": function(element) {
			var temp = PayPlans.$('<input>');
			var value = $(element).data('value');

			PayPlans.$('body').append(temp);
			temp.val(value).select();

			document.execCommand('copy');
			temp.remove();

			this.clipboardMessage().show();
			var self = this;

			this.clipboardMessage()
				.delay(1000)
				.fadeOut('slow');
		}
	}
	</bindings>
	<title><?php echo JText::_('Available Variables'); ?></title>
	<content>

		<div style="display: none;position: fixed;background: #000;bottom: 10%;left: 40%;color: #fff;padding: 5px 10px;border-radius: 20px;opacity: 0.9;" data-clipboard-message><?php echo JText::_('Copied to your clipboard');?></div>
		
		<div class="o-tab o-tab o-tab--line is-horizontal" data-fd-tabs-header>
			<?php foreach ($items as $key => $item) { ?>
				<div data-fd-tab-header-item class="o-tab__item <?php echo ($key === array_key_first($items)) ? 'is-active' : ''; ?>" >
					<a class="o-tab__link" href="#<?php echo strtolower($key);?>" data-pp-toggle="tab" class="o-tabs__link" data-fd-tab data-form-tabs="<?php echo strtolower($key);?>">
						<?php echo JText::_($key); ?>
					</a>
				</div>
				<?php if ($key === array_key_last($items) && $apps) { ?>
					<?php foreach ($apps as $app) { ?>
						<?php echo $app[0]; ?>
					<?php } ?>
				<?php } ?>
			<?php }?>
			
		</div>

		<div class="tab-content">
			<?php foreach ($items as $key => $item) { ?>
				<div class="tab-pane <?php echo ($key === array_key_first($items)) ? 'active' : ''; ?>" id="<?php echo strtolower($key);?>">
					<?php echo $this->output('admin/rewriter/dialogs/table', array('data' => $item)); ?>
				</div>
				<?php if ($key === array_key_last($items) && $apps) { ?>
					<?php foreach ($apps as $app) { ?>
						<?php echo $app[1]; ?>
					<?php } ?>
				<?php } ?>
			<?php }?>
		</div>

	</content>
	<buttons>
		<?php echo $this->fd->html('dialog.button', 'COM_PP_CLOSE_BUTTON', 'default', ['attributes' => 'data-close-button']); ?>
	</buttons>
</dialog>