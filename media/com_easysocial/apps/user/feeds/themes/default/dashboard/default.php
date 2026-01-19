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
<div class="app-feeds" data-feeds>

	<div class="app-contents<?php echo !$feeds ? ' is-empty' : '';?>" data-app-contents>

		<div class="o-grid t-lg-mb--xl">
			<div class="o-grid__cell">
				<?php echo JText::_('APP_FEEDS_DASHBOARD_INFO'); ?>
			</div>

			<div class="o-grid__cell-auto-size">
				<a class="btn btn-es-primary" href="javascript:void(0);" data-feeds-create><?php echo JText::_('APP_FEEDS_NEW_FEED'); ?></a>
			</div>
		</div>

		<div class="app-contents-data">
			<div data-feeds-lists>
				<?php if( $feeds ){ ?>
					<?php foreach( $feeds as $feed ){ ?>
						<?php echo $this->loadTemplate( 'themes:/apps/user/feeds/dashboard/default.item' , array( 'feed' => $feed ) ); ?>
					<?php } ?>
				<?php } ?>
			</div>
		</div>

		<?php echo $this->html('html.emptyBlock', 'APP_FEEDS_NO_FEEDS_YET', 'fa-database'); ?>
	</div>
</div>
