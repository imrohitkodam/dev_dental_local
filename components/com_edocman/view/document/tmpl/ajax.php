<?php
/**
 * @version        1.11.3
 * @package        Joomla
 * @subpackage     Edocman
 * @author         Dang Thuc Dam
 * @copyright      Copyright (C) 2011 - 2018 Ossolution Team
 * @license        GNU/GPL, see LICENSE.php
 */

// no direct access
defined('_JEXEC') or die;
// Load the tooltip behavior.
JHtml::_('behavior.tooltip');
JHtml::_('behavior.formvalidation');
JHtml::_('behavior.keepalive');
//in case the Upload limit is turned on
if(EDocmanHelper::isUploadLimitTurnedOn())
{
    $session = JFactory::getSession();
    $session->set('files', array());
    $session->set('filesize', null);
    $session->set('originalFiles', null);
    $session->set('fileid', null);
}
?>
<script type="text/javascript">
	function submitDocument()
    {
		if (document.formvalidator.isValid(document.id('item-form')))
		{
            if (document.getElementById('jformcategory_id').value == 0)
            {
                alert("<?php echo JText::_('EDOCMAN_PLEASE_SELECT_CATEGORY'); ?>");
                document.getElementById('jformcategory_id').focus();
                return;
            }
			var answer = confirm('<?php echo JText::_('EDOCMAN_SAVE_DOCUMENT_CONFIRM');?>');
			if(answer == 1)
			{
				Joomla.submitform('upload.edit_documents', document.getElementById('item-form'));
			}
		}
		else
		{
			alert('<?php echo $this->escape(JText::_('JGLOBAL_VALIDATION_FORM_FAILED'));?>');
		}
	}
	function cancelSubmit() {
		var form = document.getElementById('item-form') ;
		form.task.value = 'document.cancel' ;
		form.submit() ;
	}
</script>

<form action="<?php echo JRoute::_('index.php?option=com_edocman&layout=edit&id=0'); ?>" method="post" name="adminForm" id="item-form" class="form-validate" enctype="multipart/form-data">
	<?php
	if ($this->params->get('page_heading'))
	{
		$heading = $this->params->get('page_heading');
	}
	else
	{
		$heading = JText::_('EDOCMAN_UPLOAD_DOCUMENT');
	}
	?>
	<h1 class="edocman-page-heading"><?php echo $heading; ?></h1>
	<?php
	if($this->header_text != ""){
		?>
		<strong><?php echo $this->header_text;?></strong>
		<?php
	}
	?>
    <table class="adminform" width="100%" style="border:0px !important;">
        <?php
            if ($this->catId)
            {
            ?>
                <tr>
                    <td class="edocman_title_col">
                        <?php echo $this->form->getLabel('category_id'); ?>
                    </td>
                    <td class="edocman_field_cell">
                        <input type="hidden" name="jform[category_id]" id="jformcategory_id" value="<?php echo $this->catId; ?>" />
                        <?php echo $this->categoryTitle; ?>
                    </td>
                </tr>
            <?php
            }
            else
            {
            ?>
                <tr>
                    <td class="edocman_title_col">
                        <?php echo $this->form->getLabel('category_id'); ?>
                    </td>
                    <td class="edocman_field_cell">
                        <?php echo $this->form->getInput('category_id'); ?>
                    </td>
                </tr>
            <?php
            }
        ?>
        <tr>
            <td colspan="2">
                <div id="uploader"><p>Your browser doesn't have Flash, Silverlight or HTML5 support.</p></div>
            </td>
        </tr>
		<tr>
			<td  style="float: left;">
				<input type="button" class="btn btn-warning" onclick="cancelSubmit();" value="<?php echo JText::_('EDOCMAN_CANCEL'); ?>" />
				<input type="button" class="btn btn-success" onclick="submitDocument();" value="<?php echo JText::_('EDOCMAN_NEXT_STEP'); ?>"  />
			</td>
		</tr>
	</table>
	<input type="hidden" name="task" value="" />
    <input type="hidden" name="Itemid" value="<?php echo JFactory::getApplication()->input->getInt('Itemid',0); ?>" />
	<?php echo JHtml::_('form.token'); ?>
</form>
<script type="text/javascript">
    Edocman.jQuery(function($) {
        $("#uploader").pluploadQueue({
            runtimes : 'html5,flash,silverlight,html4',
            url : '<?php echo JUri::root() ?>index.php?option=com_edocman&task=upload.upload&format=json',
            max_file_size : '<?php echo $this->maxFilesize; ?>mb',
            unique_names : false,
            flash_swf_url : '<?php echo JUri::root(); ?>components/com_edocman/assets/js/plupload/plupload.flash.swf',
            multipart_params : {
                "<?php echo JSession::getFormToken();?>" : "1"
            },
            // Specify what files to browse for
            filters : [
                {title : "<?php echo JText::_('EDOCMAN_ALLOWED_FILE_TYPES'); ?>", extensions : "<?php echo $this->allowedFiletypes; ?>"}
            ],

            // Rename files by clicking on their titles
            rename: true,
            init: EdocmanMediaUploaderCallBacks
        });

        function EdocmanMediaUploaderCallBacks( uploader )
        {
            uploader.bind('BeforeUpload', function(up, file) {
                var categoryId = $('#jformcategory_id').val();
                up.settings.multipart_params = {"category_id":  categoryId, "<?php echo JSession::getFormToken();?>" : "1"};
            });
            uploader.bind( 'Error', function( up, args ) {
            } );

            uploader.bind( 'FileUploaded', function( up, file, response ) {
                var res = response.response;
                if ( res )
                {
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
</script>
