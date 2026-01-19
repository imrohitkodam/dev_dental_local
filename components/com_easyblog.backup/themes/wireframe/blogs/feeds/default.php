<?php
/**
* @package      EasyBlog
* @copyright    Copyright (C) 2010 - 2016 Stack Ideas Sdn Bhd. All rights reserved.
* @license      GNU/GPL, see LICENSE.php
* EasyBlog is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/
defined('_JEXEC') or die('Unauthorized Access'); 
?>
<article>
	<header>

		<?php if ($post->hasImage()) { ?>
		<figure>
			<img src="<?php echo $post->getImage('large', true, true); ?>">	
		</figure>
		<?php } ?>

		<h1><?php echo $post->title;?></h1>

		<!-- A kicker for your article -->
		<h3 class="op-kicker"><?php echo JText::sprintf('COM_EASYBLOG_INSTANT_ARTICLE_FEED_CATEGORY', $category->title);?></h3>

		<address><?php echo $author->getName();?></address>

		<!-- The published and last modified time stamps -->
		<time class="op-published" dateTime="<?php echo $post->getCreationDate(true)->toISO8601(true);?>"><?php echo $post->getCreationDate()->format(JText::_('DATE_FORMAT_LC1'));?></time>
		<time class="op-modified" dateTime="<?php echo $post->getModifiedDate()->toISO8601(true);?>"><?php echo $post->getModifiedDate()->format(JText::_('DATE_FORMAT_LC1'));?></time>
	</header>

	<?php if ($post->hasLocation()) {  ?>
		<figure class="op-map">
		  <script type="application/json" class="op-geotag">  
		    {
		      "type": "Feature",
		      "geometry": 
		        {
		          "type": "Point",
		          "coordinates": [<?php echo $post->latitude; ?>, <?php echo $post->longitude; ?>]    
		        },    
		      "properties": 
		        {      
		          "title": "<?php echo $post->address ?>",     
		          "pivot": true,      
		          "style": "satellite",
		        }
		     }  
		  </script>
		</figure>
	<?php } ?>
	
	<?php echo $post->getInstantContent();?>

	<footer>
		<aside><?php echo JText::sprintf('COM_EASYBLOG_INSTANT_ARTICLE_FEED_PUBLISHED_ON', $site);?></aside>
	</footer>
</article>