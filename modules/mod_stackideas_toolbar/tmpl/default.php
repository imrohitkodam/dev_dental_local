<?php
/**
* @package      StackIdeas
* @copyright    Copyright (C) Stack Ideas Sdn Bhd. All rights reserved.
* @license      GNU/GPL, see LICENSE.php
* StackIdeas Toolbar is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/
defined('_JEXEC') or die('Unauthorized Access');
?>
<div id="fd" class="mod-si-toolbar <?php echo $params->get('moduleclass_sfx'); ?><?php echo FH::responsive()->isMobile() ? ' is-mobile' : ''; ?><?php echo FH::responsive()->isTablet() ? ' is-tablet' : ''; ?>"
	data-fd-toolbar
	data-fd-unique="<?php echo uniqid(); ?>"
	data-fd-main="<?php echo FDT::getMainComponent(); ?>"
	data-fd-polling-url="<?php echo base64_encode($adapter->getAjaxPollingUrl()); ?>"
	data-fd-polling-interval="<?php echo $adapter->getPollingInterval(); ?>"
	data-fd-error="<?php echo JText::_('MOD_SI_TOOLBAR_ERROR_FETCHING_CONTENT'); ?>"
	data-fd-responsive="<?php echo $responsive; ?>"
	data-fd-search-suggestion="<?= ($adapter->getSuggestion() === '1') ? 'true': 'false' ?>"
	data-fd-search-suggestion-minimum="<?= $adapter->getMinSearch()?>"
	>
	<div class="<?php echo FDT::getAppearance();?> <?php echo FDT::getAccent();?>">
		<div class="fd-toolbar" data-fd-toolbar-wrapper>
			<?php echo $themes->html('menu');?>

			<?php echo $themes->html('search');?>

			<div class="fd-toolbar__item fd-toolbar__item--action">
				<nav class="o-nav fd-toolbar__o-nav">
					<?php echo $themes->html('action.compose'); ?>

					<?php echo $themes->html('action.search'); ?>

					<?php echo $themes->html('action.notifications'); ?>

					<?php echo $themes->html('action.subscriptions');?>

					<?php echo $themes->html('dropdown.user'); ?>

					<?php echo $themes->html('dropdown.responsive'); ?>
				</nav>
			</div>
		</div>

		<?php if (FDT::config()->get('showDivider', true)) { ?>
			<div class="fd-toolbar-divider"></div>
		<?php } ?>
	</div>

	<?php echo $themes->fd->html('html.tooltip'); ?>
</div>
