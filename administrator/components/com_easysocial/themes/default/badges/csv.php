<?php
/**
* @package      EasySocial
* @copyright    Copyright (C) 2010 - 2017 Stack Ideas Sdn Bhd. All rights reserved.
* @license      GNU/GPL, see LICENSE.php
* EasySocial is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/
defined('_JEXEC') or die('Unauthorized Access');
?>
<form name="adminForm" id="adminForm" class="pointsForm" method="post" enctype="multipart/form-data">
<div class="row">
	<div class="col-md-6">
		<div class="panel">
			<div class="panel-head">
				<b><?php echo JText::_('COM_EASYSOCIAL_BADGES_INSTALL_UPLOAD_CSV');?></b>
				<p><?php echo JText::_('COM_EASYSOCIAL_BADGES_INSTALL_UPLOAD_CSV_DESC');?></p>
			</div>
			<div class="panel-body">
				<code>"USER_ID"</code> , <code>"BADGE_ID"</code> , <code>"ACHIEVED_DATE"</code> , <code>"CUSTOM_MESSAGE"</code> , <code>"PUBLISH_STREAM"</code>
				<div class="mb-20 mt-20">
					<ul class="g-list-unstyled">
						<li>
							<code>USER_ID</code> - <?php echo JText::_('COM_EASYSOCIAL_BADGES_CSV_USER_ID_DESC'); ?>
						</li>
						<li class="mt-5">
							<code>BADGE_ID</code> - <?php echo JText::_('COM_EASYSOCIAL_BADGES_CSV_ID_DESC'); ?>
						</li>
						<li class="mt-5">
							<code>ACHIEVED_DATE</code> (<?php echo JText::_('COM_EASYSOCIAL_OPTIONAL');?>) - <?php echo JText::_('COM_EASYSOCIAL_BADGES_CSV_DATE_DESC'); ?>
						</li>
						<li class="mt-5">
							<code>CUSTOM_MESSAGE</code> (<?php echo JText::_('COM_EASYSOCIAL_OPTIONAL');?>) - <?php echo JText::_('COM_EASYSOCIAL_BADGES_CSV_CUSTOM_MSG'); ?>
						</li>
						<li class="mt-5">
							<code>PUBLISH_STREAM</code> (<?php echo JText::_('COM_EASYSOCIAL_OPTIONAL');?>) - <?php echo JText::_('COM_EASYSOCIAL_BADGES_CSV_PUBLISH_STREAM'); ?>
						</li>
					</ul>
				</div>
				<div>
					<input type="file" name="package" id="package" class="input" style="width:265px;" data-uniform />
					<button class="btn btn-small btn-es-primary installUpload"><?php echo JText::_('Upload CSV File');?> &raquo;</button>
				</div>
			</div>
		</div>
	</div>
</div>
<input type="hidden" name="option" value="com_easysocial" />
<input type="hidden" name="controller" value="badges" />
<input type="hidden" name="task" value="massAssign" />
<?php echo JHTML::_('form.token');?>
</form>