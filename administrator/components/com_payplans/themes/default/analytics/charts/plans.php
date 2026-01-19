<?php
/**
* @package		PayPlans
* @copyright	Copyright (C) 2010 - 2018 Stack Ideas Sdn Bhd. All rights reserved.
* @license		GNU/GPL, see LICENSE.php
* PayPlans is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/
defined('_JEXEC') or die('Unauthorized Access');
?>
<div class="t-lg-p--xl">

	<div data-pp-analytics-plan>
		<?php echo $this->fd->html('loader.block', [
			'class' => 'flex items-center',
			'loaderClass' => 'block',
			'text' => JText::_('COM_PP_RETRIEVE_CHART_DATA')
		]); ?>

		<div id="canvas-holder">
			<canvas id="chart-area"></canvas>
		</div>

		<div class="o-empty">
			<div class="o-empty__content">
				<i class="o-empty__icon fdi fas fa-exclamation-circle"></i>
				<div class="o-empty__text"><?php echo JText::_('COM_PP_CHART_NO_DATA'); ?></div>
			</div>
		</div>
	</div>
</div>
