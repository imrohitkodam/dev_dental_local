<?php 
/**
 * @version        1.7.2
 * @package        Joomla
 * @subpackage     EDocman
 * @author         Tuan Pham Ngoc
 * @copyright	Copyright (C) 2011 - 2013 Ossolution Team
 * @license        GNU/GPL, see LICENSE.php
 */
defined('_JEXEC') or die;
JToolBarHelper::title(   JText::_( 'EDOCMAN_BATCH_UPLOAD_STEP_1' ), 'upload' );
JToolBarHelper::save('edit_documents', JText::_('EDOCMAN_NEXT_STEP'));
JHtml::_('formbehavior.chosen', 'select');
?>
<form action="<?php echo JRoute::_('index.php?option=com_edocman&view=upload');?>" method="post" name="adminForm" id="adminForm">
<div class="row-fluid">	
	<table width="100%" class="adminform">
		<tr>
			<td colspan="2">
				<p class="message">
					<?php echo JText::_('EDOCMAN_BULK_UPLOAD_INSTRUCTIONS'); ?>
				</p>
			</td>
		</tr>
		<tr>
			<td class="key">
				<?php echo JText::_('EDOCMAN_CATEGORY');?>
			</td>
			<td>
				<?php echo $this->lists['category_id'];?>
			</td>
		</tr>
        <?php
        if(!$this->config->access_level_inheritance) {
            ?>
            <tr>
                <td class="key" valign="top">
                    <?php echo JText::_('EDOCMAN_ACCESS'); ?>
                </td>
                <td>
                    <?php
                    EDocmanHelper::showCheckboxfield('accesspicker', 0, JText::_('EDOCMAN_PRESETS'), JText::_('EDOCMAN_GROUPS'));
                    ?>
                    <div id="presetsdiv" style="padding-top:5px;">
                        <?php
                        echo $this->lists['access'];
                        ?>
                    </div>
                    <div id="groupsdiv" style="display:none;padding-top:5px;">
                        <?php
                        echo $this->lists['groups'];
                        ?>
                    </div>
                </td>
            </tr>
            <?php
        }
        ?>
		<tr>
			<td class="key">
				<?php echo JText::_('EDOCMAN_PUBLISHED'); ?>
			</td>
			<td>
				<?php echo $this->lists['published']; ?>
			</td>
		</tr>
		<tr>
			<td colspan="2">
				<div id="uploader"><p>Your browser doesn't have Flash, Silverlight or HTML5 support.</p></div>	
			</td>
		</tr>
	</table>
</div>
	<input type="hidden" name="task" value="" />	
	<?php echo JHtml::_('form.token'); ?>
</form>
<script type="text/javascript">
	var filesCount = 0;
	Joomla.submitbutton = function(task)
	{
		var form = document.adminForm;
		if (form.category_id.value == 0)
		{
			alert("<?php echo JText::_('EDOCMAN_PLEASE_SELECT_CATEGORY'); ?>");
			form.category_id.focus();
			return;
		}
		Joomla.submitform(task, form);
	}

	Edocman.jQuery(function($) {
		$("#uploader").pluploadQueue({
			runtimes : 'html5,flash,silverlight,html4',
			url : '<?php echo JUri::root() ?>administrator/index.php?option=com_edocman&task=upload.upload&format=json',
			max_file_size : '<?php echo $this->maxFilesize; ?>mb',
			unique_names : false,
			flash_swf_url : '<?php echo JUri::root(); ?>components/com_edocman/assets/js/plupload/plupload.flash.swf',
			multipart_params : {
				"<?php echo JSession::getFormToken();?>" : "1"
			},
			init: EdocmanMediaUploaderCallBacks
		});

		function EdocmanMediaUploaderCallBacks( uploader )
		{
			uploader.bind('BeforeUpload', function(up, file) {
				var categoryId = $('#category_id').val();
				up.settings.multipart_params = {"category_id":  categoryId, "<?php echo JSession::getFormToken();?>" : "1"};
			});
			uploader.bind( 'Error', function( up, args ) {
			} );

			uploader.bind( 'FileUploaded', function( up, file, response ) {
				var res = response.response;
				if ( res ) {
					var objResponse = jQuery.parseJSON( res );
					if ( typeof objResponse.error != 'undefined')
					{
						up.trigger( 'Error', {
							code:    -300,
							message: 'Upload Failed',
							details: file.name + ' failed',
							file:    file
						} );
						return false;
					}
					else
					{
												
					}
				}
			} );
		}
	});

    function updateRadioButton(select_item){
        if(select_item == '0'){
            jQuery('#presetsdiv').slideDown();
            jQuery('#groupsdiv').slideUp();
        }else{
            jQuery('#presetsdiv').slideUp();
            jQuery('#groupsdiv').slideDown();
        }
    }
</script>