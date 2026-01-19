<?php
/**
* @package		EasySocial
* @copyright	Copyright (C) 2010 - 2019 Stack Ideas Sdn Bhd. All rights reserved.
* @license		GNU/GPL, see LICENSE.php
* EasySocial is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/
defined('_JEXEC') or die('Unauthorized Access');
?>
<div class="es-container" data-es-container data-feeds>
	<div class="es-content">
		<div class="fitbit-wrapper">

			<?php if ($hasFitbit) { ?>
			<div class="o-grid o-grid--gutters t-lg-mb--lg">
				<?php if ($hasFitbit) { ?>
				<div class="o-grid__cell fitbit-sync">
					<?php echo JText::sprintf('APP_FITBIT_LAST_SYNCHRONIZED', '<b>' . $updatedDate->format(JText::_('DATE_FORMAT_LC2'), true) . '</b>');?>
				</div>
				<?php } ?>

				<?php if ($user->id == $this->my->id && $hasFitbit) { ?>
				<div class="o-grid__cell">
					<div class="t-text--right">
						<a href="javascript:void(0);" data-unlink-fitbit><?php echo JText::_('APP_FITBIT_UNLINK'); ?></a>
					</div>
				</div>
				<?php } ?>
			</div>
			<?php } ?>

			<?php if ($user->id == $this->my->id && !$hasFitbit) { ?>
			<div class="o-grid o-grid--gutters t-lg-mb--lg">
				<div class="o-grid__cell fitbit-sync">

				</div>

				<div class="o-grid__cell">
					<div class="t-text--right">
						<span data-oauth-login>
							<a href="javascript:void(0);" data-oauth-login-button data-url="<?php echo $authorizationUrl;?>" class="btn btn-es-default">
								<i class="fa fa-walking"></i>&nbsp; <?php echo JText::_('APP_FITBIT_SIGN_IN');?>
							</a>
						</span>
					</div>
				</div>
			</div>
			<?php } ?>

			<div class="o-grid o-grid--gutters fitbit-stats">
				<div class="o-grid__cell">
					<div class="fitbit-stat fitbit-stat--highlight">
						<div class="o-media">
							<div class="o-media__image">
								<img src="<?php echo rtrim(JURI::root(), '/');?>/media/com_easysocial/apps/user/fitbit/assets/images/es-trophy.svg" class="">
							</div>
							<div class="o-media__body">
								<div class="fitbit-stat-box t-text--left t-lg-ml--xl t-xs-ml--md">
									<div class="fitbit-stat__title"><?php echo JText::_('APP_FITBIT_YOUR_BEST_DAY');?></div>
									<div class="fitbit-stat__step"><?php echo $highestDay ? $highestDay->value : 0;?> Steps</div>
									<div class="fitbit-stat__meta"><?php echo $highestDay ? $highestDay->dateObject->format('l, F j, Y') : '&mdash;';?></div>
								</div>
							</div>
						</div>
					</div>

				</div>
				<div class="o-grid__cell">
					<div class="o-grid o-grid--gutters o-grid--center">
						<div class="o-grid__cell">
							<div class="fitbit-stat fitbit-today">
								<div class="fitbit-stat__title"><?php echo JText::_('APP_FITBIT_TODAY');?></div>
								<div class="fitbit-stat__step"><?php echo $todaySteps;?></div>
								<div class="fitbit-stat__meta"><?php echo JText::_('APP_FITBIT_STEPS');?></div>
							</div>
						</div>
						<div class="o-grid__cell">
							<div class="fitbit-stat fitbit-average">
								<div class="fitbit-stat__title"><?php echo JText::_('APP_FITBIT_AVERAGE');?></div>
								<div class="fitbit-stat__step"><?php echo $averageSteps;?></div>
								<div class="fitbit-stat__meta"><?php echo JText::_('APP_FITBIT_STEPS');?></div>
							</div>
						</div>
					</div>
				</div>
			</div>

			<?php if ($user->id == $this->my->id) { ?>
			<div class="o-grid o-grid--gutters t-lg-mb--lg">
				<div class="o-grid__cell">
					<div class="o-form-horizontal">
						<div class="o-form-group">
							<label class="o-control-label" for="review-ratings" style="width: 60%;">
								<?php echo JText::_('APP_FITBIT_ACCESS_SETTINGS');?>
							</label>
							<div class="o-control-input">
								<?php echo $this->html('form.toggler', 'stats', $statsAccess, 'stats', 'data-stats-toggler'); ?>
							</div>
						</div>
					</div>
				</div>

				<div class="o-grid__cell">
					<div class="t-text--right">
						<a href="javascript:void(0);" class="btn btn-sm btn-es-danger-o" data-purge-logs>
							<i class="far fa-trash-alt"></i>&nbsp; <?php echo JText::_('APP_FITBIT_PURGE'); ?>
						</a>
						<a href="javascript:void(0);" class="btn btn-sm btn-es-default-o" data-insert-log>
							<i class="fa fa-plus"></i>&nbsp; <?php echo JText::_('APP_FITBIT_ADD'); ?>
						</a>
					</div>
				</div>
			</div>
			<?php } ?>

			<table class="table table-striped table-hover t-lg-mt--xl">
				<thead>
					<tr>
						<td>
							<b><?php echo JText::_('APP_FITBIT_DATE');?></b>
						</td>
						<td width="40%">
							<b><?php echo JText::_('APP_FITBIT_STEPS');?></b>
						</td>

						<?php if ($params->get('data_edit', true) && $user->id === $this->my->id) { ?>
						<td width="10%" class="center">
							<b><?php echo JText::_('APP_FITBIT_ACTIONS'); ?></b>
						</td>
						<?php } ?>
					</tr>
				</thead>
				<tbody>
					<?php $i = 1; ?>
					<?php if ($steps) { ?>
						<?php foreach ($steps as $step) { ?>
						<tr data-item data-id="<?php echo $step->id;?>">
							<td>
								<?php echo JFactory::getDate($step->date)->format(JText::_('DATE_FORMAT_LC4'));?>
							</td>
							<td>
								<span class="t-hiddenx" data-value><?php echo number_format($step->value);?></span>
								<span class="t-hidden" data-form>
									<div class="o-input-group">
										<input type="text" data-value-input class="o-form-control center" value="<?php echo $step->value;?>" size="5" style="max-width: 64px; display: inline-block;" />
										<span class="o-input-group__btn">
											<button class="btn btn-es-primary-o" type="button" data-save-item>
												<i class="far fa-save"></i>
											</button>
										</span>
									</div>
								</span>

								<div class="t-text--danger t-hidden" data-fitbit-error>
								</div>
							</td>

							<?php if ($params->get('data_edit', true) && $user->id === $this->my->id) { ?>
							<td class="center">
								<?php if (!$limitEditDays || $i <= $limitEditDays) { ?>
								<a href="javascript:void(0);" data-fitbit-edit>
									<i class="far fa-edit"></i>
								</a>
								<?php } else { ?>
									&mdash;
								<?php } ?>
								&nbsp;
								<a href="javascript:void(0);" data-fitbit-delete>
									<i class="far fa-trash-alt"></i>
								</a>
							</td>
							<?php } ?>

							<?php $i++; ?>
						</tr>
						<?php } ?>
					<?php } else { ?>
						<tr>
							<td colspan="3">
								<?php if ($user->id === $this->my->id) { ?>
								<div class="es-container" data-es-container data-feeds>
									<div class="es-content">
										<div class="fitbit-sign-in">
											<h3><?php echo JText::_('APP_FITBIT_NO_DATA'); ?></h3>
											<p><?php echo JText::_('APP_FITBIT_NO_DATA_INFO');?></p>

											<?php if (!$hasFitbit) { ?>
											<span data-oauth-login>
												<a href="javascript:void(0);" data-oauth-login-button data-url="<?php echo $authorizationUrl;?>" class="btn btn-es-primary">
													<i class="fa fa-walking"></i>&nbsp; <?php echo JText::_('APP_FITBIT_SIGN_IN');?>
												</a>
											</span>
											<?php } ?>

											<span class="t-lg-ml--lg">
												<a href="javascript:void(0);" class="btn btn-es-default" data-insert-log>
													<i class="fa fa-plus"></i>&nbsp; <?php echo JText::_('APP_FITBIT_ADD'); ?>
												</a>
											</span>
										</div>
									</div>
								</div>
								<?php } ?>
							</td>
						</tr>
					<?php } ?>
				</tbody>
			</table>
		</div>
	</div>
</div>
