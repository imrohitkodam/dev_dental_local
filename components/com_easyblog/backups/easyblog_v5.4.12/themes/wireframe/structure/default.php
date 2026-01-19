<?php
/**
* @package		EasyBlog
* @copyright	Copyright (C) Stack Ideas Sdn Bhd. All rights reserved.
* @license		GNU/GPL, see LICENSE.php
* EasyBlog is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/
defined('_JEXEC') or die('Unauthorized Access');
?>
<div id="eb" class="eb-component eb-<?php echo $this->theme;?> eb-view-<?php echo $view;?> eb-layout-<?php echo $layout;?> <?php echo $suffix;?>
			<?php echo $this->isIphone() ? ' is-iphone' : '';?>
			<?php echo $this->isMobile() ? ' is-mobile' : '';?>
			<?php echo $this->isTablet() ? ' is-tablet' : '';?>
			<?php echo $view == 'composer' && $this->isIpad() ? ' is-mobile' : '';?>
			<?php echo $rtl ? ' is-rtl' : '';?>
		">
	<div class="eb-container" data-eb-container>

		<div class="eb-container__main">
			<div class="eb-content">
				<?php echo $jsToolbar; ?>

				<?php if ($miniheader) { ?>
				<div id="es" class="es <?php echo EB::responsive()->isMobile() ? 'is-mobile' : 'is-desktop';?>">
					<?php echo $miniheader; ?>
				</div>
				<?php } ?>

				<?php echo $toolbar; ?>

				<?php echo EB::info()->html();?>

				<?php if ($loadImageTemplates) { ?>
					<?php echo $this->output('site/layout/image/popup'); ?>
					<?php echo $this->output('site/layout/image/container'); ?>
				<?php } ?>

				<?php echo $contents; ?>

				<?php if ($jscripts) { ?>
				<div>
					<?php echo $jscripts;?>
				</div>
				<?php } ?>

				<?php if ($view == 'entry' || $view == 'latest') { ?>
				<div data-gdpr-template class="hide">
					<div data-gdpr-notice-container class="gdpr-notice-container">
						<div class="gdpr-notice-container__content">
							<div class="eb-post-title reset-heading" style="font-weight: 700;" data-gdpr-template-title data-title-template="<?php echo JText::_('COM_EB_GDPR_TITLE');?>"></div>
							<p class="mb-20"><?php echo JText::sprintf('COM_EB_IFRAME_COOKIE_AGREE_CONTENT', JURI::root()); ?></p>
							<div class="">
								<button class="btn btn-default mb-20" data-gdpr-template-agree><?php echo JText::_('COM_EB_IFRAME_COOKIE_AGREE_BUTTON'); ?></button>
								<div class="">
									<a href="javascript:void(0);" target="_blank" rel="noopener" data-gdpr-direct-link><?php echo JText::_('COM_EB_IFRAME_DIRECT_LINK'); ?></a>
								</div>
							</div>
						</div>
					</div>
				</div>
				<?php } ?>

			</div>
		</div>
	</div>
</div>
