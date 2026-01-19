var ppInstaller = {};

var previousView = 'start';

(function($){
	
	ppInstaller = {
	
	submitform : function() 
	{

		var $confirmToRestore = false;
		
		if($("input[name='restore']:checked").val()) {
			$confirmToRestore = 	confirm("Are you sure you want to restore");
			if(!$confirmToRestore) {
				$("input[name='restore']:checked").prop('checked', false);
				$("#ppInstaller_submit_button").html($(this).attr('title'));
				$("#ppInstaller_submit_button").removeClass('btn-danger');
				$('#ppInstaller_submit_button').button('complete');
				return false;
			}
			else {
				$("#goingToInstall").val($("input[name='restore']:checked").val());
			} 
		};
		 
		this.executeTask({});
	},
			
	needToSubmit : function()
	{
		if(-1 >= this.interval){
			return false;
		}
		this.interval = -1;
		this.form.submit();
	},
	
	warning : function()
	{
		alert('Something is going wrong, Please contact to PayPlans Support Team.');
	},
	
	autoRequest : function()
	{
	    ppInstaller.hideButton();
	    window.setInterval(function(){ ppInstaller.needToSubmit(); },500);
	    //if time limit goes more than 30 sec then alert will be appeared
	    window.setInterval( function(){ppInstaller.warning()},300000);
	},
	
	hideButton : function(){
	    document.getElementById("ppInstaller_submit_button").style.display = 'none'; 
	    document.getElementById("ppInstaller_spinner").style.display = 'block'; 
	},
	
	confirmRevert : function(){
	    var r=confirm("Are you sure to revert?");
	    if (r==true)
	      {
	        window.location = 'index.php?option=com_ppinstaller&task=revert&remove=true';
	      }
	    else
	      {
	        return false;
	    }
	},
	
	//success call back fuction for executing major task
	replaceNextTpl : function(data){
		//replace the current content with next
		$("#replacableTpl").html(data);
		
		ppInstaller.executeTask({});
		
		return true;
	},
	replaceTpl : function(data){
		//replace the current content with next
		$("#replacableTpl").html(data);
			
		return true;
	},
	
	//error call back fuction for executing major task
	errorInReplace : function(data){
		$("#replacableTpl").html(
				"<div class='alert alert-warning clearfix'>" +
				"<strong>Some Fatal Error Occured !</strong> Please try to run installer again. " +
				"<a class='btn btn-default pull-right' href='index.php?option=com_ppinstaller'>Run Again&nbsp;<i class='glyphicon  glyphicon glyphicon-repeat'></i></a></div>"+ 
				data);
		return false;
	},
	
	executeTask : function (postData) {

		if(postData == undefined) {
			var postData = {};
		}
		
		//send ajax request to replace the the template with error and succes function
		var sendFormDataTemp = $('#adminForm').serializeArray();
		
		$.each(sendFormDataTemp, function(index,data) {
			if (postData[data.name] == undefined) {
		    	postData[data.name] = data.value || '';
		    }
		});
		 
		ppInstaller.activateRound(postData.view);
		 
		var submitUrl = "index.php?option=com_ppinstaller";//+sendFormData;
		
		ppInstaller.ajax.go(submitUrl,postData,null,this.errorInReplace);
						
		//All major tasks are complete here
		return false;

	},
	
	credentialCheck : function () {
		
		var username = $('#ppinstallerUsername').val();
		var password = $('#ppinstallerPassword').val();
		
		if(username == ""){
			$('#ppinstallerUsername').tooltip('open');
			return false;
		}
		
		if(password == ""){
			$('#ppinstallerPassword').tooltip('open');
			return false;
		}

		$("#ppInstaller_submit_button").button('loading');
		var submitUrl = "index.php?option=com_ppinstaller&view=requirements&task=credential";
		
		ppInstaller.ajax.go( submitUrl, { ppinstallerUsername : username ,  ppinstallerPassword : password});
		
		return false;
	},
	
	activateRound : function(view) {
		
		if(previousView == view){
			return true;
		}
		
		$(".circle").removeClass("active-circle");
		$(".nav-text").removeClass("active-nav-text");
		
		$('.'+previousView).find(".circle").addClass("done-circle");
		$('.'+previousView).find(".nav-text").addClass("done-nav-text");
		
		$('.'+view).find(".circle").addClass("active-circle");
		$('.'+view).find(".nav-text").addClass("active-nav-text");
		
		previousView = view;
	},
	
	changeTask : function(previousTask, nextTask, postData, message) {
		$("#"+previousTask).removeClass("activate glyphicon-refresh");
		$("#"+nextTask).addClass("activate glyphicon-refresh").removeClass("glyphicon-question-sign");
		if(message != undefined) {
			$("#"+previousTask).next().next().html(message);
		}
		ppInstaller.executeTask(postData);
		
		return true;
	},
	
	stopTask : function(currentTask, message) {
		$("#"+currentTask).removeClass("activate glyphicon-refresh").addClass("glyphicon-remove").parent().addClass("text-danger");
		if(message == "") {
			message = "<strong>Error !</strong> while processing task : "+currentTask;
		}
		
		message = message +	"<button class='btn btn-danger pull-right' data-toggle='modal' data-target='#myModal'>" +
							"Ask for Help !" +
							"</button>";
		
		$(".ppinstaller-steps-error").removeClass('hide').html(message);
			
		return true;
	},
	/*--------------------------------------------------------------
	  AJAX related work
	--------------------------------------------------------------*/
	ajax : {	
					
					//XITODO : replace via jQuery code
					create : function(sParentId, sTag, sId){
						var objParent = this.$(sParentId);
						objElement = document.createElement(sTag);
						objElement.setAttribute('id',sId);
						if(objParent){
							objParent.appendChild(objElement);
						}
					},

					remove : function(sId){
						$(sId).remove();
					},

					default_error_callback : function (error){
						//XIFW_TODO : log to console
						alert("An error has occured\n"+error);
					},

					default_success_callback : function (result){

						//XITODO : log to console

						// we now have an array, that contains an array.
						for(var i=0; i<result.length;i++){

							var cmd 		= result[i][0];
							var id			= result[i][1];
							var property 	= result[i][2];
							var data 		= result[i][3];

							switch(cmd){
							case 'as': 	// assign or clear
								var objElement = $(id);
								if(objElement){
									if(property == 'innerHtml' || property == 'innerHTML'){
										$('#'+id).html(data);
									}else if(property == 'replaceWith'){
										$('#'+id).replaceWith(data);
									}else{
										eval("objElement."+property+"=  data \; ");
									}
								}

								break;

							case 'al':	// alert
								if(data){
									alert(data);}
								break;

							case 'ce':
								ppInstaller.ajax.create(id,property, data);
								break;

							case 'rm':
								ppInstaller.ajax.remove(id);
								break;

							case 'cs':	// call script
								var scr = id + '(';
								if($.isArray(data)){
									scr += '(data[0])';
									for (var l=1; l<data.length; l++) {
										scr += ',(data['+l+'])';
									}
								} else {
									scr += 'data';
								}
								scr += ');';
								eval(scr);
								break;

							default:
								alert("Unknow command: " + cmd);
							}
						}
					},

					error : function(Request, textStatus, errorThrown, errorCallback) {
						errorCallback(Request.responseText);	
					},
					
					success : function(msg, successCallback, errorCallback) {
						// Initialize
						var junk = null;
						var message = "";
						
						// Get rid of junk before the data
						var valid_pos = msg.indexOf('###');
						var valid_last_pos = msg.lastIndexOf('###');
						if( valid_pos == -1 ) {
							// Valid data not found in the response
							msg = 'Invalid AJAX data: ' + msg;
							errorCallback(msg);
							return;
						}
						
						// get message between ###<----->### second argument is length
						message = msg.substr(valid_pos+3, valid_last_pos-(valid_pos+3)); 
						
						try {
							var data = JSON.parse(message);
						}catch(err) {
							var msg = err.message + "\n<br/>\n<pre>\n" + message + "\n</pre>";
							errorCallback(msg);
							return;
						}
						
						// Call the callback function
						successCallback(data);
					},

					/*
					 * url : URL to call
					 * data : array / json / string / object
					 * */
					go : function (url, data, successCallback, errorCallback, timeout){
						
						if(data != null && data.iframe == true){
							var call = {data:data, url:url}; 
							return ppInstaller.iframe.show(call);	
						}
						// timeout 60 seconds
						if(timeout == null) timeout = 600000;
						if(errorCallback == null) errorCallback = ppInstaller.ajax.default_error_callback;
						if(successCallback == null) successCallback = ppInstaller.ajax.default_success_callback;

						// properly oute the url
						ajax_url = ppInstaller.route.url(url) + '&format=ajax';
					
						//execute ajax
						// in jQ1.5+ first argument is url
						$.ajax(ajax_url, {
							type	: "POST",
							cache	: false,
							data	: data,
							timeout	: timeout,
							success	: function(msg){ ppInstaller.ajax.success(msg,successCallback,errorCallback); },
							error	: function(Request, textStatus, errorThrown){ppInstaller.ajax.error(Request, textStatus, errorThrown, errorCallback);}
						});
					}
				
	},

	/*--------------------------------------------------------------
	  URL related work
	--------------------------------------------------------------*/
	route : {
		url : function(url){
				// already a complete URL
				if ( url.indexOf('http://') === -1 || url.indexOf('https://') === -1 ) {
					//it is not absolute URL (without http)
					//if site is not being used in subdirectory then ppInstaller_vars['url']['base_without_scheme'] will be empty
					//and base url need to be added in the requested URL
				
					var base2_url_index = url.indexOf(ppInstaller_vars['url']['base_without_scheme']); //indexOf give 0 when string is empty and -1 gives when it is null
				
					if(ppInstaller_vars['url']['base_without_scheme'] == "" ) {
						base2_url_index = -1;
					}

					// only add if, its not routed URL
					if(base2_url_index === -1 ) {
						url = ppInstaller_vars['url']['base'] + url;
					}
				}

				return url;
		}
	},
	
	/*--------------------------------------------------------------
	  Iframe related work
	--------------------------------------------------------------*/
	iframe : {

		show:function (call, appendTo,onLoadCallback){
			
			if(onLoadCallback == null) onLoadCallback = this.process;
			if(appendTo == null) appendTo = '#ppInstallerWindowBody';
			
			if(typeof call.data.classes === "undefined"){ 
				call.data.classes = '';
			} 
			
			if(typeof call.data.id === "undefined"){ 
				call.data.id = '';
			}
			
			$iframe = $('<iframe id="'+call.data.id+'" class="span12 '+call.data.classes+'" frameborder="0" scrolling="auto" height="90%">');
			$iframe.load(onLoadCallback).appendTo(appendTo);

			// properly output the url
			url = ppInstaller.route.url(call.url);
			
			//url += '&' + $.param(call.data);
			$iframe.attr('src',url);
			return $iframe;
		},
		
		process : function(){
		}
	},

	precheckAgree : function(object){
		
		if (object.checked) {
			$('#ppInstaller_submit_button').removeAttr('disabled');
		}
		else {
			$('#ppInstaller_submit_button').attr('disabled', 'true');
		}
	},
	
	upgrade : {
			request		: 	function(){
							var	args = {'event_args': {}};
							ppInstaller.ajax.go('index.php?option=com_ppinstaller&task=installerUpgrade', args);
							
							$('#ppinstaller-upgrade-error').hide();													
							$('#ppinstaller-upgrade-loading').show();
							$('#ppinstaller-upgrade-init').hide();
							
							return false;
			},
			
			response 	: function(response){
							response = JSON.parse(response);						
							
							if(response.response_code == 200){
								//tracking
								ppInstaller.tracking('upgrade_success', null);
								
								$('#ppinstaller-upgrade-loading').hide();
								$('#ppinstaller-upgrade-success').show();
								setTimeout(function(){
									$('#ppinstaller-upgrade-modal').modal('hide'); 
									location.reload();}, 3000);						
							}
							else {
								//tracking
								ppInstaller.tracking('upgrade_failed', null);
								$('#ppinstaller-upgrade-loading').hide();
								$('#ppinstaller-upgrade-error').html("There is some problem in upgrading component. <br/>The error code is "+ response.error_code+".<br/> Please report this issue to service provider.");
								$('#ppinstaller-upgrade-error').show();
							}						
							return true;
			}
	},
	
	tracking : function(event, event_args){
		return true;
	}
	
	};
	
})(jQuery);
