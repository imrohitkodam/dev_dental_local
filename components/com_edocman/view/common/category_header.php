<div class="sortPagiBar <?php echo $bootstrapHelper->getClassMapping('row-fluid'); ?>">
	<div class="<?php echo $bootstrapHelper->getClassMapping('span3'); ?>">
		<?php
			if ($showLayoutswitcher)
			{
			?>
				<strong><?php echo JText::_('EDOCMAN_DISPLAY'); ?></strong>
				<div class="btn-group <?php $bootstrapHelper->getClassMapping('hidden-phone'); ?>">
					<a rel="grid" href="#" class="btn"><i class="edocman-icon-th" title="<?php echo JText::_('EDOCMAN_GRID'); ?>"></i></a>
					<a rel="list" href="#" class="btn"><i class="edocman-icon-th-list" title="<?php echo JText::_('EDOCMAN_LIST'); ?>"></i></a>
				</div>
			<?php
			}
		?>
	</div>
	<div class="<?php echo $bootstrapHelper->getClassMapping('span9'); ?>">
		<div class="clearfix pull-right sortPagiBarRight">
			<div class="edocman-sort-direction">
				<?php echo $lists['filter_order_Dir'] ?>
			</div>
			<div class="edocman-document-sorting">
				<?php echo $lists['filter_order']; ?>
			</div>
		</div>
	</div>
</div>
<div class="clearfix"></div>