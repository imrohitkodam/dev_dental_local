EasySocial.module('site/toolbar/profile', function($) {

	var module = this;

	EasySocial.require()
	.library('popbox')
	.done(function($){

		EasySocial.Controller('Toolbar.Profile', {
				defaultOptions: {
					"{dropdown}": "[data-toolbar-profile-dropdown]",
					"{dropdownToggle}": "[data-toolbar-toggle]"
				}
			}, function(self){ return{

				init: function() {

				},

				"{self} popboxActivate" : function(el, event, popbox) {
					$(popbox.tooltip)
						.implement(EasySocial.Controller.Toolbar.Profile.Logout);
				},

				"{dropdownToggle} click" : function() {
					$('.popbox-notifications').hide();
				}
			}}
		);

		EasySocial.Controller('Toolbar.Profile.Logout',{
				defaultOptions: {
					// Elements within this container.
					"{logoutForm}": "[data-toolbar-logout-form]",
					"{logoutButton}": "[data-toolbar-logout-button]"
				}
			}, function(self) { return{
					
					logout: function() {
						self.logoutForm().submit();
					},

					"{logoutButton} click" : function() {
						console.log('debug');

						self.logout();
					}
				}
			});

		module.resolve();
	});

});
