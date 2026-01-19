EasySocial.module('fields/user/easyblog_biography/content', function($) {
	var module = this;

	EasySocial.Controller(
		'Field.Easyblog_Biography', {
			defaultOptions: {
				required: false,
        		"editor": null,
				"{input}": "[data-field-easyblog-bio]"
			}
		},
		function(self, opts, base) {

			return {
				init : function() {
        			self.editor = self.options.editor;

					opts.error = {
						required: base.find('[data-error-required]').data('error-required')
					};
				},

			    "{self} onRender": function() {
			        var data = self.input().htmlData();
			        opts.error = data.error || {};
			    },

			    raiseError: function(msg) {
			        self.trigger('error', [msg]);
			    },

			    clearError: function() {
			        self.trigger('clear');
			    },

				"{input} change": function(el, event) {

					if (!self.validateInput()) {
						self.element.addClass('error');
					} else {
						self.element.removeClass('error');
					}
				},

				validateInput: function() {

					self.clearError();

					if (!self.options.required) {
						return true;
					}

			        var value = self.editor.getContent();

			        if ($.isEmpty(value)) {
						self.raiseError(opts.error.required);
			            return false;
			        }

					return true;
				},

				"{self} onSubmit": function(el, event, register) {

					if (!self.options.required) {
						register.push(true);
						return;
					}

					register.push(self.validateInput());
					return;
				}
			}
		});

	module.resolve();
});
