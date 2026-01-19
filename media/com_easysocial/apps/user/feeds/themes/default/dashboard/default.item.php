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

<div class="o-box t-lg-mb--lg" data-id="<?php echo $feed->id;?>" data-item>
	<div>
		<a href="<?php echo $feed->url;?>" class="btn btn-link btn-sm t-text--bold t-lg-pl--no t-lg-pull-left" target="_blank">
		    <i class="fa fa-rss-square"></i> <?php echo $feed->title;?>
		</a>

		<div class="t-lg-pull-right">
		    <ol class="g-list--horizontal has-dividers--right">
		        <li class="g-list__item">
		        	<?php echo ES::date($feed->created)->toLapsed();?>
		        </li>
		        <li class="g-list__item">
		        	<a href="javascript:void(0);" class="btn btn-es-danger btn-sm" data-feeds-item-remove><?php echo JText::_('APP_FEEDS_REMOVE_ITEM');?></a>
		        </li>
		    </ol>
		</div>
	</div>
</div>