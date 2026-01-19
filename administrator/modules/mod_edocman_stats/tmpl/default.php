<?php
/**
* @version		2.7.x
* @package		K2
* @author		JoomlaWorks http://www.joomlaworks.net
* @copyright	Copyright (c) 2006 - 2016 JoomlaWorks Ltd. All rights reserved.
* @license		GNU/GPL license: http://www.gnu.org/copyleft/gpl.html
*/

// no direct access
defined('_JEXEC') or die;
define('K2_JVERSION','30');
// Quick and dirty fix for Joomla! 3.0 missing CSS tabs when creating tabs using the API.
// Should be removed when Joomla! fixes that...
if (K2_JVERSION == '30')
{
	$document = JFactory::getDocument();
	$document->addStyleDeclaration('
		dl.tabs {float:left;margin:10px 0 -1px 0;z-index:50;}
		dl.tabs dt {float:left;padding:4px 10px;border:1px solid #ccc;margin-left:3px;background:#e9e9e9;color:#666;}
		dl.tabs dt.open {background:#F9F9F9;border-bottom:1px solid #f9f9f9;z-index:100;color:#000;}
		div.current {clear:both;border:1px solid #ccc;padding:10px 10px;}
		dl.tabs h3 {font-size:12px;line-height:12px;margin:4px;}
');
}

// Import Joomla! tabs
jimport('joomla.html.pane');

?>

<?php if(K2_JVERSION != '30') $pane = JPane::getInstance('Tabs'); ?>

<div class="clr"></div>

<?php echo (K2_JVERSION == '30') ? JHtml::_('tabs.start') : $pane->startPane('myPane'); ?>

<?php if($params->get('latestItems', 1)): ?>
<?php echo (K2_JVERSION == '30') ? JHtml::_('tabs.panel', JText::_('Latest Documents'), 'latestItemsTab') : $pane->startPanel(JText::_('Latest Documents'), 'latestItemsTab'); ?>
<!--[if lte IE 7]>
<br class="ie7fix" />
<![endif]-->
<table class="adminlist table table-striped">
	<thead>
		<tr>
			<td class="title"><?php echo JText::_('Document Title'); ?></td>
			<td class="title"><?php echo JText::_('Created'); ?></td>
			<td class="title"><?php echo JText::_('Author'); ?></td>
		</tr>
	</thead>
	<tbody>
		<?php foreach($latestItems as $latest): ?>
		<tr>
			<td><a href="<?php echo JRoute::_('index.php?option=com_edocman&task=document.edit&id='.$latest->id); ?>"><?php echo $latest->title; ?></a></td>
			<td><?php echo JHTML::_('date', strtotime($latest->created_time) , 'd/m/Y - H:M'); ?></td>
			<td><?php echo $latest->author; ?></td>
		</tr>
		<?php endforeach; ?>
	</tbody>
</table>
<?php if(K2_JVERSION != '30') echo $pane->endPanel(); ?>
<?php endif; ?>

<?php if($params->get('popularItems', 1)): ?>
<?php echo (K2_JVERSION == '30') ? JHtml::_('tabs.panel', JText::_('Popular Documents'), 'popularItemsTab') : $pane->startPanel(JText::_('Popular Documents'), 'popularItemsTab'); ?>
<!--[if lte IE 7]>
<br class="ie7fix" />
<![endif]-->
<table class="adminlist table table-striped">
	<thead>
		<tr>
			<td class="title"><?php echo JText::_('Document Title'); ?></td>
			<td class="title"><?php echo JText::_('Hits'); ?></td>
			<td class="title"><?php echo JText::_('Created'); ?></td>
			<td class="title"><?php echo JText::_('Author'); ?></td>
		</tr>
	</thead>
	<tbody>
		<?php foreach($popularItems as $popular): ?>
		<tr>
			<td><a href="<?php echo JRoute::_('index.php?option=com_edocman&task=document.edit&id='.$popular->id); ?>"><?php echo $popular->title; ?></a></td>
			<td><?php echo $popular->hits; ?></td>
			<td><?php echo JHTML::_('date', strtotime($popular->created_time) , 'd/m/Y - H:M'); ?></td>
			<td><?php echo $popular->author; ?></td>
		</tr>
		<?php endforeach; ?>
	</tbody>
</table>
<?php if(K2_JVERSION != '30') echo $pane->endPanel(); ?>
<?php endif; ?>

<?php if($params->get('mostDownloadItems', 1)): ?>
<?php echo (K2_JVERSION == '30') ? JHtml::_('tabs.panel', JText::_('Most Downloaded Documents'), 'mostCommentedItemsTab') : $pane->startPanel(JText::_('Most Downloaded Documents'), 'mostCommentedItemsTab'); ?>
<!--[if lte IE 7]>
<br class="ie7fix" />
<![endif]-->
<table class="adminlist table table-striped">
	<thead>
		<tr>
			<td class="title"><?php echo JText::_('Document Title'); ?></td>
			<td class="title"><?php echo JText::_('Downloaded'); ?></td>
			<td class="title"><?php echo JText::_('Created'); ?></td>
			<td class="title"><?php echo JText::_('Author'); ?></td>
		</tr>
	</thead>
	<tbody>
		<?php foreach($mostDownloadedItems as $mostCommented): ?>
		<tr>
			<td><a href="<?php echo JRoute::_('index.php?option=com_edocman&task=document.edit&id='.$mostCommented->id); ?>"><?php echo $mostCommented->title; ?></a></td>
			<td><?php echo $mostCommented->downloads; ?></td>
			<td><?php echo JHTML::_('date', strtotime($popular->created_time) , 'd/m/Y - H:M'); ?></td>
			<td><?php echo $mostCommented->author; ?></td>
		</tr>
		<?php endforeach; ?>
	</tbody>
</table>
<?php if(K2_JVERSION != '30') echo $pane->endPanel(); ?>
<?php endif; ?>

<?php if($params->get('statistics', 1)): ?>
<?php echo (K2_JVERSION == '30') ? JHtml::_('tabs.panel', JText::_('Statistics'), 'statsTab') : $pane->startPanel(JText::_('Statistics'), 'statsTab'); ?>
<!--[if lte IE 7]>
<br class="ie7fix" />
<![endif]-->
<table class="adminlist table table-striped">
	<thead>
		<tr>
			<td class="title"><?php echo JText::_('Type'); ?></td>
			<td class="title"><?php echo JText::_('Count'); ?></td>
		</tr>
	</thead>
	<tbody>
		<tr>
			<td><?php echo JText::_('Documents'); ?></td>
			<td><?php echo $statistics->numOfItems; ?></td>
		</tr>
		<tr>
			<td><?php echo JText::_('Categories'); ?></td>
			<td><?php echo $statistics->numOfCategories; ?></td>
		</tr>
		<tr>
			<td><?php echo JText::_('Tags'); ?></td>
			<td><?php echo $statistics->numOfTags; ?></td>
		</tr>
	</tbody>
</table>
<?php if(K2_JVERSION != '30') echo $pane->endPanel(); ?>
<?php endif; ?>

<?php echo K2_JVERSION != '30'? $pane->endPane() : JHtml::_('tabs.end'); ?>
