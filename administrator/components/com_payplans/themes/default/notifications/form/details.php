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
<div class="grid grid-cols-1 md:grid-cols-12 gap-md">
	<div class="col-span-1 md:col-span-5 w-auto">
		<?php echo $this->output('admin/app/generic/form', ['app' => $app]); ?>
	</div>

	<div class="col-span-1 md:col-span-7 w-auto">
		<div class="panel">
			<?php echo $this->html('panel.heading', 'COM_PP_NOTIFICATIONS_BEHAVIOR'); ?>

			<div class="panel-body">

				<?php if ($when === 'on_status') { ?>
				<div class="flex flex-col md:flex-row hover:bg-gray-100 px-xs py-md">
					<?php echo $this->html('form.label', 'COM_PP_NOTIFICATIONS_STATUS', '', 3); ?>

					<div class="flex-grow">
						<?php echo $this->html('form.status', 'app_params[on_status]', $params->get('on_status'), 'both', '', false, '', [PP_SUBSCRIPTION_NONE]); ?>
					</div>
				</div>
				<?php } ?>

				<?php if ($when === 'on_subscription_renewal') { ?>
					<div class="flex flex-col md:flex-row hover:bg-gray-100 px-xs py-md">
					<?php echo $this->html('form.label', 'COM_PAYPLANS_APP_EMAIL_RECURRING_RENEWAL_LABEL', 'COM_PAYPLANS_APP_EMAIL_RECURRING_RENEWAL_LABEL_DESC', 3, true, true); ?>

					<div class="flex-grow">
						<?php echo $this->fd->html('form.toggler', 'app_params[on_each_recurring]', $params->get('on_each_recurring', false)); ?>
					</div>
				</div>
				<?php } ?>

				<?php if ($when === 'on_preexpiry') { ?>
				<div class="flex flex-col md:flex-row hover:bg-gray-100 px-xs py-md">
					<?php echo $this->html('form.label', 'COM_PAYPLANS_APP_EMAIL_ON_PRE_EXPIRY_LABEL', 'COM_PAYPLANS_APP_EMAIL_ON_PRE_EXPIRY_DESC', 3, true, true); ?>

					<div class="flex-grow">
						<?php echo $this->html('form.timer', 'app_params[on_preexpiry]', $params->get('on_preexpiry', '000000000000')); ?>
					</div>
				</div>

				<div class="flex flex-col md:flex-row hover:bg-gray-100 px-xs py-md">
					<?php echo $this->html('form.label', 'COM_PAYPLANS_APP_EMAIL_LAST_CYCLE_LABEL', 'COM_PAYPLANS_APP_EMAIL_LAST_CYCLE_LABEL_DESC', 3, true, true); ?>

					<div class="flex-grow">
						<?php echo $this->fd->html('form.toggler', 'app_params[on_lastcycle]', $params->get('on_lastcycle', false)); ?>
					</div>
				</div>
				<?php } ?>

				<?php if ($when === 'on_preexpiry_trial') { ?>
				<div class="flex flex-col md:flex-row hover:bg-gray-100 px-xs py-md">
					<?php echo $this->html('form.label', 'COM_PP_APP_EMAIL_ON_PRE_EXPIRY_TRIAL_LABEL', 'COM_PP_APP_EMAIL_ON_PRE_EXPIRY_TRIAL_DESC', 3, true, true); ?>

					<div class="flex-grow">
						<?php echo $this->html('form.timer', 'app_params[on_preexpiry_trial]', $params->get('on_preexpiry_trial', '000003000000')); ?>
					</div>
				</div>
				<?php } ?>

				<?php if ($when === 'on_postexpiry') { ?>
				<div class="flex flex-col md:flex-row hover:bg-gray-100 px-xs py-md">
					<?php echo $this->html('form.label', 'COM_PAYPLANS_APP_EMAIL_ON_POST_EXPIRY_LABEL', 'COM_PAYPLANS_APP_EMAIL_ON_POST_EXPIRY_DESC', 3, true, true); ?>

					<div class="flex-grow">
						<?php echo $this->html('form.timer', 'app_params[on_postexpiry]', $params->get('on_postexpiry', '000000000000')); ?>
					</div>
				</div>
				<?php } ?>

				<?php if ($when === 'on_postactivation') { ?>
				<div class="flex flex-col md:flex-row hover:bg-gray-100 px-xs py-md">
					<?php echo $this->html('form.label', 'COM_PAYPLANS_APP_EMAIL_ON_POST_ACTIVATION_LABEL', 'COM_PAYPLANS_APP_EMAIL_ON_POST_ACTIVATION_DESC', 3, true, true); ?>

					<div class="flex-grow">
						<?php echo $this->html('form.timer', 'app_params[on_postactivation]', $params->get('on_postactivation', '000000000000')); ?>
					</div>
				</div>
				<?php } ?>

				<?php if ($when === 'on_cart_abondonment') { ?>
				<div class="flex flex-col md:flex-row hover:bg-gray-100 px-xs py-md">
					<?php echo $this->html('form.label', 'COM_PAYPLANS_APP_EMAIL_ON_CART_ABONDONMENT_LABEL', 'COM_PAYPLANS_APP_EMAIL_ON_CART_ABONDONMENT_DESC', 3, true, true); ?>

					<div class="flex-grow">
						<?php echo $this->html('form.timer', 'app_params[on_cart_abondonment]', $params->get('on_cart_abondonment', '000000000000')); ?>
					</div>
				</div>
				<?php } ?>
				
				<div class="flex flex-col md:flex-row hover:bg-gray-100 px-xs py-md">
					<?php echo $this->html('form.label', 'COM_PP_NOTIFICATIONS_SUBJECT', '', 3); ?>

					<div class="flex-grow">
						<?php echo $this->fd->html('form.text', 'app_params[subject]', $params->get('subject', '')); ?>
					</div>
				</div>

				<div class="flex flex-col md:flex-row hover:bg-gray-100 px-xs py-md">
					<?php echo $this->html('form.label', 'COM_PP_NOTIFICATIONS_CC_LIST', '', 3); ?>

					<div class="flex-grow">
						<?php echo $this->fd->html('form.textarea', 'app_params[send_cc]', $params->get('send_cc', '')); ?>
					</div>
				</div>

				<div class="flex flex-col md:flex-row hover:bg-gray-100 px-xs py-md">
					<?php echo $this->html('form.label', 'COM_PP_NOTIFICATIONS_BCC_LIST', '', 3); ?>

					<div class="flex-grow">
						<?php echo $this->fd->html('form.textarea', 'app_params[send_bcc]', $params->get('send_bcc', '')); ?>
					</div>
				</div>

				<div class="flex flex-col md:flex-row hover:bg-gray-100 px-xs py-md">
					<?php echo $this->html('form.label', 'COM_PAYPLANS_APP_EMAIL_ATTACHMENT_LABEL', 'COM_PAYPLANS_APP_EMAIL_ATTACHMENT_DESC', 3); ?>

					<div class="flex-grow">
						<?php echo $this->html('form.filelist', 'app_params[attachment]', $params->get('attachment', ''), '', '', 'media:/emails/attachments', '.', [], false); ?>

					</div>
				</div>

				<div class="flex flex-col md:flex-row hover:bg-gray-100 px-xs py-md">
					<?php echo $this->html('form.label', 'COM_PP_NOTIFICATIONS_INCLUDE_INVOICE_IN_ATTACHMENTS', '', 3); ?>

					<div class="flex-grow">
						<?php echo $this->fd->html('form.toggler', 'app_params[send_invoice]', $params->get('send_invoice', false)); ?>
					</div>
				</div>

				<div class="flex flex-col md:flex-row hover:bg-gray-100 px-xs py-md">
					<?php echo $this->html('form.label', 'COM_PP_NOTIFICATIONS_SEND_AS_HTML', '', 3); ?>

					<div class="flex-grow">
						<?php echo $this->fd->html('form.toggler', 'app_params[html_format]', $params->get('html_format', true)); ?>
					</div>
				</div>

				<div class="flex flex-col md:flex-row hover:bg-gray-100 px-xs py-md" data-pp-form-group-wrapper>
					<?php echo $this->html('form.label', 'COM_PP_NOTIFICATIONS_CONTENT_SOURCE', '', 3); ?>

					<div class="flex-grow">
						<?php echo $this->fd->html('form.dropdown', 'app_params[email_template]', $params->get('email_template', 'custom'), [
							'custom' => JText::_('COM_PP_NOTIFICATIONS_CUSTOM_CONTENT'),
							'choose_template' => JText::_('COM_PP_NOTIFICATIONS_USE_EXISTING_TEMPLATE'),
							'choose_joomla_article' => JText::_('COM_PP_NOTIFICATIONS_USE_EXISTING_JOOMLA_ARTICLE')
						], ['attributes' => 'data-email-template']); ?>
					</div>
				</div>

				<div class="flex flex-col md:flex-row hover:bg-gray-100 px-xs py-md <?php echo $params->get('email_template', 'custom') === 'custom' ? '' : 't-hidden' ?>" data-custom-content>
					<?php echo $this->html('form.label', 'COM_PAYPLANS_APP_EMAIL_CONTENT_LABEL', 'COM_PAYPLANS_APP_EMAIL_CONTENT_DESC', 3); ?>

					<div class="flex-grow">
						<?php echo $this->html('form.editor', 'app_params[content]', $params->get('content', ''), ''); ?>

					</div>
				</div>

				<div class="flex flex-col md:flex-row hover:bg-gray-100 px-xs py-md <?php echo $params->get('email_template', 'custom') === 'choose_template' ? '' : 't-hidden' ?>" data-email-templates>
					<?php echo $this->html('form.label', 'COM_PAYPLANS_APP_EMAIL_TEMPLATE_LABEL', 'COM_PAYPLANS_APP_EMAIL_TEMPLATE_DESC', 3); ?>

					<div class="flex-grow">
						<?php echo $this->html('form.filelist', 'app_params[choose_template]', $params->get('choose_template', 'subscription_active.php'), '', '', 'media:/emails/templates'); ?>

					</div>
				</div>

				<div class="flex flex-col md:flex-row hover:bg-gray-100 px-xs py-md <?php echo $params->get('email_template', 'custom') === 'choose_joomla_article' ? '' : 't-hidden' ?>" data-content-joomlaarticle>
					<?php echo $this->html('form.label', 'COM_PP_NOTIFICATIONS_JOOMLA_ARTICLE_LABEL', 'COM_PP_NOTIFICATIONS_JOOMLA_ARTICLE_DESC', 3); ?>

					<div class="flex-grow">
						<?php echo $this->html('form.joomlaArticle', 'app_params[choose_joomla_article]', $params->get('choose_joomla_article'), '', '', ['multiple' => false]); ?>
					</div>
				</div>

				<div class="flex flex-col md:flex-row hover:bg-gray-100 px-xs py-md">
					<label class="col-md-3"></label>

					<div class="flex-grow">
						<?php echo $this->html('form.rewriter'); ?>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>