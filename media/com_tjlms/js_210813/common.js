/*
 * @version    SVN:<SVN_ID>
 * @package    com_tjlms
 * @author     Techjoomla <extensions@techjoomla.com>
 * @copyright  Copyright (c) 2009-2015 TechJoomla. All rights reserved
 * @license    GNU General Public License version 2, or later
 */

var tjLmsCommon = (function(){
	return {
		root_url	: (typeof root_url == 'undefined') ? '' : root_url,
		loadPopup: function(link) {
			var width = jQuery(window).width();
			var height = jQuery(window).height();

			var wwidth = width-(width*0.10);
			var hheight = height-(height*0.10);
			SqueezeBox.open(link, { handler: 'iframe', size: {x: wwidth, y: hheight},classWindow: 'tjlms-modal'});
		},
		closePopup: function(reloadWindow){
			if (reloadWindow && reloadWindow == '1')
			{
				window.parent.location.reload();
			}
			else
			{
				parent.SqueezeBox.close();
			}
		},
		file: {
			allowedSize: '2',
			showstatusbar: false,
			showprogressbar: false,
			onAfterUploadFunctions: ['onAfterUpload', 'addTableEntries'],
			afterProcessDone: 'tjlms.test.showFileinUploadlist',
			formData: null,
			fc: 0,
			$file: null,
			validate: function(file) {
				var uploadedfile	=	jQuery(file)[0].files[0];
				var sizeStr="";
				var result=[];
				result['error'] = '0';

				if (uploadedfile === undefined)
				{
					jQuery('.statusbar').addClass('d-none');
				}
				else
				{
					var sizeKB = uploadedfile.size/1024;

					if(parseInt(sizeKB) > 1024){
						var sizeMB = sizeKB/1024;
						sizeStr = sizeMB.toFixed(2) + " MB";
					}
					else{
						sizeStr = sizeKB.toFixed(2) + " KB";
					}

					/* if file size is greater than allowed*/
					if((this.allowedSize * 1024 * 1024) < uploadedfile.size)
					{
						result['error'] = '1';
						result['msg'] = Joomla.JText._('COM_TJLMS_ALLOWED_FILE_SIZE_ERROR_MSG').replace("%s", this.allowedSize).replace("%s", sizeStr);
					}

					let allowedExtensions = jQuery(file).attr('accept');
					if (allowedExtensions)
					{
						var valid_extensions = allowedExtensions.split(',');
						var ext = uploadedfile.name.split('.').pop().toLowerCase();

						/* If extension is not in provided valid extensions*/
						if(jQuery.inArray(ext, valid_extensions) == -1)
						{
							result['msg'] = Joomla.JText._('COM_TJLMS_ALLOWED_FILE_EXTENSION_ERROR_MSG').replace("%s", allowedExtensions).replace("%s", ext);
							result['error'] = '1';
						}
					}
				}

				return result;
			},
			upload: function(){
			var file = this.$file;
			var formData = this.formData;
			var fileObj = this;
			this.fc=0;
			jQuery(file).closest('.fileupload').siblings(".statusbar").remove();
			var status = new this.statusAndProgress(file);

			var isValid = this.validate(file);
			if (isValid.error == "1") {
				status.setMsg(isValid);
				return false;
			}

			var totalProcess = this.onAfterUploadFunctions.length + 1;
			var percentForupload = 100 / totalProcess;

			var returnvar	= true;
			var jqXHR = jQuery.ajax({
				 xhr: function() {
					var xhrobj = jQuery.ajaxSettings.xhr();
					status.setAbort(xhrobj);
					if (xhrobj.upload) {
						xhrobj.upload.addEventListener('progress', function(event) {
							var percent = 0;
							var position = event.loaded || event.position;
							var total = event.total;
							if (event.lengthComputable) {
								percent = Math.ceil(position / total * percentForupload);
							}

							status.setProgress(percent);
						}, false);
					}
					return xhrobj;
				},
				url: root_url + 'index.php?option=com_tjlms&task=fileupload.validateAndUpload&tmpl=component&format=json',
				type: 'POST',
				data:  formData,
				mimeType:"multipart/form-data",
				contentType: false,
				dataType:'json',
				cache: false,
				processData:false,
				success: function(response)
				{
					if (!response.success && response.message)
					{
						var result=[];
						result['error'] = '1';
						result['msg'] = response.message;
						status.setMsg(result);
						returnvar = false;
					}
					else
					{
						doProcess = true;
						status.setProgress(percentForupload);
						fileObj.onAfterUploadProcess(status, response.data);
					}
				},
				error: function(jqXHR, textStatus, errorThrown)
				{
					var result = [];
					result['error'] = '1';
					result['msg'] = jqXHR.responseText;
					status.setMsg(result);
					returnvar	= false;
				}
			});

			return returnvar;
		},
		onAfterUploadProcess: function(status, formData) {
			var totalProcess = this.onAfterUploadFunctions.length + 1;
			var percentForeachProcess = 100 / totalProcess;

			var functiontocall = this.onAfterUploadFunctions[this.fc];
			var fclength = this.onAfterUploadFunctions.length;
			var fileObj = this;
			if (this.fc == fclength)
			{
				let fileName = formData.org_filename;
				let result = [];
				result['msg'] = Joomla.JText._('COM_TJLMS_SUCCESS_UPLOAD').replace("%s", fileName);
				status.setMsg(result);

				if (typeof this.afterProcessDone === 'function')
				{
					this.afterProcessDone(formData);
					return;
				}
				else
				{
					var func = window;
					var funcSplit = this.afterProcessDone.split('.');
					for (i = 0;i < funcSplit.length;i++){
						func = func[funcSplit[i]];
					}
					extravalid = func(formData);
					return;
				}
			}

			var returnvar	= true;

			var saveurl= root_url + 'index.php?option=com_tjlms&task=fileupload.' + functiontocall + '&tmpl=component&format=json';
			var promise = tjService.postData(saveurl,formData);

			promise.fail(
				function(response) {
					var result = [];
					result['error'] = '1';
					result['msg'] = response.responseText;
					status.setMsg(result);
					returnvar	= false;
				}
			).done(function(response) {

				if (!response.success && response.message){
					var result = [];
					result['error'] = '1';
					result['msg'] = response.message;
					status.setMsg(result);
					returnvar	= false;
				}
				else {
					fileObj.fc++;
					let thisProcessno = fileObj.fc + 1;
					status.setProgress(percentForeachProcess * thisProcessno);
					fileObj.onAfterUploadProcess(status, response.data);
				}
			});

			return returnvar;
		},
		statusAndProgress : function (fileelement) {

			let me = this;

			this.statusbar = jQuery("<div class='statusbar no-gutters'></div>");
			this.alert = jQuery('<div class="msg alert"></div>').appendTo(this.statusbar);
			this.progressBar = jQuery('<div class="progress"><div class="progress-bar progress-bar-uploading"><span class="progress_bar_text">Uploading <span class="progress_per"></span></div></div>').appendTo(this.statusbar);

			this.abort = jQuery("<span class='abort mr-5'>Abort</span>").insertAfter(this.progressBar.find(".progress_bar_text"));

			var object = this;
			this.statusbar.insertAfter(fileelement.closest('.fileupload'));
			this.setMsg = function(result)
			{
				this.statusbar.show();
				if(result['error']){
					this.alert.addClass("alert alert-warning");
				}
				else{
					this.alert.addClass("alert alert-success");
				}
				this.alert.html(result['msg']);
				this.alert.show();

				setTimeout(function(){
				  me.alert.hide();
				  me.progressBar.hide();
				}, 5000);
			}
			this.setProgress = function(progress)
			{
				this.alert.hide();
				this.statusbar.show();
				this.progressBar.show();
				progress = parseInt(progress)
				var progressBarWidth = progress*this.progressBar.width()/ 100;
				this.progressBar.find('.progress-bar').animate({ width: progressBarWidth }, 10);
				this.progressBar.find('.progress_per').html(progress + "% ");
				if(parseInt(progress) >= 100)
				{
					this.abort.hide();
					this.progressBar.hide();
				}
			}
			this.setAbort = function(jqxhr)
			{
				this.abort.show();
				var sb = this.statusbar;
				this.abort.click(function()
				{
					sb.closest('.controls').find('.fileupload-preview').val('');
					sb.closest('.controls').find('input:file').val("");

					var qid = jQuery(this).closest('[data-js-id="test-question"]').attr('data-js-itemid');
					jQuery('#lesson_files_' + qid).val('');

					jqxhr.abort();
					sb.hide();
				});
			}
		}
	},
	initialize: function(){
		var that = this;
			jQuery(document).ready(function(){
				jQuery(document).on("click", "[data-js-role='tjmodal']" , function() {
					that.loadPopup(jQuery(this).attr('data-js-link'));
			});
		});

		return this;
	}
}
})().initialize();
