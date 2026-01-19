<?php
/**
* @package      PayPlans
* @copyright    Copyright (C) Stack Ideas Sdn Bhd. All rights reserved.
* @license      GNU/GPL, see LICENSE.php
* PayPlans is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/
defined('_JEXEC') or die('Unauthorized Access');
?>
<div class="app-tree" <?php echo $attributes;?>>
	<div>
		<span class="t-lg-pr--md"><a href="javascript:void(0);" data-articlelist-all><?php echo JText::_('COM_PP_ARTICLELIST_SELECT_ALL'); ?></a></span> |
		<span class="t-lg-pl--md"><a href="javascript:void(0);" data-articlelist-none><?php echo JText::_('COM_PP_ARTICLELIST_SELECT_NONE'); ?></a></span>
	</div>
	<hr />
<?php foreach ($articles as $article) { ?>
	<?php
		$checked = '';
		if ($selected) {
			$checked = in_array($article->id, $selected) ? ' checked="checked"' : '';
		}
	?>
	<div class="tree-control">
		<label for="<?php echo $article->id;?>" class="checkbox">
			<input data-article-item type="checkbox" id="<?php echo $article->id;?>" value="<?php echo $article->id;?>" name="<?php echo $name;?>[]"<?php echo $checked;?> />
			<div class="tree-title">
				<?php echo $article->title;?>
			</div>
		</label>
	</div>
		
<?php } ?>
</div>
