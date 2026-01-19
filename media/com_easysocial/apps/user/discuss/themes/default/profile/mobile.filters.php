<?php
/**
* @package		EasyDiscuss
* @copyright	Copyright (C) Stack Ideas Sdn Bhd. All rights reserved.
* @license		GNU/GPL, see LICENSE.php
* EasyDiscuss is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/
defined('_JEXEC') or die('Unauthorized Access');
?>
<div class="es-mobile-filter" data-es-mobile-filters>
	<div class="es-mobile-filter__hd">
		<div class="es-mobile-filter__hd-cell is-slider">
			<div class="es-mobile-filter-slider is-end-left" data-es-swiper-slider>
				<div class="es-mobile-filter-slider__content swiper-container" data-es-swiper-container>
					<div class="swiper-wrapper">
						<?php echo $this->html('mobile.filterTab', 'APP_EASYDISCUSS_DISCUSSIONS_FILTER_ALL', 'javascript:void(0)', true, array('data-discuss-filter="userposts"')); ?>

						<?php if ($config->get('main_qna') && $config->get('layout_enablefilter_unanswered')) { ?>
							<?php echo $this->html('mobile.filterTab', 'APP_EASYDISCUSS_DISCUSSIONS_FILTER_UNANSWERED', 'javascript:void(0)', false, array('data-discuss-filter="unanswered"')); ?>
						<?php } ?>

						<?php if ($config->get('main_qna') && $config->get('layout_enablefilter_resolved')) { ?>
							<?php echo $this->html('mobile.filterTab', 'APP_EASYDISCUSS_DISCUSSIONS_FILTER_RESOLVED', 'javascript:void(0)', false, array('data-discuss-filter="resolved"')); ?>
						<?php } ?>

						<?php if ($config->get('main_qna') && $config->get('layout_enablefilter_unresolved')) { ?>
							<?php echo $this->html('mobile.filterTab', 'APP_EASYDISCUSS_DISCUSSIONS_FILTER_UNRESOLVED', 'javascript:void(0)', false, array('data-discuss-filter="unresolved"')); ?>
						<?php } ?>

						<?php echo $this->html('mobile.filterTab', 'APP_EASYDISCUSS_DISCUSSIONS_FILTER_REPLIES', 'javascript:void(0)', false, array('data-discuss-filter="userreplies"')); ?>

						<?php if ($config->get('main_qna') && (ED::ismoderator() || (ED::isMine($user->id)))) { ?>
							<?php echo $this->html('mobile.filterTab', 'APP_EASYDISCUSS_DISCUSSIONS_FILTER_PENDING', 'javascript:void(0)', false, array('data-discuss-filter="pending"')); ?>
						<?php } ?>
					</div>
				</div>
			</div>
		</div>

		<?php if ($canCreateDiscussion) { ?>
			<?php echo $this->html('mobile.filterActions',
					array($this->html('mobile.filterAction', 'APP_EASYDISCUSS_CREATE_DISCUSSION', $composeLink))
			); ?>
		<?php } ?>
	</div>
</div>
