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
<div class="fd-toolbar__o-nav-item"
	data-fd-tooltip="toolbar"
	data-fd-tooltip-title="<?php echo JText::_('COM_EASYDISCUSS_SUBSCRIBE'); ?>"
	data-fd-tooltip-placement="top" 
	>
	<a href="javascript:void(0);" class="fd-toolbar__link"
	data-fd-dropdown="toolbar"
	data-fd-dropdown-placement="bottom"
	data-fd-dropdown-offset="[0, 10]"
	data-fd-dropdown-trigger="click"
	>
		<i class="fdi fa fa-envelope"></i>
	</a>
	<div class="t-hidden">
		<div id="fd" class="">
			<div class="<?php echo FDT::getAppearance();?> <?php echo FDT::getAccent();?>">
				<div class="o-dropdown divide-y divide-gray-200">
					<div class="o-dropdown__hd px-md py-sm font-bold">
						<?php echo JText::_('COM_EASYDISCUSS_SUBSCRIBE'); ?>
					</div>

					<?php if ($config->get('main_rss')) { ?>
						<div class="o-dropdown__bd px-md py-sm">
							<a href="<?php echo ED::feeds()->getFeedUrl('view=index');?>" class="space-y-xs hover:no-underline text-gray-500">
								<div>
									<b>
										<i class="fdi fa fa-rss-square"></i>&nbsp; <?php echo JText::_('COM_ED_SUBSCRIBE_RSS');?>
									</b>
								</div>
								<div class="text-sm">
									<?php echo JText::_('COM_ED_SUBSCRIBE_RSS_INFO');?>
								</div>
							</a>
						</div>
					<?php } ?>
					<?php if ($config->get('main_sitesubscription')) { ?>
						<div class="o-dropdown__ft px-md py-sm">
							<?php if (!$subscription) { ?>
							<a href="javascript:void(0);" class="space-y-xs hover:no-underline text-gray-500" 
								data-ed-subscribe
								data-type="site"
								data-cid="0"
							>
							<?php } ?>

							<div class="space-y-xs">
								<div>
									<b>
										<i class="fdi fa fa-at"></i>&nbsp; <?php echo JText::_('COM_ED_RECEIVE_EMAIL_UPDATES');?>
									</b>
								</div>

								<?php if ($subscription) { ?>
								<div class="text-sm">
									<?php echo JText::_('COM_ED_TOOLBAR_ALREADY_SUBSCRIBED');?> 
								</div>
								<div class="pt-sm">
										<?php echo $this->fd->html('button.link', 
											EDR::_('index.php?option=com_easydiscuss&view=subscription'),
											JText::_('COM_ED_MANAGE_YOUR_SUBSCRIPTION'), 
											'primary', 
											['block' => true]
										);?>
								</div>
								
								<?php } ?>
							</div>

							<?php if (!$subscription) { ?>
								<div class="text-sm">
									<?php echo JText::_('COM_ED_RECEIVE_EMAIL_UPDATES_INFO');?>
								</div>
							</a>
							<?php } ?>
						</div>
					<?php } ?>
				</div>
			</div>
		</div>
	</div>
</div>