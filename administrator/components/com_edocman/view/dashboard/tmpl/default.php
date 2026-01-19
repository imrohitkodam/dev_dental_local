<?php
/**
 * @version        1.9.7
 * @package        Joomla
 * @subpackage     EDocman
 * @author         Tuan Pham Ngoc
 * @copyright      Copyright (C) 2011 - 2018 Ossolution Team
 * @license        GNU/GPL, see LICENSE.php
 */
JHtml::_('behavior.tooltip');
defined('_JEXEC') or die('');
?>
<table width="100%">
	<tr>
		<td width="33%" valign="top">
		<?php
		if (version_compare(JVERSION, '4.0.0-dev', 'ge')){
			echo JHtml::_('bootstrap.startAccordion', 'statistics_pane', array('active' => 'latest_panel'));
			echo JHtml::_('bootstrap.addSlide', 'statistics_pane', '<i class="icon-file"></i>&nbsp;'.JText::_('EDOCMAN_LASTEST_DOCUMENTS'), 'latest_panel');		
			echo $this->loadTemplate('latest');	
			echo JHtml::_('bootstrap.endSlide');
			echo JHtml::_('bootstrap.addSlide', 'statistics_pane', '<i class="icon-clock"></i>&nbsp;'.JText::_('EDOCMAN_LASTEST_UPDATE'), 'latest_updates');		
			echo $this->loadTemplate('updates');
			echo JHtml::_('bootstrap.endSlide');
			echo JHtml::_('bootstrap.addSlide', 'statistics_pane', '<i class="icon-arrow-up"></i>&nbsp;'.JText::_('EDOCMAN_TOP_HITS'), 'top_hits');		
			echo $this->loadTemplate('hits');
			echo JHtml::_('bootstrap.endSlide');
			echo JHtml::_('bootstrap.addSlide', 'statistics_pane', '<i class="icon-download"></i>&nbsp;'.JText::_('EDOCMAN_TOP_DOWNLOADS'), 'top_downloads');		
			echo $this->loadTemplate('downloads');
			echo JHtml::_('bootstrap.endSlide');
			echo JHtml::_('bootstrap.endAccordion');
		}else{
			echo JHtml::_('sliders.start', 'statistics_pane');
			echo JHtml::_('sliders.panel', '<i class="icon-file"></i>&nbsp;'.JText::_('EDOCMAN_LASTEST_DOCUMENTS'), 'latest_panel');		
			echo $this->loadTemplate('latest');		
			echo JHtml::_('sliders.panel', '<i class="icon-clock"></i>&nbsp;'.JText::_('EDOCMAN_LASTEST_UPDATE'), 'latest_updates');		
			echo $this->loadTemplate('updates');		
			echo JHtml::_('sliders.panel', '<i class="icon-arrow-up"></i>&nbsp;'.JText::_('EDOCMAN_TOP_HITS'), 'top_hits');		
			echo $this->loadTemplate('hits');		
			echo JHtml::_('sliders.panel', '<i class="icon-download"></i>&nbsp;'.JText::_('EDOCMAN_TOP_DOWNLOADS'), 'top_downloads');		
			echo $this->loadTemplate('downloads');		
			echo JHtml::_('sliders.end');
		}
		?>
		</td>
		<td width="47%" valign="top" style="padding-left:20px;">

			<div id="cpanel">
				<?php
					$this->quickiconButton('index.php?option=com_edocman&amp;view=categories', 'icon-48-categories.png', JText::_('EDOCMAN_CATEGORIES'));
					$this->quickiconButton('index.php?option=com_edocman&amp;task=category.add', 'icon-48-category-add.png', JText::_('EDOCMAN_NEW_CATEGORY'));
					$this->quickiconButton('index.php?option=com_edocman&amp;view=documents', 'icon-48-documents.png', JText::_('EDOCMAN_DOCUMENTS'));
					$this->quickiconButton('index.php?option=com_edocman&amp;task=document.add', 'icon-48-document-add.png', JText::_('EDOCMAN_NEW_DOCUMENT'));
					$this->quickiconButton('index.php?option=com_edocman&amp;view=licenses', 'icon-48-licenses.png', JText::_('EDOCMAN_LICENSES'));
					$this->quickiconButton('index.php?option=com_edocman&amp;task=license.add', 'icon-48-license-add.png', JText::_('EDOCMAN_NEW_LICENSE'));
					$this->quickiconButton('index.php?option=com_edocman&amp;view=import', 'icon-48-plugin.png', JText::_('EDOCMAN_BULK_IMPORT'));
					$this->quickiconButton('index.php?option=com_edocman&amp;view=upload', 'icon-48-batchupload.png', JText::_('EDOCMAN_AJAX_UPLOAD'));
                    $this->quickiconButton('index.php?option=com_edocman&amp;view=documents&layout=remove_orphan', 'icon-48-remove_orphan.png', JText::_('EDOCMAN_REMOVE_ORPHAN_DOCUMENTS'));
					$this->quickiconButton('index.php?option=com_edocman&amp;view=language', 'icon-48-languages.png', JText::_('EDOCMAN_TRANSLATION'));
					$this->quickiconButton('index.php?option=com_edocman&amp;view=sefoptimize', 'icon-48-sef.png', JText::_('EDOCMAN_SEF_OPTIMIZE'));
					$this->quickiconButton('index.php?option=com_edocman&amp;view=downloadlogs', 'icon-48-downloadlogs.png', JText::_('EDOCMAN_DOWNLOAD_LOG'));
					$this->quickiconButton('index.php?option=com_edocman&amp;view=configuration', 'icon-48-config.png', JText::_('EDOCMAN_CONFIG'));
					$this->quickiconButton('http://edocmandocs.ext4joomla.com/', 'icon-48-help_header.png', JText::_('EDOCMAN_HELP'));
					$this->quickiconButton('index.php?option=com_edocman', 'icon-48-download.png', JText::_('EDOCMAN_UPDATE_CHECKING'), 'update-check');
				?>
			</div>
		</td>
		<td width="20%" valign="top">
			<img src="<?php echo JUri::base();?>components/com_edocman/assets/images/edocman_small_logo.png" style="width:300px;"/>
			<BR />
			<table width="100%">
				<tr>
					<td width="100%" style="padding:10px;color:#474445;background-color:#F46F20;color:white;">
						Installed version: <?php echo EdocmanHelper::getInstalledVersion();?>
						<BR />
						Author: <a href="http://www.joomdonation.com" target="_blank" style="color:white;"><strong>Ossolution team</strong></a>
						<BR /><BR />
						<strong>Usefull links</strong>
						<BR />
						<i class="icon-new"></i>&nbsp;<a href="http://joomdonation.com/forum/edocman.html" target="_blank" style="color:white;">Forum</a>
						<BR />
						<i class="icon-edit" style="color:white;"></i>&nbsp;<a href="http://joomdonation.com/support-tickets.html" target="_blank" style="color:white;">Support ticket</a>
						<BR />
						<i class="icon-download" style="color:white;"></i>&nbsp;<a href="http://joomdonation.com/my-downloads.html" target="_blank" style="color:white;">Download latest</a>
						<BR />
						<i class="icon-book" style="color:white;"></i>&nbsp;<a href="http://edocmandocs.ext4joomla.com/" target="_blank" style="color:white;">Documentation</a>
						<BR />
					</td>
				</tr>
			</table>
		</td>
	</tr>
