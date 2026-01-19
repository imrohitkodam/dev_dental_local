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
<div class="es-widget is-module">
	<div class="es-widget-head">
		<div class="pull-left widget-title">
			<?php echo JText::_('APP_PAGE_FILES_WIDGET_TITLE');?>
		</div>
		<span class="widget-label">(<?php echo $total;?>)</span>
	</div>

	<div class="es-widget-body recent-files">
		<?php if ($files) { ?>
		<ul class="o-nav o-nav--stacked">
			<?php foreach ($files as $file) { ?>
			<li>
				<div class="row">
					<div class="col-md-12">
						<a href="<?php echo $file->getPreviewURI();?>" target="_blank"><i class="icon-es-<?php echo $file->getIconClass();?> mr-5"></i> <?php echo $file->name; ?></a>

						<div class="t-fs--sm t-lg-pull-right t-lg-mr--md">
							<?php echo $file->getSize('kb');?> <?php echo JText::_('COM_EASYSOCIAL_UNIT_KILOBYTES');?>
						</div>
					</div>
				</div>

				<div class="row author-info mt-5">
					<div class="col-md-12 t-fs--sm">
						<i class="fa fa-user"></i> <?php echo JText::sprintf('APP_PAGE_FILES_UPLOADED_BY' , $this->html('html.user' , $file->user_id , true)); ?>
					</div>
				</div>
			</li>
			<?php } ?>
		</ul>
		<?php } else { ?>
		<div class="t-fs--sm">
			<?php echo JText::_('APP_PAGE_FILES_EMPTY_FILES'); ?>
		</div>
		<?php } ?>
	</div>
</div>
