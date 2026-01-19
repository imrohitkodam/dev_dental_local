<?php
/**
 * @version        1.9.7
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
$rowFluidClass	   = $bootstrapHelper->getClassMapping('row-fluid');
$span12Class	   = $bootstrapHelper->getClassMapping('span12');
?>
<div id="form-content" class="modal fade" style="display: none;">
	<div class="modal-header">
		<?php
		if($config->twitter_bootstrap_version == 2){	
		?>
			<a class="close" data-dismiss="modal">x</a>
		<?php } ?>
		<h2><?php echo JText::_('EDOCMAN_DOWNLOAD_DOCUMENT'); ?></h2>
	</div>
	<div class="alert alert-success" id="downloadsuccess">
		<a href="#" class="close" data-dismiss="alert">&times;</a>
		<p id="download-complete-message" class="download-complete-message"></p>
	</div>
	<div class="modal-body-download <?php echo $rowFluidClass;?>">
		<div class="<?php echo $span12Class?>">
			<form class="edocman-download-form form form-horizontal" name="download-form">
				<p class="text-info" id="download-instruction"><?php echo JText::_('EDOCMAN_COMPLETE_FORM_TO_DOWNLOAD'); ?></p>
				<div class="<?php echo $controlGroupClass;  ?>">
					<label class="<?php echo $controlLabelClass; ?>" for="name">
						<?php echo  JText::_('EDOCMAN_NAME') ?><span class="required">*</span>
					</label>
					<div class="<?php echo $controlsClass; ?>">
						<input type="text" name="name" id="edocman-name" class="input-xlarge"/>
					</div>
				</div>

				<div class="<?php echo $controlGroupClass;  ?>">
					<label class="<?php echo $controlLabelClass; ?>" for="name">
						<?php echo  JText::_('EDOCMAN_EMAIL') ?><span class="required">*</span>
					</label>
					<div class="<?php echo $controlsClass; ?>">
						<input id="email" type="email" name="email" value="" class="input-xlarge"><br>
					</div>
				</div>
                <?php
                if($config->turn_on_privacy){
                ?>
                <div class="<?php echo $controlGroupClass;  ?>">
                    <input type="checkbox" name="privacy_agreement" id="privacy_agreement" value="0"/>
                    <span id="privacy_agreement_span">
                    <?php
                    echo JText::_('EDOCMAN_AGREE_PRIVACY_POLICY_MESSAGE');
                    ?>
                    </span>
                </div>
                <?php } ?>
			</div>
		</form>
	</div>
	<div class="modal-footer">
		<input class="btn btn-success btn-send" type="button" value="<?php echo JText::_('EDOCMAN_PROCESS'); ?>">
		<a href="#" class="btn btn-close" data-dismiss="modal"><?php echo JText::_('EDOCMAN_CLOSE'); ?></a>
	</div>
</div>

<script id="dynamic" type="text/javascript">
	if (typeof(Edocman) === 'undefined') {
		var Edocman = {};
	}
	Edocman.jQuery = jQuery.noConflict()
	Edocman.jQuery(document).ready(function($){
		$('.progress').hide();
		$('#downloadsuccess').hide();
		var $modal = $('#form-content');
		$('.email-popup').click(function(){
			var documentTitle = $(this).attr('data-document-title');
			var instruction = $('#download-instruction').html();
			instruction = instruction.replace('[DOCUMENT_TITLE]', documentTitle);
			$('#download-instruction').html(instruction);
			$('.btn-send').attr('id',$(this).attr('id'));
		})

		var screenWidth = $( document ).width();

		if(screenWidth < 400)
		{
			$('#form-content').removeClass('fade');
			$('#form-content').modal({
				show : false,
				backdrop: true,
				keyboard: true
			}).css({
				width: '300px',
				height: '400px',
				'margin-left': function () {
					return 0;
				}
			});
		}
		else
		{
			$('#form-content').modal({
				show : false,
				backdrop: true,
				keyboard: true
			}).css({
				width: '500px',
				height: '400px',
				'margin-left': function () {
					return -($(this).width() / 2);
				}
			});
		}

		//bootstrap 3
		<?php
		if($config->twitter_bootstrap_version == 3){	
		?>
		$(document).on('hide.bs.modal','#form-content', function () {  window.location = '<?php echo JUri::getInstance()->toString();?>' });
		<?php
		}elseif($config->twitter_bootstrap_version == 2){	
		?>
		//bootstrap 2
		$modal.on('hidden', function () { window.location = '<?php echo JUri::getInstance()->toString();?>' });
		<?php
		}elseif($config->twitter_bootstrap_version == 4){	
		?>
		$modal.on('click', '.btn-close', function(e){
			window.location = '<?php echo JUri::getInstance()->toString();?>'
		})
		<?php
		}	
		?>
		$modal.on('click', '.btn-send', function(e){
			e.preventDefault();
			var validate = validateForm();
			if(validate)
			{
				$('.btn-send').attr('disabled','disabled');
				$('.btn-send').before('<span class="wait">&nbsp;<img src="components/com_edocman/assets/images/loading.gif" alt="" /></span>');
				var documentId = $(this).attr('id');
				var data = {
					'task'	:	'document.store_download',
					'document_id' : documentId,
					'name': $('#edocman-name').val(),
					'email' : $('#email').val()
				};
				$.ajax({
					type: 'POST',
					url: 'index.php?option=com_edocman',
					data: data,
					dataType: 'html',
					success: function(htmltext) {
						$('.modal-body-download').hide();
						$('.wait').remove();
						$('#downloadsuccess').show('fast');
						$('.download-complete-message').html(htmltext);
					},
					error: function(jqXHR, textStatus, errorThrown) {
						alert(textStatus);
					}
				});
			}
		})
		function validateForm()
		{
			var emailReg            = /^([\w-\.]+@([\w-]+\.)+[\w-]{2,10})?$/;
			var names               = $('#edocman-name').val();
			var email               = $('#email').val();
			var inputVal            = new Array(names, email);

			var inputMessage = new Array("name", "email address");
			$('.error').hide();
			if(inputVal[0] == ""){
				$('#edocman-name').after('<span class="error"> Please enter your ' + inputMessage[0] + '</span>');
				return false;
			}
			if(inputVal[1] == ""){
				$('#email').after('<span class="error"> Please enter your ' + inputMessage[1] + '</span>');
				return false;
			}
			else if(!emailReg.test(email)){
				$('#email').after('<span class="error"> Please enter a valid email address</span>');
				return false;
			}
            <?php
            if($config->turn_on_privacy){
            ?>
			if(document.getElementById('privacy_agreement').checked === false){
                $('#privacy_agreement_span').after('<span class="error"> <?php echo JText::_('EDOCMAN_AGREE_PRIVACY_POLICY_ERROR');?></span>');
                return false;
            }
            <?php } ?>
			return true;
		}
	})
</script>
