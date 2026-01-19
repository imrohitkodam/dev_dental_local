FD50.plugin("component", function($) {

var csrfFetch = false;

var Component = $.Component = function(name, options) {

	if (arguments.length < 1) {
		return Component.registry;
	}

	if (arguments.length < 2) {
		return Component.registry[name];
	}

	return Component.register(name, options);
}

Component.registry = {};

Component.proxy = function(component, property, value) {
	component[property] = value;

	// If it's a method
	if ($.isFunction(value)) {
		// Change the "this" context to the component itself
		component[property] = $.proxy(value, component);
	}
}

Component.register = function(name, options) {

	// If an abstract component was passed in
	var abstractComponent;

	// Normalize arguments
	if ($.isFunction(name)) {
		abstractComponent = name;
		name = abstractComponent.className;
		options = abstractComponent.options;
	}

	var self =

		// Put it in component registry
		Component.registry[name] =

		// Set it to the global namespace
		window[name] =

		// When called as a function, it will return the correct jQuery object.
		function(command) {
			return ($.isFunction(command)) ? command($) : component;
		};

	// Extend component with properties in component prototype
	$.each(Component.prototype, function(property, value) {
		Component.proxy(self, property, value);
	});

	self.$ = $;
	self.options = options;
	self.className = name;
	self.identifier = 'easyblog';
	self.componentName = "com_easyblog";
	self.prefix = self.identifier + "/";
	self.version = options.version;
	self.safeVersion = self.version.replace(/\./g,"");
	self.scriptVersion = options.scriptVersion;
	self.environment = options.environment  || $.environment;
	self.mode = options.mode || $.mode;
	self.debug = (self.environment==='development');
	self.console = Component.console(self);
	self.language = options.language || $.locale.lang || "en";
	self.ajaxUrl = options.ajaxUrl      || $.basePath + "/?option=" + self.componentName;
	self.scriptPath = options.scriptPath   || $.rootPath + "/media/" + self.componentName + "/scripts/";

	// Legacy and needs to be removed
	self.viewPath = options.viewPath || self.ajaxUrl + '&tmpl=component&no_html=1&controller=themes&task=getAjaxTemplate';
	self.scriptVersioning = options.scriptVersioning || false;
	self.tasks = [];

	// Register component to bootleader
	FD50.component(name, self);

	// If there's no abstract componet prior to this, we're done!
	if (!abstractComponent) {
		return;
	}

	// If we're on development mode
	if (self.debug) {

		// Execute queue in abstract component straightaway
		abstractComponent.queue.execute();

	// If we're on static or optimized mode
	} else {

		// Get component installers from bootloader and install them
		var installer, installers = FD50.installer(name);
		while(installer = installers.shift()) {
			self.install.apply(self, installer);
		}

		// Wait until definitions, scripts & resources are installed
		$.when(
			self.install("definitions"),
			self.install("scripts")
		).done(function(){

			// Then only execute queue in abstract component.
			abstractComponent.queue.execute();
		});
	}
}

Component.extend = function(property, value) {

	// For later components
	Component.prototype[property] = value;

	// For existing components
	$.each(Component.registry, function(name, component) {
		Component.proxy(component, property, value);
	});
}

$.template("component/console",'<div id="[%== component.identifier %]-console" class="foundry-console" style="display: none; z-index: 999999;"><div class="console-header"><div class="console-title">[%= component.className %] [%= component.version %]</div><div class="console-remove-button">x</div></div><div class="console-log-item-group"></div><style type="text/css">.foundry-console{position:fixed;width:50%;height:50%;bottom:0;left:0;background:white;box-shadow: 0 0 5px 0;margin-left: 25px;}.console-log-item-group{width: 100%;height: 100%;overflow-y:scroll;}.console-header{position: absolute;background:red;color:white;font-weight:bold;top:-24px;left: 0;line-height:24px;width:100%}.console-remove-button{text-align:center;cursor: pointer;display:block;width: 24px;float:right}.console-remove-button:hover{color: yellow}.console-title{padding: 0 5px;float:left}.console-log-item{padding: 5px}.console-log-item + .console-log-item{border-top: 1px solid #ccc}</style></div>');

Component.console = function(component) {

	return (function(self){

		var instance = function(method) {

				if (arguments.length < 1) {
					return instance.toggle();
				}

				return instance[method] && instance[method].apply(instance, arguments);
			},

			element;

			instance.selector = "#" + self.identifier + "-console";

			instance.init = function() {

				element = $(instance.selector);

				if (element.length < 1) {

					element = $($.View("component/console", {component: self})).appendTo("body");

					element.find(".console-remove-button").click(function(){
						element.hide();
					});
				}

				instance.element = element;

				return arguments.callee;
			};

			instance.methods = {

				log: function(message, type, code) {

					type = type || "info";

					var itemGroup = element.find(".console-log-item-group"),
						item =
							$(document.createElement("div"))
								.addClass("console-log-item type-" + type)
								.attr("data-code", code)
								.html(message);

					itemGroup.append(item);
					itemGroup[0].scrollTop = itemGroup[0].scrollHeight;

					// Automatically show window on each log
					if (self.debug) { element.show(); }
				},

				toggle: function() {
					element.toggle();
				},

				reset: function() {
					element.find(".console-log-item-group").empty();
				}
			};

		$.each(instance.methods, function(method, fn) {
			instance[method] = function() {
				instance.init(); // Always call init in case of destruction of element
				return fn.apply(instance, arguments);
			}
		});

		return instance;

	})(component);
}

var doc = $(document);
var proto = Component.prototype;

$.extend(proto, {

	run: function(command) {
		return ($.isFunction(command)) ? command($) : this;
	},

	ready: (function(){

		// Replace itself once document is ready
		doc.ready(function(){
			proto.ready = proto.run;
		});

		return function(callback) {

			if (!$.isFunction(callback)) return;

			// When document is ready
			doc.ready(function() {
				callback($);
			});
		}
	})(),

	install: function(name, factory) {

		var self = this,
			task = self.tasks[name] || (self.tasks[name] = $.Deferred());

		// Getter
		if (!factory) return task;

		// Setter
		var install = function(){
			factory($, self);
			return task.resolve();
		}

		// If this is installer contains component definitions,
		// install straightaway.
		if (name=="definitions") return install();

		// Else for component definitiosn to install first,
		// then only install this installer.
		$.when(self.install("definitions")).done(install);
	},

	token: function() {

		let token = window.ezb.csrfToken ? window.ezb.csrfToken : Joomla.getOptions('csrf.token', window.ezb.token);
		return token;
	},

	template: function(name) {

		var self = this;

		// Get all component templates
		if (name==undefined) {

			return $.grep($.template(), function(template) {

				return template.indexOf(self.prefix)==0;
			});
		}

		// Prepend component prefix
		arguments[0] = self.prefix + name;

		// Getter or setter
		return $.template.apply(null, arguments);
	},

	// Component require extends $.require with the following additional methods:
	// - resource()
	// - view()
	// - language()
	//
	// It also changes the behaviour of existing methods to load in component-specific behaviour.
	require: function(options) {

		var self = this;
		var options = options || {};
		var require = $.require(options);
		var _require = {};

		// Keep a copy of the original method so the duck punchers below can use it.
		$.each(["library", "script", "language", "template", "done"], function(i, method){
			_require[method] = require[method];
		});

		require.library = function() {

			_require.script.apply(this, arguments);

			return require;
		};

		require.script = function() {
			var batch = this,

				request = batch.expand(arguments, {path: self.scriptPath}),

				names = $.map(request.names, function(name) {

					// Ignore module definitions, urls and relative paths.
					if ($.isArray(name) || $.isUrl(name) || /^(\/|\.)/.test(name)) {
						return name;
					}

					var extension = (request.options.extension || self.options.environment == 'production' ? 'min.js' : 'js');
					var versioning = ((self.scriptVersioning) ? "?" + self.scriptVersion + '=1' : "");
					var moduleName = self.prefix + name;
					var moduleUrl = $.uri(request.options.path)
										.toPath('./' + name + '.' + extension + versioning)
										.toString();

					return [[moduleName, moduleUrl, true]];
				});

			_require.script.apply(require, [request.options].concat(names));

			return require;
		};

		// Override path
		require.template = function() {

			var batch = this;
			var request = batch.expand(arguments, {path: self.scriptPath});

			_require.template.apply(require, [request.options].concat(

				$.map(request.names, function(name) {
					return [[self.prefix + name, name]];
				})
			));

			return require;
		};

		// Only execute require done callback when component is ready
		require.done = function(callback) {
			return _require.done.call(require, function(){

				// We need to get the latest csrf token after the page load
				// in order to prevent possible 'invalid token' issue when
				// Joomla cache is enabled. #3098
				if (window.ezb.csrfToken === undefined) {
					if (csrfFetch === false) {
						csrfFetch = EasyBlog.ajax('site/controllers/base/csrf')
						.done(function(token) {
							window.ezb.csrfToken = token;

							self.ready(callback);
						});
					} else {
						csrfFetch.done(function() {
							self.ready(callback);
						})
					}
				} else {
					self.ready(callback);
				}
			});
		};

		return require;
	},

	module: function(name, factory) {

		var self = this;

		// TODO: Support for multiple module factory assignment
		if ($.isArray(name)) {
			return;
		}

		var fullname = self.prefix + name;

		return (factory) ?

			// Set module
			$.module.apply(null, [fullname, function(){

				var module = this;

				factory.call(module, $);
			}])

			:

			// Get module
			$.module(fullname);
	}
});

$.Component.extend("ajax", function(namespace, params, callback) {

	var self = this;
	var date = new Date();

	var options = {
			url: self.ajaxUrl + "&_ts=" + date.getTime(),
			data: $.extend(params, {
					option: self.componentName,
					namespace: namespace
			})
		};

	options = $.extend(true, options, self.options.ajax);
	options.data[self.token()] = 1;

	// This is for server-side function arguments
	if (options.data.hasOwnProperty('args')) {
		options.data.args = $.toJSON(options.data.args);
	}

	if ($.isPlainObject(callback)) {

		if (callback.type) {

			switch (callback.type) {

				case 'jsonp':

					callback.dataType = 'jsonp';

					// This ensure jQuery doesn't use XHR should it detect the ajax url is a local domain.
					callback.crossDomain = true;

					options.data.transport = 'jsonp';
					break;

				case 'iframe':

					// For use with iframe-transport
					callback.iframe = true;

					callback.processData = false;

					callback.files = options.data.files;

					delete options.data.files;

					options.data.transport = 'iframe';
					break;
			}

			delete callback.type;
		}

		$.extend(options, callback);
	}

	if ($.isFunction(callback)) {
		options.success = callback;
	}

	var ajax = $.server(options);

	ajax.progress(function(message, type, code) {
		if (self.debug && type=="debug") {
			self.console.log(message, type, code);
		}
	});

	return ajax;
});

$.Component.extend("Controller", function() {

	var self = this,
		args = $.makeArray(arguments),
		name = args[0],
		staticProps,
		protoFactory;

	// Getter
	if (args.length==1) {
		return $.String.getObject(name);
	};

	// Setter
	if (args.length > 2) {
		staticProps = args[1],
		protoFactory = args[2]
	} else {
		staticProps = {},
		protoFactory = args[1]
	}

	// Map component as a static property
	// of the controller class
	$.extend(staticProps, {
		root: self.className + '.Controller',
		component: self
	});

	return $.Controller.apply(this, [name, staticProps, protoFactory]);
});

$.Component.extend("View", function(name) {

	var self = this;

	// Gett all component views
	if (arguments.length < 1) {
		return self.template();
	}

	// Prepend component prefix
	arguments[0] = self.prefix + arguments[0];

	// Getter or setter
	return $.View.apply(this, arguments);
});
// Component should always be the last core plugin to load.

// Execute all pending foundry modules
FD50.module.execute();

// Get all abstract components
$.each(FD50.component(), function(i, abstractComponent){

    // If this component is registered, stop.
    if (abstractComponent.registered) return;

    // Create an instance of the component
    $.Component.register(abstractComponent);
});

});
