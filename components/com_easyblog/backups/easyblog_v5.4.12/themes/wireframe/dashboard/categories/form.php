<?php
/**
* @package		EasyBlog
* @copyright	Copyright (C) 2010 - 2018 Stack Ideas Sdn Bhd. All rights reserved.
* @license		GNU/GPL, see LICENSE.php
* EasyBlog is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/
defined('_JEXEC') or die('Unauthorized Access');
?>
<form method="post" action="<?php echo JRoute::_('index.php');?>" enctype="multipart/form-data">
	<?php echo $this->html('dashboard.heading', (!$category->id) ? 'COM_EASYBLOG_DASHBOARD_CATEGORIES_CREATE' : 'COM_EASYBLOG_DASHBOARD_CATEGORIES_EDIT', 'fa fa-folder-open-o'); ?>
	<div class="eb-box">
		<div class="eb-box-body">
			<div class="form-horizontal clear">
				<div class="form-group">
					<label class="col-md-3 control-label"><?php echo JText::_('COM_EASYBLOG_DASHBOARD_CATEGORIES_NAME'); ?></label>
					<div class="col-md-7">
						<input type="text" id="title" name="title" class="form-control input-sm" value="<?php echo $this->escape($category->title);?>" placeholder="<?php JText::_('COM_EASYBLOG_DASHBOARD_CATEGORIES_NAME_REQUIRED'); ?>" />
					</div>
				</div>

				<div class="form-group">
					<label class="col-md-3 control-label"><?php echo JText::_('COM_EASYBLOG_CATEGORY_ALIAS'); ?></label>
					<div class="col-md-7">
						<input name="alias" id="alias" class="form-control input-sm" maxlength="255" value="<?php echo $this->escape($category->alias);?>" placeholder="<?php echo JText::_('COM_EASYBLOG_DASHBOARD_CATEGORIES_ALIAS_OPTIONAL'); ?>"/>
					</div>
				</div>

				<div class="form-group">
					<label class="col-md-3 control-label"><?php echo JText::_('COM_EASYBLOG_DASHBOARD_CATEGORIES_DESCRIPTION');?></label>
					<div class="col-md-7">
						<?php echo $editor->display('description', $category->get( 'description') , '99%', '200', '10', '10', array('image', 'readmore', 'pagebreak'), array(), 'com_easyblog'); ?>
					</div>
				</div>

				<div class="form-group">
					<label class="col-md-3 control-label"><?php echo JText::_('COM_EASYBLOG_DASHBOARD_CATEGORIES_PARENT'); ?></label>
					<div class="col-md-7">
						<?php echo $parentList; ?>
					</div>
				</div>

				<div class="form-group">
					<label class="col-md-3 control-label"><?php echo JText::_('COM_EASYBLOG_DASHBOARD_CATEGORIES_PRIVACY'); ?></label>
					<div class="col-md-7">
						<?php echo JHTML::_('select.genericlist', EB::privacy()->getOptions('category'), 'private', 'class="form-control"' , 'value' , 'text', $category->private);?>
					</div>
				</div>

				<div class="form-group<?php echo ($category->private != 2) ? ' hide' : ''; ?>" data-category-access>
					<?php if ($category->default) { ?>
						<label class="col-md-3 control-label"><?php echo JText::_('COM_EASYBLOG_CATEGORIES_ACL_VIEW_TITLE'); ?></label>
						<div class="col-md-7">
							<div class="eb-alert row-table alert alert-notice">
								<div class="col-cell cell-tight cell-sign">
									<i class="fa fa-times-circle"></i>
								</div>
								<div class="col-cell cell-text"><?php echo JText::_('COM_EB_CATEGORIES_ASSIGNED_PERMISSIONS_DENIED');?></div>
							</div>
						</div>
					<?php } else { ?>
						<label class="col-md-3 control-label"><?php echo JText::_('COM_EASYBLOG_CATEGORIES_ACL_VIEW_TITLE'); ?></label>
						<div class="col-md-7">
							<select multiple="multiple" name="category_acl_view[]" class="form-control" style="height: 150px;">
								<?php foreach ($groups[CATEGORY_ACL_ACTION_VIEW] as $group) { ?>
								<option value="<?php echo $group->groupid; ?>" style="padding:2px;" <?php echo ($group->status) ? 'selected="selected"' : ''; ?> ><?php echo $group->groupname; ?></option>
								<?php } ?>
							</select>
						</div>
					<?php } ?>
				</div>

				<div class="form-group<?php echo ($category->private != 2) ? ' hide' : ''; ?>" data-category-access>
					<?php if ($category->default) { ?>
						<label class="col-md-3 control-label"><?php echo JText::_('COM_EB_CATEGORIES_ASSIGNED_PERMISSIONS_SELECT_CREATION_TYPE'); ?></label>
						<div class="col-md-7">
							<div class="eb-alert row-table alert alert-notice">
								<div class="col-cell cell-tight cell-sign">
									<i class="fa fa-times-circle"></i>
								</div>
								<div class="col-cell cell-text"><?php echo JText::_('COM_EB_CATEGORIES_ASSIGNED_PERMISSIONS_DENIED');?></div>
							</div>
						</div>
					<?php } else { ?>
						<label class="col-md-3 control-label"><?php echo JText::_('COM_EB_CATEGORIES_ASSIGNED_PERMISSIONS_SELECT_CREATION_TYPE'); ?></label>
						<div class="col-md-7">
							<select name="params[category_acl_type]" id="category_acl_type" class="form-control">
								<option <?php echo $params->get('category_acl_type') == CATEGORY_ACL_ACTION_SELECT ? 'selected="selected"' : '';?> value="<?php echo CATEGORY_ACL_ACTION_SELECT;?>">
									<?php echo JText::_('COM_EB_CATEGORIES_ASSIGNED_PERMISSIONS_SELECT_CREATION_TYPE_GROUP');?>
								</option>
								<option <?php echo $params->get('category_acl_type') == CATEGORY_ACL_ACTION_SPECIFIC ? 'selected="selected"' : '';?> value="<?php echo CATEGORY_ACL_ACTION_SPECIFIC;?>">
									<?php echo JText::_('COM_EB_CATEGORIES_ASSIGNED_PERMISSIONS_SELECT_CREATION_TYPE_USERS');?>
								</option>
							</select>

							<div class="mt-10 <?php echo $params->get('category_acl_type') == CATEGORY_ACL_ACTION_SELECT ? '' : 'hide';?>" data-category-acl-select>
								<select multiple="multiple" name="category_acl_select[]" class="form-control" style="height: 150px;">
									<?php foreach ($groups[CATEGORY_ACL_ACTION_SELECT] as $group) { ?>
										<option value="<?php echo $group->groupid; ?>" style="padding:2px;" <?php echo ($group->status) ? 'selected="selected"' : ''; ?> ><?php echo $group->groupname; ?></option>
									<?php } ?>
								</select>
							</div>

							<div class="mt-10 <?php echo $params->get('category_acl_type') == CATEGORY_ACL_ACTION_SPECIFIC ? '' : 'hide';?>" data-category-acl-specific>
								<?php echo $this->html('form.usertags', 'category_acl_specific', $usertags, '', array('category' => $category->id)); ?>
							</div>
						</div>
					<?php } ?>
				</div>

				<?php if ($this->config->get('layout_categoryavatar')) { ?>
				<div class="form-group">
					<label class="col-md-3 control-label"><?php echo JText::_('COM_EASYBLOG_DASHBOARD_CATEGORIES_AVATAR'); ?></label>
					<div class="col-md-7">
						<?php if(! empty($category->avatar)) { ?>
							<img style="border-style:solid;" src="<?php echo $category->getAvatar(); ?>" width="60" height="60"/><br />
						<?php } ?>

						<?php if($this->acl->get('upload_cavatar')){ ?>
							<input id="file-upload" type="file" name="Filedata" size="33" title="<?php echo JText::_('COM_EASYBLOG_PICK_AN_IMAGE');?>" />
						<?php } ?>
					</div>
				</div>
				<?php } ?>

			</div>
		</div>
	</div>

	<div class="form-actions">
		<div class="pull-left">
			<a href="<?php echo EBR::_('index.php?option=com_easyblog&view=dashboard&layout=categories');?>" class="btn btn-default"><?php echo JText::_('COM_EASYBLOG_CANCEL_BUTTON');?></a>
		</div>

		<div class="pull-right">
			<button class="btn btn-primary" data-submit-button>
				<?php echo ($category->id) ? JText::_('COM_EASYBLOG_UPDATE_BUTTON') : JText::_('COM_EASYBLOG_CREATE_BUTTON'); ?>
			</button>
		</div>
	</div>

	<input type="hidden" name="id" value="<?php echo $category->id;?>" />
	<?php echo $this->html('form.action', 'categories.save'); ?>
</form>
