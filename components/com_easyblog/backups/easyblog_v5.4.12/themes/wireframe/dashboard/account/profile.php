<?php
/**
* @package      EasyBlog
* @copyright    Copyright (C) 2010 - 2019 Stack Ideas Sdn Bhd. All rights reserved.
* @license      GNU/GPL, see LICENSE.php
* EasyBlog is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/
defined('_JEXEC') or die('Unauthorized Access');
?>
<div class="eb-box">
	<?php echo $this->html('dashboard.miniHeading', 'COM_EASYBLOG_DASHBOARD_ACCOUNT_SETTINGS', 'fa fa-user'); ?>

	<div class="eb-box-body">
		<div class="form-horizontal clear">
			<?php if ($this->config->get('layout_avatar') && $this->config->get('layout_avatarIntegration') == 'default') { ?>
			<div class="form-group">
				<?php echo $this->html('dashboard.label', 'COM_EASYBLOG_DASHBOARD_ACCOUNT_PROFILE_PICTURE', 'avatar'); ?>
				<div class="col-md-8">
					<div class="media">
						<div class="media-object pull-left mr-10">
							<img class="avatar-image" src="<?php echo $profile->getAvatar(); ?>"/>
						</div>

						<?php if ($this->acl->get('upload_avatar')) { ?>
						<div id="avatar-upload-form" class="media-body">
							<?php echo JText::sprintf('COM_EASYBLOG_DASHBOARD_ACCOUNT_PROFILE_PICTURE_UPLOAD_CONDITION', (float) $this->config->get('main_upload_image_size', 0) , EBLOG_AVATAR_LARGE_WIDTH, EBLOG_AVATAR_LARGE_HEIGHT); ?>
							<div class="mts"><input id="file-upload" type="file" name="avatar" /></div>
							<div><span id="upload-clear"></span></div>
						</div>
						<?php } ?>
					</div>
				</div>
			</div>
			<hr />
			<?php } ?>

			<div class="form-group">
				<?php echo $this->html('dashboard.label', 'COM_EASYBLOG_DASHBOARD_ACCOUNT_REALNAME'); ?>

				<div class="col-md-5">
					<?php echo $this->html('dashboard.text', 'fullname', $this->escape($this->my->name)); ?>
				</div>
			</div>
			<div class="form-group">
				<?php echo $this->html('dashboard.label', 'COM_EASYBLOG_DASHBOARD_ACCOUNT_WHAT_OTHERS_CALL_YOU'); ?>

				<div class="col-md-5">
					<?php echo $this->html('dashboard.text', 'nickname', $this->escape($profile->nickname)); ?>
				</div>
			</div>
			<div class="form-group">
				<?php echo $this->html('dashboard.label', 'COM_EASYBLOG_DASHBOARD_ACCOUNT_USERNAME');?>

				<div class="col-md-5">
					<?php echo $this->html('dashboard.text', '', $this->my->username, '', array('attr' => 'disabled="disabled"')); ?>
				</div>
			</div>

			<?php if ($this->config->get('main_joomlauserparams')) { ?>
			<div class="form-group">
				<?php echo $this->html('dashboard.label', 'COM_EASYBLOG_DASHBOARD_ACCOUNT_EMAIL');?>

				<div class="col-md-5">
					<?php echo $this->html('dashboard.text', 'email', $this->escape($this->my->email)); ?>
				</div>
			</div>

			<div class="form-group">
				<?php echo $this->html('dashboard.label', 'COM_EASYBLOG_DASHBOARD_ACCOUNT_PASSWORD');?>

				<div class="col-md-5">
					<input class="form-control" type="password" id="password" name="password" value=""  autocomplete="new-password" />
				</div>
			</div>

			<div class="form-group">
				<?php echo $this->html('dashboard.label', 'COM_EASYBLOG_DASHBOARD_ACCOUNT_RECONFIRM_PASSWORD');?>

				<div class="col-md-5">
					<input class="form-control" type="password" id="password2" name="password2"  autocomplete="new-password" />
				</div>
			</div>

			<div class="form-group">
					<?php echo $this->html('dashboard.label', 'COM_EB_JOOMLA_TIMEZONE'); ?>

				<div class="col-md-5">
				<select name="timezone" class="form-control">
					<option value="UTC" <?php if ($userTimezone === 'UTC' || !$userTimezone) { ?>selected="selected"<?php } ?>><?php echo JText::_('COM_EB_JOOMLA_TIMEZONE_USE_DEFAULT'); ?></option>
					<?php foreach ($joomlaTimezone as $group => $countries) { ?>
						<optgroup label="<?php echo $group;?>">
						<?php foreach ($countries as $country) { ?>
							<option value="<?php echo $country; ?>" <?php echo $userTimezone === $country ? 'selected="selected"' : ''; ?>><?php echo $country;?></option>
						<?php } ?>
						</optgroup>
					<?php } ?>
				</select>
				</div>
			</div>

			<div class="form-group">
					<?php echo $this->html('dashboard.label', 'COM_EB_JOOMLA_LANGUAGE'); ?>

				<div class="col-md-5">
				<select name="language" class="form-control">
					<option value="" selected="selected"><?php echo JText::_('COM_EB_JOOMLA_LANGUAGE_USE_DEFAULT'); ?></option>
					<?php foreach ($languages as $language) { ?>
						<option value="<?php echo $language->value; ?>" <?php echo $userLanguage == $language->value ? 'selected="selected"' : ''; ?>><?php echo $language->text;?></option>
					<?php } ?>
				</select>
				</div>
			</div>
			<?php } ?>
		</div>
	</div>
</div>
