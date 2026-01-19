<?php
/**
 * @package Freestyle Joomla
 * @author Freestyle Joomla
 * @copyright (C) 2013 Freestyle Joomla
 * @license GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
**/
defined('_JEXEC') or die;
?>

<?php echo FSS_Helper::PageStylePopup(true); ?>
<?php echo FSS_Helper::PageTitlePopup("CANNED_PRESET_REPLIES"); ?>

<?php $in_table = false; $grouping = "xxxxxx"; $set=1;?>

<div id="form_reply_list">
	<?php foreach ($this->canned as $canned): ?>
		<?php 
			$canned->category = ltrim($canned->category, "0123456789 ");
			if ($canned->category != $grouping)
			{
				$set++;
				if ($in_table) echo "</ul></div></div>";
				$in_table = false;
				
				
				echo "<div>";
			
				if ($canned->category)
				{
					echo "<h4 style='margin-top:4px;margin-bottom:4px'>";	
					echo '<a class="collapsed" data-toggle="collapse" data-parent="#form_reply_list" href="#set'.$set.'"><i class="icon-arrow-right"></i> ';
					echo $canned->category;
					echo "</a></h4>";
				}
				
				$grouping = $canned->category;
			}

		if (!$in_table) : ?>
			<div class="container <?php if ($grouping != '') echo 'collapse'; ?>" id="set<?php echo $set; ?>"> <ul class="unstyled">
		<?php endif; $in_table = true;?>
			<li>
				<a href="<?php echo JRoute::_('index.php?option=com_fsj_fssadd&view=canned&layout=form&tmpl=component&canned=' . $canned->id . "&ticket=" . JRequest::getVar('ticket') . "&message=" . JRequest::getVar('message')); ?>">
					<?php echo $canned->title; ?>
				</a>
			</li>
			<?php if ($canned->notes): ?>
				<li style="margin-left: 16px;">
					<?php echo $canned->notes; ?>
				</li>
			<?php endif; ?>
	<?php endforeach; ?>
	</ul>
	</div></div>

</div>

</div>

<div class="modal-footer fss_main">
	<a href="#" class="btn btn-default close_popup simplemodal-close" data-dismiss="modal"><?php echo JText::_('CANNED_CLOSE'); ?></a>
</div>
