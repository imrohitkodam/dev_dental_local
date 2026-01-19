<?php
	defined('_JEXEC') or die;
	if (count($rows)) {		
	?>
		<ul class="menu">
			<?php				
				foreach ($list as $row)
                {
				?>
					<li>
						<a href="<?php echo EDocmanHelperRoute::getCategoryRoute($row->id, $itemId); ?>"><?php echo $row->treename; ?></a>
					</li>
				<?php	
				}
			?>			
		</ul>
<?php
	}
?>					