</table>

<style>
    #statistics_pane
    {
        margin:0px !important
    }
</style>
<script type="text/javascript">
	var upToDateImg = '<?php echo JUri::base(true).'/components/com_edocman/assets/images/icon-48-jupdate-uptodate.png' ?>';
	var updateFoundImg = '<?php echo JUri::base(true).'/components/com_edocman/assets/images/icon-48-jupdate-updatefound.png';?>';
	var errorFoundImg = '<?php echo JUri::base(true).'/components/com_edocman/assets/images/icon-48-deny.png';?>';
	jQuery(document).ready(function() {
		jQuery.ajax({
			type: 'POST',
			url: 'index.php?option=com_edocman&task=check_update',
			dataType: 'json',
			success: function(msg, textStatus, xhr)
			{
				if (msg.status == 1)
				{
					jQuery('#update-check').find('img').attr('src', upToDateImg).attr('title', msg.message);
					jQuery('#update-check').find('span').text(msg.message);
				}
				else if (msg.status == 2)
				{
					jQuery('#update-check').find('img').attr('src', updateFoundImg).attr('title', msg.message);
					jQuery('#update-check').find('a').attr('href', 'http://joomdonation.com/my-downloads.html');
					jQuery('#update-check').find('span').text(msg.message);
				}
				else
				{
					jQuery('#update-check').find('img').attr('src', errorFoundImg);
					jQuery('#update-check').find('span').text('<?php echo JText::_('EDOCMAN_UPDATE_CHECKING_ERROR'); ?>');
				}
			},
			error: function(jqXHR, textStatus, errorThrown)
			{
				jQuery('#update-check').find('img').attr('src', errorFoundImg);
				jQuery('#update-check').find('span').text('<?php echo JText::_('EDOCMAN_UPDATE_CHECKING_ERROR'); ?>');
			}
		});
	});
</script>