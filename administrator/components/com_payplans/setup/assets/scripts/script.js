$(document).ready(function(){

	loading = $('[data-installation-loading]'),
	submit = $('[data-installation-submit]'),
	retry = $('[data-installation-retry]'),
	form = $('[data-installation-form]'),
	completed = $('[data-installation-completed]'),
	source = $('[data-source]'),
	installAddons = $('[data-installation-install-addons]'),
	steps = $('[data-installation-steps]');
});

var pp = {

	init: function() {
	},

	options: {
		"apikey": "<?php echo $input->get('apikey', '');?>",
		"path": null,
		"controller": "install"
	},

	ajaxUrl: "<?php echo JURI::root();?>administrator/index.php?option=com_payplans&ajax=1",

	ajax: function(task, properties, callback) {

		var prop = $.extend(pp.options, properties);

		var dfd = $.Deferred();

		$.ajax({
			type: "POST",
			url: pp.ajaxUrl + "&controller=" + prop.controller + "&task=" + task,
			data: prop
		}).done(function(result) {
			callback && callback.apply(this, [result]);

			dfd.resolve(result);
		});

		return dfd;
	},


	addons: {

		configs: {
			plugins: [],
			modules: [],
			plgTmpPath:'',
			modTmpPath:'',
			sampledata: true,
			maintenance: []
		},

		setup: function(plgPath, modPath, plugins, modules, sampledata, maintenance) {

			pp.addons.configs.plgTmpPath = plgPath;
			pp.addons.configs.modTmpPath = modPath;
			pp.addons.configs.plugins = plugins;
			pp.addons.configs.modules = modules;
			pp.addons.configs.sampledata = sampledata;
			pp.addons.configs.maintenance = maintenance;
		},

		install: function() {

			// 1. modules
			// 2. plugins
			// 3. sampledata
			// 4. maintenance scripts

			// Show loading indicator
			loading.removeClass('d-none');
			installAddons.addClass('d-none');

			pp.addons.updateProgressBar('addons', '10');

			// install modules.
			pp.addons.installModules();

		},

		installModules: function() {

			if (pp.addons.configs.modules.length > 0) {

				var moduleName = pp.addons.configs.modules.shift();

				// Run the ajax calls now
				pp.ajax('module', {"controller": "addons", "path": pp.addons.configs.modTmpPath, 'module': moduleName}, function(result) {

					pp.addons.insertLog(result.message);


					// Update the progress
					pp.addons.updateProgressBar('addons', '20');

					// now lets check if we need to run the next plugins group or not.
					pp.addons.installModules();
				});

			} else {

				pp.addons.updateProgressBar('addons', '40');

				// lets install sample data
				pp.addons.installPlugins();
			}

		},


		installPlugins: function() {

			// Update the progress
			pp.addons.updateActivityLog('addons','Installing plugins ...');

			if (pp.addons.configs.plugins.length > 0) {

				var group = pp.addons.configs.plugins.shift();

				// Run the ajax calls now
				pp.ajax('plugin', {"controller": "addons", "path": pp.addons.configs.plgTmpPath, "group": group}, function(result) {

					pp.addons.insertLog(result.message);

					pp.addons.updateProgressBar('addons', '50');

					// now lets check if we need to run the next plugins group or not.
					pp.addons.installPlugins();
				});

			} else {

				pp.addons.updateProgressBar('addons', '70');

				// lets install sample data
				pp.addons.installSampleData();
			}

		},

		installSampleData: function() {

			pp.addons.updateActivityLog('addons','Installing sample data ...');

			if (pp.addons.configs.sampledata) {

				// Run the ajax calls now
				pp.ajax('install', {"controller": "sampledata", "sampledata": pp.addons.configs.sampledata}, function(result) {

					pp.addons.updateActivityLog('addons',result.message);

					// Update the progress
					pp.addons.updateProgressBar('addons', '100');
					pp.addons.installMaintenanceScripts();
				});

			} else {

				pp.addons.updateProgressBar('addons', '100');

				pp.addons.complete('addons', 'Installation of module and plugins completed.', false);

				pp.addons.installMaintenanceScripts();
			}

		},

		installMaintenanceScripts: function()
		{
			// now lets check if we need to run the next plugins group or not.
			if (pp.addons.configs.maintenance.length > 0) {


				var script = pp.addons.configs.maintenance.shift();

				// Run the ajax calls now
				pp.ajax('execute', {"controller": "maintenance", "script": script}, function(result) {

					pp.addons.updateProgressBar('sync', '40');
					pp.addons.insertLog(result.message);

					// check if we stil need to run the next script or not.
					pp.addons.installMaintenanceScripts();

				});

			} else {

				// update the progress bar here
				pp.addons.updateProgressBar('sync', '60');

				// run finalize step
				pp.addons.finalize();
			}
		},

		finalize: function() {

			pp.addons.updateProgressBar('sync', '80');

			// Run the ajax calls now
			pp.ajax('finalize', {"controller": "maintenance"}, function(result) {

				// update the progress bar here
				pp.addons.updateProgressBar('sync', '100');

				// add log
				pp.addons.insertLog(result.message);

				// process the complete step
				pp.addons.complete('sync', result.stateMessage, true);

			});

		},


		complete: function(type, msg, finalstep) {

			var progressBarMessage = $('[data-progress-active-message]');
			var progressBarCompleteMessage = $('[data-progress-complete-message]');

			if (type == 'sync') {
				var progressBarMessage = $('[data-sync-progress-active-message]');
				var progressBarCompleteMessage = $('[data-sync-progress-complete-message]');
			}

			progressBarMessage.addClass('d-none');
			progressBarCompleteMessage.removeClass('d-none');


			if (finalstep === true) {
				loading.addClass('d-none');
				submit.removeClass('d-none');

				// Update the progress
				$('[data-progress-execscript]')
					.find('.progress-state')
					.html(msg)
					.addClass('text-success')
					.removeClass('text-info');

				// now we bind onclick event
				submit.on('click', function() {
					form.submit();
				});
			}

		},

		updateProgressBar:function(type, percentage)
		{
			var progressBar = $('[data-progress-bar]');

			if (type == 'sync') {
				progressBar = $('[data-sync-progress-bar]');
			}

			progressBar.css('width', percentage + '%');
		},


		updateActivityLog: function(type, message) {

			var progressBarMessage = $('[data-progress-active-message]');

			if (type == 'sync') {
				progressBarMessage = $('[data-sync-progress-active-message]');
			}

			progressBarMessage.html(message);
		},



		insertLog: function(message) {
			var logs = $('[data-progress-logs]');
			var item = $('<li class="mt-0">');

			item.addClass('pp-text-success').html(message);

			logs.append(item);
		},

		retrieveList: function() {

			var selection = $('[data-addons-container]');
			var progress = $('[data-addons-progress]');
			var syncProgress = $('[data-sync-progress]');

			// Hide submit
			submit.addClass('d-none');

			pp.ajax('list', {"controller": "addons", "path": pp.options.path}, function(result){

				// Hide the retrieving message
				var retrievingMessage = $('[data-addons-retrieving]');
				retrievingMessage.addClass('d-none');

				loading.addClass('d-none');
				installAddons.removeClass('d-none');

				selection.html(result.html);

				// Get files for maintenance
				var scripts = result.scripts;

				var maintenanceMsg = result.maintenanceMsg;

				// Set the submit
				installAddons.on('click', function() {

					// Hide the container
					selection.addClass('d-none');

					// Show the installation progress
					progress.removeClass('d-none');
					syncProgress.removeClass('d-none');

					// Install the selected items
					var modules = [];
					var plugins = [];

					$('[data-checkbox-module]:checked').each(function(i, el) {
						modules.push($(el).val());
					});

					// Since all plugins needs to be installed, we should just check it
					$('[data-plugins]').each(function() {
						plugins.push($(this).val())
					});

					var sampleData = 0;
					if($('#sample-data').is(":checked")){
						sampleData = 1;
					}

					pp.addons.setup(result.pluginPath, result.modulePath, plugins, modules, sampleData, scripts);
					pp.addons.install();

					// var total = modules.length + plugins.length;
					// var each = 100 / total;
					// var progressBar = $('[data-progress-bar]');
					// var progressBarMessage = $('[data-progress-active-message]');
					// var progressBarCompleteMessage = $('[data-progress-complete-message]');

					// var totalScripts = scripts.length;
					// var eachScript = 100 / totalScripts;
					// var syncProgressBar = $('[data-sync-progress-bar]');
					// var syncProgressBarResult = $('[data-sync-progress-bar-result]');

					// // Show loading indicator
					// loading.removeClass('d-none');
					// installAddons.addClass('d-none');

				});
			});
		}
	},

	installation: {
		path: null,

		showRetry: function(step) {

			steps.addClass('error');

			retry
				.data('retry-step', step)
				.removeClass('hide');

			// Hide the submit
			submit.addClass('hide');

			// Hide the loading
			loading.addClass('hide');
		},

		extract: function() {

			pp.installation.setActive('data-progress-extract');

			pp.ajax('extract', {}, function(result) {

				// Update the progress
				pp.installation.update('data-progress-extract', result, '10%');

				if (!result.state) {
					pp.installation.showRetry('extract');
					return false;
				}

				// Set the path
				pp.options.path = result.path;

				// Run the next command
				pp.installation.runSQL();
			});
		},

		download: function() {

			pp.installation.setActive('data-progress-download');

			pp.ajax('download', {}, function(result) {

				// Set the progress
				pp.installation.update('data-progress-download', result, '10%');

				if (!result.state) {
					pp.installation.showRetry('download');
					return false;
				}

				// Set the installation path
				pp.options.path = result.path;

				pp.installation.runSQL();
			});
		},

		runSQL: function() {

			// Install the SQL stuffs
			pp.installation.setActive('data-progress-sql');

			pp.ajax('sql', {}, function(result) {

				// Update the progress
				pp.installation.update('data-progress-sql', result, '15%');

				if (!result.state) {
					pp.installation.showRetry('runSQL');
					return false;
				}

				// Run the next command
				pp.installation.installFoundry();
			});
		},

		installFoundry : function() {

			// Install the foundry package
			pp.installation.setActive('data-progress-foundry');

			pp.ajax('foundry', {}, function(result) {
				// Set the progress
				pp.installation.update('data-progress-foundry', result, '18%');

				if (!result.state) {
					pp.installation.showRetry('installFoundry');
					return false;
				}

				pp.installation.installAdmin();
			});
		},

		installAdmin: function() {

			// Install the admin stuffs
			pp.installation.setActive('data-progress-admin');

			// Run the ajax calls now
			pp.ajax('copy', {"type": "admin"}, function(result) {

				// Update the progress
				pp.installation.update('data-progress-admin', result, '20%');

				if (!result.state) {
					pp.installation.showRetry('installAdmin');
					return false;
				}

				pp.installation.installSite();
			});
		},

		installSite : function() {

			// Install the admin stuffs
			pp.installation.setActive('data-progress-site');

			pp.ajax('copy', { "type" : "site" }, function(result) {


				// Update the progress
				pp.installation.update('data-progress-site', result, '25%');

				if (!result.state) {
					pp.installation.showRetry('installSite');
					return false;
				}

				pp.installation.installLanguages();
			});
		},

		installLanguages : function() {
			// Install the admin stuffs
			pp.installation.setActive('data-progress-languages');

			pp.ajax('copy', {"type": "languages"}, function(result) {

				// Set the progress
				pp.installation.update('data-progress-languages', result, '30%');

				if (!result.state) {
					pp.installation.showRetry('installLanguages');
					return false;
				}

				pp.installation.installMedia();
			});

		},

		installMedia : function() {

			// Install the admin stuffs
			pp.installation.setActive('data-progress-media');

			pp.ajax('copy', {"type": "media"}, function(result) {
				// Set the progress
				pp.installation.update('data-progress-media', result, '35%');

				if (!result.state) {
					pp.installation.showRetry('installMedia');
					return false;
				}

				pp.installation.installToolbar();
			});
		},

		installToolbar : function() {

			// Install the admin stuffs
			pp.installation.setActive('data-progress-toolbar');

			pp.ajax('toolbar', {}, function(result) {
				// Set the progress
				pp.installation.update('data-progress-toolbar', result, '40%');

				if (!result.state) {
					pp.installation.showRetry('installToolbar');
					return false;
				}

				pp.installation.syncDB();
			});
		},

		syncDB: function() {

			// Synchronize the database
			pp.installation.setActive('data-progress-syncdb');

			pp.ajax('sync', {}, function(result) {
				pp.installation.update('data-progress-syncdb', result, '45%');

				if (!result.state) {
					pp.installation.showRetry('syncDB');
					return false;
				}

				pp.installation.postInstall();
			});
		},

		postInstall : function() {

			// Perform post installation stuffs here
			pp.installation.setActive('data-progress-postinstall');

			pp.ajax('post', {}, function(result) {

				// Set the progress
				pp.installation.update('data-progress-postinstall', result, '100%');

				if (!result.state) {
					pp.installation.showRetry('postInstall');
					return false;
				}

				// Show the complete notice and next step button
				completed.removeClass('d-none');
				submit.removeClass('d-none');

				submit.on('click', function() {
					source.val(pp.options.path);

					form.submit();
				});

			});
		},

		update: function(element, obj, progress) {
			var logItem = $('[' + element + ']');

			logItem.removeClass("is-loading")
				.addClass(obj.state ? 'is-complete' : 'is-error');
		},

		setActive: function(item) {
			var logItem = $('[' + item + ']');

			logItem.addClass('is-loading');
		}
	}
}
