<?php
/**
 * @version         1.9.7
 * @package        Joomla
 * @subpackage     EDocman
 * @author         Tuan Pham Ngoc
 * @copyright      Copyright (C) 2011 - 2018 Ossolution Team
 * @license        GNU/GPL, see LICENSE.php
 */
// no direct access
defined( '_JEXEC' ) or die ;
EDocmanHelper::loadBootstrapJs();
$controlGroupClass = $bootstrapHelper->getClassMapping('control-group');
$controlLabelClass = $bootstrapHelper->getClassMapping('control-label');
$controlsClass     = $bootstrapHelper->getClassMapping('controls');
?>
<div id="sharing-form" class="modal fade" style="display: none;">
	<div class="modal-header">
		<a class="close" data-dismiss="modal">x</a>
		<h2><?php echo JText::_('EDOCMAN_SHARING_DOCUMENT'); ?></h2>
	</div>
	<div class="alert alert-success">
		<a href="#" class="close" data-dismiss="alert">&times;</a>
		<p id="sharing-complete-message"></p>
	</div>
	<div class="modal-body">
		<form class="edocman-download-form form form-horizontal" name="download-form">
			<p class="text-info" id="sharing-instruction"><?php echo JText::_('EDOCMAN_COMPLETE_FORM_TO_SHARE'); ?></p>
			<div class="<?php echo $controlGroupClass;  ?>">
				<label class="<?php echo $controlLabelClass; ?>" for="name">
					<?php echo  JText::_('EDOCMAN_YOUR_NAME') ?><span class="required">*</span>
				</label>
				<div class="<?php echo $controlsClass; ?>">
					<input type="text" name="name" id="name" class="input-large"/>
				</div>
			</div>
			<div class="<?php echo $controlGroupClass;  ?>">
				<label class="<?php echo $controlLabelClass; ?>" for="name">
					<?php echo  JText::_('EDOCMAN_YOUR_FRIEND_NAME') ?><span class="required">*</span>
				</label>
				<div class="<?php echo $controlsClass; ?>">
					<input type="text" name="friend_name" id="friend_name" class="input-large"/>
				</div>
			</div>
			<div class="<?php echo $controlGroupClass;  ?>">
				<label class="<?php echo $controlLabelClass; ?>" for="name">
					<?php echo  JText::_('EDOCMAN_YOUR_FRIEND_EMAIL') ?><span class="required">*</span>
				</label>
				<div class="<?php echo $controlsClass; ?>">
					<input id="friend_email" type="text" name="friend_email" value="" class="input-large"><br>
				</div>
			</div>
			<div class="<?php echo $controlGroupClass;  ?>">
				<label class="<?php echo $controlLabelClass; ?>" for="name">
					<?php echo  JText::_('EDOCMAN_MESSAGE') ?>
				</label>
				<div class="<?php echo $controlsClass; ?>">
					<textarea name="message" id="message" cols="50" rows="5" style="width:250px !important;"></textarea>
				</div>
			</div>
		</form>
	</div>
	<div class="modal-footer">
		<input class="btn btn-success btn-send" type="button" value="<?php echo JText::_('EDOCMAN_PROCESS'); ?>">
		<a href="#" class="btn" data-dismiss="modal"><?php echo JText::_('EDOCMAN_CLOSE'); ?></a>
	</div>
</div>

<script id="dynamic" type="text/javascript">
        if (typeof(Edocman) === 'undefined') {
            var Edocman = {};
        }
        Edocman.jQuery = jQuery.noConflict()
		Edocman.jQuery(document).ready(function($){
			$('.progress').hide();
			$('.alert-success').hide();
			var $modal = $('#sharing-form');
			$('.email-popup').click(function(){
				var documentTitle = $(this).attr('data-document-title');
				var instruction = $('#sharing-instruction').html();
				instruction = instruction.replace('[DOCUMENT_TITLE]', documentTitle);
				$('#sharing-instruction').html(instruction);
				$('.btn-send').attr('id',$(this).attr('id'));
			})

			$('#form-content').modal({
				show : false,
				backdrop: true,
				keyboard: true
			}).css({
				width: '500px',
				'margin-left': function () {
					return -($(this).width() / 2);
				}
			});
			//bootstrap 3
			//$(document).on('hide.bs.modal','#form-content', function () {  window.location = '<?php echo JFactory::getUri()->toString(); ?>' });
			//bootstrap 2
			$modal.on('hidden', function () { window.location = '<?php echo JFactory::getUri()->toString(); ?>' });
			$modal.on('click', '.btn-send', function(e){
				e.preventDefault();
				var validate = validateForm();
				if(validate)
				{
					$('.btn-send').attr('disabled','disabled');
					$('.btn-send').before('<span class="wait">&nbsp;<img src="components/com_edocman/assets/images/loading.gif" alt="" /></span>');
					var documentId = $(this).attr('id');
					var data = {
						'task'	:	'document.share_document',
						'document_id' : documentId,
						'name': $('#name').val(),
						'friend_name': $('#friend_name').val(),
						'friend_email' : $('#friend_email').val()
					};
					$.ajax({
						type: 'POST',
						url: 'index.php?option=com_edocman',
						data: data,
						dataType: 'html',
						success: function(html) {
							$('.modal-body').hide();
							$('#sharing-complete-message').html(html);
							$('.wait').remove();
							$('.alert-success').show('fast');
						},
						error: function(jqXHR, textStatus, errorThrown) {
							alert(textStatus);
						}
					});
				}
			})
			function validateForm()
			{
				var emailReg = /^([\w-\.]+@([\w-]+\.)+[\w-]{2,4})?$/;
				var names = $('#name').val();
				var friend_names = $('#friend_name').val();
				var friend_email = $('#friend_email').val();
				var inputVal = new Array(names, friend_names, friend_email);

				var inputMessage = new Array("name", "friend name", "email address");
				$('.error').hide();
				if(inputVal[0] == ""){
					$('#name').after('<span class="error"> Please enter your ' + inputMessage[0] + '</span>');
					return false;
				}
				if(inputVal[1] == ""){
					$('#friend_name').after('<span class="error"> Please enter your ' + inputMessage[1] + '</span>');
					return false;
				}
				if(inputVal[2] == ""){
					$('#friend_email').after('<span class="error"> Please enter your ' + inputMessage[2] + '</span>');
					return false;
				}
				else if(!emailReg.test(friend_email)){
					$('#friend_email').after('<span class="error"> Please enter a valid email address</span>');
					return false;
				}
				return true;
			}

		})
</script>
