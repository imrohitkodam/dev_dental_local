<?php
/**
* @package      EasySocial
* @copyright    Copyright (C) 2010 - 2014 Stack Ideas Sdn Bhd. All rights reserved.
* @license      GNU/GPL, see LICENSE.php
* EasySocial is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/
defined( '_JEXEC' ) or die( 'Unauthorized Access' );
?>
<div class="app-tasks" data-tasks>
	<div class="es-filterbar">
		<div class="filterbar-title"><?php echo JText::_( 'APP_EASYDISCUSS_MANAGE_SUBSCRIPTIONS' ); ?></div>
		<div class="app-info">
			<?php echo JText::_('APP_EASYDISCUSS_MANAGE_SUBSCRIPTIONS_INFO'); ?>
		</div>
	</div>

	<div class="app-contents" data-app-contents>

		<div class="app-contents-data">
			<?php if ($subs) { ?>
				<?php foreach ($subs as $subtype => $sub) { ?>
					<div class="es-discuss-group">
					<?php if (count($sub) > 0) { ?>
						<h5 class="es-discuss-group__title"><?php echo JText::_('APP_EASYDISCUSS_SUBSCRIPTION_' . $subtype); ?></h5>
						
						<?php foreach ($sub as $item) { ?>
						<div class="es-discuss-item">
							
							<div class="" data-subscription-item>
								<div class="o-grid">
									<div class="o-grid__cell">
										 <a href="<?php echo $item->link; ?>" class="es-discuss-link"><?php echo $item->title; ?></a>
									</div>
									<div class="o-grid__cell o-grid__cell--auto-size">
										<div class="btn-group">
											<a href="javascript:void(0);" data-bs-toggle="dropdown" class="dropdown-toggle_ btn btn-es-default-o btn-sm">
												<i class="fa fa-ellipsis-h"></i>
											</a>
											<ul class="dropdown-menu dropdown-menu-user">
												<li>
													<a href="javascript:void(0);" data-es-discuss-unsubscribe data-type="<?php echo $item->type;?>" data-cid="<?php echo $item->cid;?>" data-id="<?php echo $item->id;?>"><?php echo JText::_('APP_EASYDISCUSS_SUBSCRIPTION_UNSUBSCRIBE_BUTTON'); ?></a>
												</li>
											</ul>

										</div>
									</div>
								</div>
							</div>
							<div class="o-alert o-alert--info t-hidden" data-unsubscribe-success>
								<?php echo JText::_('APP_EASYDISCUSS_SUCCESSFULLY_UNSUBSCRIBE_ALERT'); ?>
							</div>
						</div>
						<?php } ?> 
				<?php } ?>
				</div>
			<?php } ?>
		<?php } ?>    

		</div>
	</div>

</div>
