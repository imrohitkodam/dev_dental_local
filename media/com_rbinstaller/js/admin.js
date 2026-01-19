if (typeof(rbinstaller)=='undefined'){
	var rbinstaller = {};
}

(function($){
// START : 	
// Scoping code for easy and non-conflicting access to $.
// Should be first line, write code below this line.	

rbinstaller.cart = {
		refresh_counter : function(items){							
							var length = Object.keys(items).length;
							if( length > 0) {
								var html = '<a href="#" data-toggle="modal" data-target="#rbinstaller-cart">'+
											'<i class="fa fa-shopping-cart fa-2x"></i>'+
											' My Cart'+
											'<sup class="badge badge-warning">'+
												length +
											'</sup>'+
											'</a>';
								$('#rbinstaller-cart-icon').html(html);
								return true;
							}
	
							var html = '<span class="muted">'+
										'<i class="fa fa-shopping-cart fa-2x"></i>'+
										' My Cart'+
										'<sup class="badge">0</sup>'+
									'</a>';
							$('#rbinstaller-cart-icon').html(html);
							return true;		
		},
		
		add_item	:	function(item_id){	
							var args = {'event_args' : {'item_id' : item_id}};
							rb.ajax.go('index.php?option=com_rbinstaller&view=item&task=addToCart', args);
							return false;
		},
		
		add_success : function(items){
						rbinstaller.cart.refresh_counter(items);
						var scope = angular.element(document.getElementById('rbinstaller-list-ctrl')).scope();
						return scope.cart.add_success(items);
		},
		
		remove_item	:	function(item_id){			
							var args = {'event_args' : {'item_id' : item_id}};
							rb.ajax.go('index.php?option=com_rbinstaller&view=item&task=removeFromCart', args);
		},
		
		remove_success : function(items){
							rbinstaller.cart.refresh_counter(items);
							var scope = angular.element(document.getElementById('rbinstaller-list-ctrl')).scope();
							return scope.cart.remove_success(items);
		},
		
		checkout	: 	function(added_items){
							$('#rbinstaller-cart-body').html('<div class="rb-loading"></div>');
							$('#rbinstaller-cart').find('.modal-footer').html('');
							
							var args = {'event_args' : { 'items' : added_items, 'return_url' : encodeURIComponent(location.href) }};
							
							//tracking checkout
							var added_items_title = new Array();
							for (var index in added_items) {
								added_items_title.push(rbinstaller_items[added_items[index]]['title']);
							}
														
							var event_args = { 'items'  : added_items_title.join(" ,") };
							rbinstaller.tracking('checkout', event_args);
							
							rb.ajax.go('index.php?option=com_rbinstaller&view=item&task=checkout&product_tag='+rbinstaller_product_tag_alias, args);	
		},

	    redirect_to_pay : function(url){						
						window.location.href = url;
		}
};

rbinstaller.item = {
		item_id 	: 0,
		
		install		: 	function(item_id, version_id, product_tag){
						rbinstaller.item.item_id = item_id; 
						var	args = {'event_args': {'item_id': item_id, 'version_id' : version_id, 'product_tag' : product_tag}};
						rb.ajax.go('index.php?option=com_rbinstaller&view=item&task=install&product_tag='+rbinstaller_product_tag_alias, args);
		},
		
		install_response : function(response){
						response = JSON.parse(response);
						
						var scope = angular.element(document.getElementById('rbinstaller-list-ctrl')).scope();
						
						if(response.response_code == 200){
							rbinstaller.item.track_installation(rbinstaller_items[rbinstaller.item.item_id].alias, 1);
							return scope.install.success();
						}
						else {
							rbinstaller.item.track_installation(rbinstaller_items[rbinstaller.item.item_id].alias, 0);
							return scope.install.error(response);
						}				
		},
		
		track_installation : function(label, value){				      
			                var base = "http://pub.readybytes.net/broadcast/rbinstaller/action.html";
			                var url = base+"?domain="+rb_vars.url.root+"&label="+label+"&value="+value+"&action=install";
			                $('#installation-traker-iframe').attr('src',url);

					        return true;	
		}
};

rbinstaller.credential = {
		show_popup : function(){
					$("#rbinstaller-credential-form").find("input").jqBootstrapValidation();
					$(document).on('click', '#rbinstaller-credential-verify', function(){return rbinstaller.credential.verify();});
					$('#rbinstaller-login-modal').modal({ keyboard: false, backdrop: 'static' });
					$('#rbinstaller-login-modal').modal('show');
		},
		
		verify : function(){
					if($("#rbinstaller-credential-form").find("input").jqBootstrapValidation("hasErrors")){
						$('#rbinstaller-credential-error').html('<div class="alert alert-error">Please fill the correct details.</div>');
						return false;
					}
					
					var url = "index.php?option=com_rbinstaller&view=item&task=credential&action=verify&product_tag="+rbinstaller_product_tag_alias;				
					var email 	 = $('#rbinstaller-credential-email').val();
					var password = $('#rbinstaller-credential-password').val();
					
					$('#rbinstaller-credential-error').html(' ');
					$('#rbinstaller-login-modal .modal-body').html('<div class="rb-loading"></div>');
					$('#rbinstaller-login-modal .modal-footer').html('');					
					
					var arg = {'event_args' :{'email' : email, 'password' : encodeURIComponent(password)} };
					rb.ajax.go(url, arg);
					return false;
		}
};

rbinstaller.account = {
		show_popup : function(){
					$("#rbinstaller-credential-form").find("input").jqBootstrapValidation();
					$(document).on('click', '#rbinstaller-credential-verify', function(){return rbinstaller.account.verify()});					
					$('#rbinstaller-login-modal').modal('show');
		},
		
		verify : function(){
					if($("#rbinstaller-credential-form").find("input").jqBootstrapValidation("hasErrors")){
						$('#rbinstaller-credential-error').html('<div class="alert alert-error">Please fill the correct details.</div>');
						return false;
					}
					
					var url = "index.php?option=com_rbinstaller&view=item&task=update_account&action=verify&product_tag="+rbinstaller_product_tag_alias;				
					var email 	 = $('#rbinstaller-credential-email').val();
					var password = $('#rbinstaller-credential-password').val();
					
					$('#rbinstaller-credential-error').html(' ');
					$('#rbinstaller-login-modal .modal-body').html('<div class="rb-loading"></div>');
					$('#rbinstaller-login-modal .modal-footer').html('');					
					
					var arg = {'event_args' :{'email' : email, 'password' : encodeURIComponent(password)} };
					rb.ajax.go(url, arg);
					return false;
		}
};

rbinstaller.registration = {
		form : function(){
					var url = "index.php?option=com_rbinstaller&view=item&task=registration&action=form&product_tag="+rbinstaller_product_tag_alias;				
					rb.ajax.go(url);
					return false;
		},
		
		register : function(){
			if($("#rbinstaller-registration-form").find("input").jqBootstrapValidation("hasErrors")){
				$('#rbinstaller-registration-error').html('<div class="alert alert-error">Please fill the correct details.</div>');
				return false;
			}		
			
			var email 	 	= $('#rbinstaller-registration-email').val();
			var password 	= $('#rbinstaller-registration-password').val();
			var arg 		= {'event_args' :{'email' : email, 'password' : encodeURIComponent(password)} };			
			var url 		= "index.php?option=com_rbinstaller&view=item&task=registration&action=register&product_tag="+rbinstaller_product_tag_alias;
			

			$('#rbinstaller-registration-error').html(' ');
			$('#rbinstaller-login-modal .modal-body').html('<div class="rb-loading"></div>');
			$('#rbinstaller-login-modal .modal-footer').html('');
			
			rb.ajax.go(url, arg);
			return false;
		}
};


rbinstaller.filters = {
		toggle : function(element){
					if($('#rbinstaller-filter-bar-container').is(':visible')){
						$('#rbinstaller-filter-bar-container').slideUp();
						$(element).html('Show Filters <i class="fa fa-angle-double-down"></i>');
						return false;
					}
					
					$('#rbinstaller-filter-bar-container').slideDown();
					$(element).html('Hide Filters <i class="fa fa-angle-double-up"></i>');
					return false;
		}
};

rbinstaller.upgrade = {
		request		: 	function(){
						var	args = {'event_args': {}};
						rb.ajax.go('index.php?option=com_rbinstaller&view=item&task=component_upgrade', args);
												
						$('#rbinstaller-upgrade-modal .modal-header h1').html("Upgrading");
						$('#rbinstaller-upgrade-modal .modal-body').html('<div class="rb-loading"></div>');
						
						return false;
		},
		
		response 	: function(response){
						response = JSON.parse(response);						
						
						if(response.response_code == 200){
							//tracking
							rbinstaller.tracking('upgrade_success', null);
							$('#rbinstaller-upgrade-modal .modal-header h1').html("Success");
							$('#rbinstaller-upgrade-modal .modal-body').html("Component has been upgraded successfully...!!!");
							setTimeout(function(){
								$('#rbinstaller-upgrade-modal').modal('hide'); 
								location.reload();}, 3000);						
						}
						else {
							//tracking
							rbinstaller.tracking('upgrade_failed', null);
							$('#rbinstaller-upgrade-modal .modal-header h1').html("Error");
							$('#rbinstaller-upgrade-modal .modal-body').html("There is some problem in upgrading component. The error code is "+ response.error_code+".\n Please report this issue to service provider.");							
						}						
						return true;
		}
};

rbinstaller.tracking = function(event, event_args){
	
						event 	= 'appstore.'+event;
						//TODO : maintain whitelist for events which needs to be tracked
						//		 so that in future some other events can be discarded easily
						var whitelist_events = ["appstore.viewed", "appstore.addtocart", "appstore.checkout", "appstore.install_success", "appstore.install_failed", 
						                        	"appstore.register_success", "appstore.register_failed", "appstore.message", "appstore.myapps", "appstore.document", 
						                        	"appstore.support", "appstore.verification_success", "appstore.verification_failed", "appstore.search", 
						                        	"appstore.upgrade_success", "appstore.upgrade_failed"
						                        	];
						
						if(whitelist_events.indexOf(event) == -1){
							return false;
						}
						
						if(typeof(event_args) == 'undefined' || event_args === null) event_args = {};
						
						event_args.email		= (typeof(event_args.email)  != 'undefined') ? event_args.email : rbinstaller_config["email"];
						event_args.domain 		= rbinstaller_config["current_domain"]; 
						event_args.app_store 	= rbinstaller_product_tag_alias.replace("rbapps", "");
						event_args.rbinstaller  = rbinstaller_version;
						
						$.post(rbinstaller_config["tracking_url"],
					 				{ 'event' : event, 'event_args': event_args}
					 	   );
};

$(document).ready(function(){
	   $(window).scroll(function(){	           
	    	   var header_height = $("#rbinstaller-header").height();
               var scroll_top = $(window).scrollTop();
               // after scroll
               if(scroll_top >= header_height && $('#rbinstaller-search-bar').hasClass('navbar navbar-fixed-top')==false){	                     
            	   $('#rbinstaller-search-bar').addClass('navbar navbar-fixed-top');
               }
               // reverse scroll, again at top
               else if(scroll_top < header_height && $('#rbinstaller-search-bar').hasClass('navbar navbar-fixed-top')){                    
            	   $('#rbinstaller-search-bar').removeClass('navbar navbar-fixed-top')	                       
               }
	   });	
});

//ENDING :
//Scoping code for easy and non-conflicting access to $.
//Should be last line, write code above this line.
})(rb.jQuery);
