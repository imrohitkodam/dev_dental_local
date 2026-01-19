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
<div id="fd" class="is-joomla-backend">
	<div id="pp" class="pp-component pp-backend si-theme--light <?php echo FH::isJoomla4() ? 'is-loading' : '';?>" data-pp-structure data-fd-structure>

		<?php if (FH::isJoomla4()) { ?>
			<?php echo $this->fd->html('loader.block'); ?>
		<?php } ?>

		<?php if ($this->tmpl !== 'component') { ?>
			<?php echo $this->fd->html('admin.outdated', 'COM_PP_OUTDATED_VERSION', JRoute::_('index.php?option=com_payplans&tasktask=system.upgrade'), 'COM_PP_UPDATE_NOW'); ?>
		<?php } ?>

		<?php if ($this->config->get('toolbar_installation_failed')) { ?>
			<?php echo $this->fd->html('admin.notice', 'COM_PP_TOOLBAR_PACKAGE_NOT_INSTALLED', 'warning' , null, [
				'attributes' => 'data-pp-toolbar-notice',
				'dismissible' => true,
				'dismissAttribute' => 'data-pp-close-toolbarnotice'
			]); ?>
		<?php } ?>

		<?php if ($this->config->get('show_override_outdated')) { ?>
		<?php 
			$curTemplate = PP::getJoomlaTemplate();
			$overridePath = 'JOOMLA/templates/' . $curTemplate . '/html/com_payplans';
			$newOverridePath = 'JOOMLA/templates/' . $curTemplate . '/html/com_payplans.outdated';
			$docLink = 'https://stackideas.com/docs/payplans/administrators/customization/css-deprecation';
		?>
			<?php echo $this->fd->html('admin.notice', JText::sprintf('COM_PP_OUTDATED_OVERRIDE_FILES', $overridePath, $newOverridePath, $docLink), 'warning' , null, [
				'attributes' => 'data-pp-override-notice',
				'dismissible' => true,
				'dismissAttribute' => 'data-pp-close-overridenotice'
			]); ?>
		<?php } ?>

		<!--
		<div class="pp-backend__overlay"><div class="o-loader is-active"></div></div>
		-->

		<div class="app <?php echo FH::isJoomla4() ? 't-hidden' : '';?>" data-fd-body>

			<?php if (!$isStyleguide && $this->tmpl !== 'component') { ?>
				<?php echo $sidebar; ?>
			<?php } ?>

			<div class="app-content <?php echo !$page->heading ? 'pt-no' : '';?>">
				<?php if ($this->tmpl != 'component') { ?>
					<?php echo PP::info()->html(); ?>

					<?php if ($page->heading || $page->description) { ?>
						<?php echo $this->fd->html('admin.headers', $page->heading, $page->description); ?>
					<?php } ?>
				<?php } ?>

				<div class="app-body">
					<?php echo $contents; ?>
				</div>
			</div>
		</div>

		<?php if ($help) { ?>
			<?php echo $this->fd->html('admin.toolbarHelp', $help); ?>
		<?php } ?>
		
		<?php echo $this->fd->html('admin.toolbarSaveGroup'); ?>
	</div>
</div>

<?php echo $this->fd->html('html.popover'); ?>