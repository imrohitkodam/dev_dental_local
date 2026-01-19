<?php
	defined('_JEXEC') or die;
	if (count($tags)) {
	?>
		<ul class="accordion <?php echo 'accordion_'.$moduleclass; ?>">
			<?php				
				foreach ($tags as $row)
                {
				?>
					<li>
						<a href="<?php echo JRoute::_('index.php?option=com_edocman&view=search&filter_tag='.urlencode($row->tag)) ?>"><?php echo $row->tag; ?><span><?php echo $row->total ?></span></a>
					</li>
				<?php	
				}
			?>			
		</ul>
<?php
	}
?>					

