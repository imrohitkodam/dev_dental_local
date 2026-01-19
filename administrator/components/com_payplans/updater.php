<?php
/**
* @package		PayPlans
* @copyright	Copyright (C) 2010 - 2018 Stack Ideas Sdn Bhd. All rights reserved.
* @license		GNU/GPL, see LICENSE.php
* PayPlans is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/
defined('_JEXEC') or die('Unauthorized Access');

jimport('joomla.filesystem.folder');
jimport('joomla.filesystem.file');

class com_PayPlansInstallerScript
{
	public $extension = 'com_payplans';
	public $name = 'payplans';

	private $path = null;

	/**
	 * Cleanup older css and script files
	 *
	 * @since	4.0.0
	 * @access	public
	 */
	public function cleanup()
	{
		// 1. Cleanup script files
		$path = JPATH_ROOT . '/media/' . $this->extension . '/scripts';
		$sections = array('admin', 'site');

		foreach ($sections as $section) {
			$files = JFolder::files($path, $section . '[\.\-].+\.js', true, true);

			foreach ($files as $file) {
				JFile::delete($file);
			}
		}

		// 2. Cleanup css files
		$path = JPATH_ROOT . '/media/' . $this->extension . '/themes';
		$files = JFolder::files($path, '.min.css$', true, true);

		if ($files) {
			foreach ($files as $file) {
				JFile::delete($file);
			}
		}
	}

	/**
	 * Loads up payplans library
	 *
	 * @since	4.0.0
	 * @access	public
	 */
	public function engine()
	{
		$file = JPATH_ADMINISTRATOR . '/components/' . $this->extension . '/includes/' . $this->name . '.php';

		if (!JFile::exists($file)) {
			return false;
		}

		// Include foundry framework
		require_once($file);
	}

	/**
	 * Determines if the file is an install.mysql.sql file
	 *
	 * @since	4.0.0
	 * @access	public
	 */
	public function isSqlFile($file)
	{
		if (stristr($file, 'install.mysql.sql') !== false) {
			return true;
		}

		return false;
	}

	/**
	 * Determine if database is set to mysql or not.
	 *
	 * @since	4.0.0
	 * @access	public
	 */
	public function isMySQL()
	{
		$jConfig = JFactory::getConfig();
		$dbType = $jConfig->get('dbtype');

		return $dbType == 'mysql' || $dbType == 'mysqli';
	}

	/**
	 * Determines if the file is part of an SQL query
	 *
	 * @since	4.0.0
	 * @access	public
	 */
	public function isQueries($file)
	{
		if (stristr($file, 'administrator/components/' . $this->extension . '/queries') !== false) {
			return true;
		}

		return false;
	}

	/**
	 * Retrieves the currently installed PayPlan's script version
	 *
	 * @since	4.0.0
	 * @access	public
	 */
	public function getScriptVersion()
	{
		$this->engine();

		$table = PP::table('Config');
		$exists = $table->load(array('key' => 'script_version'));

		if ($exists) {
			return $table->value;
		}

		return false;
	}

	/**
	 * Retrieves the currently installed PayPlan's database version
	 *
	 * @since	4.0.0
	 * @access	public
	 */
	public function getDatabaseVersion()
	{
		$this->engine();

		$table = PP::table('Config');
		$exists = $table->load(array('key' => 'db_version'));

		if ($exists) {
			return $table->value;
		}

		return false;
	}

	/**
	 * Reads the JSON metadata file
	 *
	 * @since	4.0.0
	 * @access	public
	 */
	public function getMeta()
	{
		$file = $this->path . '/meta.json';

		$contents = file_get_contents($file);

		$meta = json_decode($contents);

		return $meta;
	}

	/**
	 * Gets the file source path
	 *
	 * @since	4.0.0
	 * @access	public
	 */
	public function getSource($file)
	{
		return $this->path . '/archive/' . $file;
	}

	/**
	 * Gets the file destination path
	 *
	 * @since	4.0.0
	 * @access	public
	 */
	public function getDestination($file)
	{
		return JPATH_ROOT . '/' . $file;
	}

	/**
	 * Triggered before the installation is complete
	 *
	 * @since	4.0.0
	 * @access	public
	 */
	public function preflight($action = 'update', $installer)
	{
		// Not used currently.
		// We can perform other pre-flight scripts before the update executes.
	}

	/**
	 * Performs update on the extension
	 *
	 * @since	4.0.0
	 * @access	public
	 */
	public function update($installer)
	{
		$engine = $this->engine();

		$parent = $installer->getParent();

		$this->version = (string) $installer->manifest->version;
		$this->path = $parent->getPath('source');

		$meta = $this->getMeta();

		// Cleanup older css and js files
		$this->cleanup();

		// Process meta file
		$this->processMeta($meta, $this->path);

		// Update the database version
		$this->updateVersion('db_version', $this->version);

		// Update the script version
		$this->updateVersion('script_version', $this->version);

		// post installation
		$this->postInstall();
	}

	/**
	 * Delete record in updates table
	 *
	 * @since	4.0.12
	 * @access	public
	 */
	public function postInstall()
	{
		// Fix log folder
		$this->fixLogs();

		// insert action logs.
		$this->installActionLogs();

		// insert any new modules
		$this->installModules();

		// insert any new plugins
		$this->installPlugins();
	}

	/**
	 * Install new modules incase there is no maintenance script created.
	 *
	 * @since	4.1.5
	 * @access	public
	 */
	public function installModules() 
	{
		if ($this->version == '4.1.5') {
			// we need to install Plan module.
			$this->installPlanModule();
		}
	}

	/**
	 * Install new plugins incase there is no maintenance script created.
	 *
	 * @since	4.2
	 * @access	public
	 */
	public function installPlugins() 
	{
		if ($this->version == '4.2.1') {
			// we need to install Plan plugins.
			$this->installPlugins421();
		}

		if ($this->version == '4.2.2') {
			// we need to install Plan plugins.
			$this->installPlugins422();
		}
	}


	/**
	 * Install new plugins added in 4.2.2
	 *
	 * @since	4.2.2
	 * @access	public
	 */
	public function installPlugins422()
	{
		$plugins = array(
			'payplans/ccavenue'
			);

		$this->installNewPlugins($plugins);

	}

	/**
	 * Install new plugins added in 4.2.1
	 *
	 * @since	4.2
	 * @access	public
	 */
	public function installPlugins421()
	{
		$plugins = array(
			'payplans/djcpoints',
			'payplans/easydiscussticketsubmission',
			'payplans/easysocialeventsubmission',
			'payplans/easysocialgroupsubmission',
			'payplans/easysocialpagesubmission',
			'sppagebuilder/payplans',
			);

		$this->installNewPlugins($plugins);
	}

	/**
	 * Install new plugins
	 *
	 * @since	4.1.5
	 * @access	public
	 */
	private function installNewPlugins($plugins)
	{
		if (!$plugins) {
			return;
		}

		foreach ($plugins as $plugin) {

			$pluginPath = rtrim(JPATH_ROOT, '/') . '/plugins/' . $plugin;

			$tmp = explode('/', $plugin);

			$group = $tmp[0];
			$element = $tmp[1];

			$folderExists = JFolder::exists($pluginPath);
			$xmlExists = JFile::exists($pluginPath . '/' . $element . '.xml');

			// make sure local has all the required files before we proceed.
			if ($folderExists && $xmlExists) {

				$now = time();

				// copy folder to Joomla tmp folder
				$tmpRootPath = rtrim(JPATH_ROOT, '/') . '/tmp/' . $now;
				$tmpPath = $tmpRootPath . '/plg_' . $element;
				JFolder::copy($pluginPath, $tmpPath);
				
				// Get Joomla's installer instance
				$installer = new JInstaller();

				// Allow overwriting existing plugins
				$installer->setOverwrite(true);
				$state = $installer->install($tmpPath);

				if ($state) {

					// make sure these plugin is enabled.
					$plg = JTable::getInstance('extension');
					$options = array('folder' => strtolower($group), 'element' => strtolower($element));
					$exists = $plg->load($options);

					if ($exists) {
						$plg->enabled = true;
						$plg->store();
					}
				}

				// delete tmp folder
				JFolder::delete($tmpRootPath);
			}
		}
	}

	/**
	 * Install plan module.
	 *
	 * @since	4.1.5
	 * @access	public
	 */
	private function installPlanModule()
	{
		$moduleName = 'mod_payplans_plan';
		$modulePath = rtrim(JPATH_ROOT, '/') . '/modules/' . $moduleName;

		// check if files exists or not.
		$folderExists = JFolder::exists($modulePath);
		$xmlExists = JFile::exists($modulePath . '/' . $moduleName . '.xml');

		// make sure local has all the required files before we proceed.
		if ($folderExists && $xmlExists) {

			$now = time();

			// copy folder to Joomla tmp folder
			$tmpRootPath = rtrim(JPATH_ROOT, '/') . '/tmp/' . $now;
			$tmpPath = $tmpRootPath . '/' . $moduleName;
			JFolder::copy($modulePath, $tmpPath);
			
			// Get Joomla's installer instance
			$installer = new JInstaller();

			// Allow overwriting existing plugins
			$installer->setOverwrite(true);
			$state = $installer->install($tmpPath);

			// delete tmp folder
			JFolder::delete($tmpRootPath);
		}

		return true;
	}

	/**
	 * Fix log files in the log folder to address issue with public users accessing log files
	 *
	 * @since	4.0.12
	 * @access	public
	 */
	public function fixLogs()
	{
		// check if there is already the .htaccess file created
		// in log folder.
		$this->addHTAccessFile();

		// now we fix the logs by converting the txt file into php file.
		$files = $this->getLegacyLFiles();
		if ($files) {
			foreach ($files as $file) {
				$this->fixLegacyFile($file);
			}
		}

		// Remove legacy folder
		$legacyLogFolder = JPATH_ROOT . '/media/payplans/log';

		if (JFolder::exists($legacyLogFolder)) {
			JFolder::delete($legacyLogFolder);
		}
	}

	/**
	 * Inserts necessary data for action logs
	 *
	 * @since	4.1
	 * @access	public
	 */
	public function installActionLogs()
	{
		// If the Joomla version is not starting from 3.9, do not need to proceed
		// Because Joomla User Actions log only available from version 3.9 onwards.
		$currentJVersion = PP::getJoomlaVersion();
		$hasActionsLog = version_compare('3.9', $currentJVersion) !== 1;

		if ($hasActionsLog) {

			$db = PP::db();

			$query = 'SELECT COUNT(1) FROM `#__action_logs_extensions` WHERE `extension`=' . $db->quote('com_payplans');

			$db->setQuery($query);

			$exists = $db->loadResult() > 0;

			if (!$exists) {
				$query = 'INSERT INTO `#__action_logs_extensions` (`extension`) VALUES (' . $db->Quote('com_payplans') . ')';

				$db->setQuery($query);
				$db->Query();
			}
		}
	}


	public static function fixLegacyFile($file)
	{
		$contents = file_get_contents($file);

		$prepend = "<" . "?" . "php defined('_JEXEC') or die('Unauthorized Access'); " . "?" . ">" . PHP_EOL;
		
		$contents = $prepend . $contents;

		// Generate a new file name
		$newFile = str_ireplace('.txt', '.php', $file);
			
		JFile::write($newFile, $contents);

		// Delete the old file
		JFile::delete($file);
	}

	/**
	 * Detects for legacy log files (Readybytes era .txt log files)
	 *
	 * @since	4.0.12
	 * @access	public
	 */
	public static function getLegacyLFiles()
	{
		$path = JPATH_ROOT . '/media/com_payplans/log';

		$files = JFolder::files($path, '.txt', true, true);

		return $files;
	}

	/**
	 * Detects for legacy log files (Readybytes era .txt log files)
	 *
	 * @since	4.0.12
	 * @access	public
	 */
	public static function addHTAccessFile()
	{
		$path = JPATH_ROOT . '/media/com_payplans/log';

		$file = $path . '/.htaccess';

		if (!JFile::exists($file)) {

			$content = '# Deny access to .htaccess
<Files .htaccess>
Order allow,deny
Deny from all
</Files>

# Deny access to files with extensions:
<FilesMatch "\.(php|txt|log|ini)$">
Order allow,deny
Deny from all
</FilesMatch>';

			JFile::write($file, $content);
		}

		return true;
	}


	/**
	 * Updates the database version in PayPlans
	 *
	 * @since	4.0.0
	 * @access	public
	 */
	public function updateVersion($type = 'db_version', $version)
	{
		$config = PP::table('Config');
		$config->load(array('key' => $type));
		$config->key = $type;
		$config->value = $version;

		// Save the configuration
		$config->store();
	}

	/**
	 * Process meta file
	 *
	 * @since	4.0.0
	 * @access	public
	 */
	public function processMeta($meta)
	{
		if ($meta->added) {
			$this->processAddedFiles($meta->added);
		}

		if ($meta->modified) {
			$this->processModifiedFiles($meta->modified);
		}

		if ($meta->deleted) {
			$this->processDeletedFiles($meta->deleted);
		}

		if ($meta->renamed) {
			$this->processRenamedFiles($meta->renamed);
		}

		// Process maintenance scripts
		if ($meta->maintenance) {
			$this->processMaintenanceFiles($meta->maintenance);
		}

		// Process precompiled files
		$this->processPrecompiledFiles();
	}

	/**
	 * Process added files
	 *
	 * @since	4.0.0
	 * @access	public
	 */
	public function processAddedFiles($files)
	{
		return $this->processModifiedFiles($files);
	}

	/**
	 * Process deleted files
	 *
	 * @since	4.0.0
	 * @access	public
	 */
	public function processDeletedFiles($files)
	{
		foreach ($files as $file) {
			$exists = JFile::exists($file);

			if ($exists) {
				JFile::delete($file);
			}
		}

		return true;
	}

	/**
	 * Process modified files
	 *
	 * @since	4.0.0
	 * @access	public
	 */
	public function processModifiedFiles($files)
	{
		foreach ($files as $file) {
			$source = $this->getSource($file);
			$dest = $this->getDestination($file);

			// Detect for install.mysql.sql
			if ($this->isSqlFile($file)) {
				JFile::copy($source, $dest);

				$this->runQueries($dest);

				continue;
			}

			$folder = dirname($dest);

			if (!JFolder::exists($folder)) {
				JFolder::create($folder);
			}

			// For query files, we need to execute them
			if ($this->isQueries($file)) {
				JFile::copy($source, $dest);

				$this->runQueries($dest);

				continue;
			}

			// make sure the source is exists before we copy
			$sourceExists = JFile::exists($source);
			if ($sourceExists) {
				JFile::copy($source, $dest);
			}
		}

		return true;
	}

	/**
	 * Process maintenance files
	 *
	 * @since	4.0.0
	 * @access	public
	 */
	public function processMaintenanceFiles($files)
	{
		// Get current DB version
		$version = $this->getDatabaseVersion();
		$updates = array();
		$db = PP::db();

		// Arrays to store all table data
		$tables = array();
		$indexes = array();
		$changes = array();

		$phpFiles = array();

		// Figure out which version script needs to be executed
		// Step 1: lets process json files first.
		foreach ($files as $file) {
			$fileName = basename($file);
			$fileVersion = basename(dirname($file));
			$filePath = $this->path . '/archive/' . $file;

			if (! JFile::exists($filePath)) {
				continue;
			}

			// Determines if the current installed version is lower than the file version
			$outdated = version_compare($version, $fileVersion) === -1;

			if (!$outdated) {
				// continue;
			}

			// Process JSON files
			if (stristr($fileName, '.json') !== false) {
				$this->processJSON($filePath);
			}
		}

		// Step 2: now we process php maintenane scripts.
		foreach ($files as $file) {
			$fileName = basename($file);
			$fileVersion = basename(dirname($file));
			$filePath = $this->path . '/archive/' . $file;

			// Determines if the current installed version is lower than the file version
			$outdated = version_compare($version, $fileVersion) === -1;

			if (!$outdated) {
				// continue;
			}

			// Process PHP files
			if (stristr($fileName, '.php') !== false) {

				// Get folder version
				$folderVersion = basename(dirname($file));
				$updatesFolder = JPATH_ADMINISTRATOR . '/components/' . $this->extension . '/updates/' . $folderVersion;
				$exists = JFolder::exists($updatesFolder);

				if (!$exists) {
					JFolder::create($updatesFolder);
				}

				// Copy the php files into the respective location
				$dest = $updatesFolder . '/' . $fileName;

				if (!JFile::exists($dest)) {
					JFile::copy($filePath, $dest);
				}

				// temporary fix on plugin installer in maintenance script.
				$this->fixMaintenanceScript($dest);

				$maintenance = PP::maintenance();
				$maintenance->runScript($dest);
			}
		}
	}

	/**
	 * Function to temporaty fix the JInstaller issues.
	 *
	 * @since	4.0.0
	 * @access	public
	 */
	public function fixMaintenanceScript($file)
	{
		if (stristr($file, 'updates/4.1.3/reinstallPluginAuthentication.php') !== false || stristr($file, 'updates/4.1.4/reinstallAuthenticationPlugin.php') !== false ) {

			$contents = file_get_contents($file);

			$newContents = str_ireplace('$installer = JInstaller::getInstance();', '$installer = new JInstaller();', $contents);
			
			// overwrite exsiting file
			JFile::write($file, $newContents);
		}
	}

	/**
	 * Process precompiled css and script files
	 *
	 * @since	4.0.0
	 * @access	public
	 */
	public function processPrecompiledFiles()
	{
		$scripts = $this->path . '/archive/precompiled/scripts';

		// 1. Copy script files first
		$files = JFolder::files($scripts, '.', false, true);

		foreach ($files as $script) {
			$dest = JPATH_ROOT . '/media/' . $this->extension . '/scripts/' . basename($script);

			JFile::copy($script, $dest);
		}

		// 2. Copy admin css file
		$adminFolder = $this->path . '/archive/precompiled/stylesheets/admin';
		$files = JFolder::files($adminFolder);
		$target = JPATH_ROOT . '/media/' . $this->extension . '/themes/admin/css';
		$exists = JFolder::exists($target);

		if ($exists) {
			foreach ($files as $file) {
				$source = $adminFolder . '/' . $file;
				$dest = $target . '/' . $file;
				
				JFile::copy($source, $dest);
			}
		}

		// 3. Copy site css files
		$stylesheets = $this->path . '/archive/precompiled/stylesheets/site';

		$files = JFolder::files($stylesheets, '.', false, true);

		foreach ($files as $file) {
			$fileName = basename($file);

			$source = $file;
			$dest = JPATH_ROOT . '/media/' . $this->extension . '/themes/site/css/' . $fileName;
			
			JFile::copy($source, $dest);
		}
	}

	/**
	 * Process JSON files
	 *
	 * @since	4.0.0
	 * @access	public
	 */
	public function processJSON($filePath)
	{
		$db = PP::db();

		$contents = file_get_contents($filePath);
		$result = json_decode($contents);
		
		// Default values
		$columnExists = true;
		$indexExists = true;
		$alterTable = false;

		foreach ($result as $row) {
			
			// New column added
			if (isset($row->column)) {

				// Store the list of tables that needs to be queried
				if (!isset($tables[$row->table])) {
					$tables[$row->table] = $db->getTableColumns($row->table);
				}

				// Check if the column is in the fields or not
				$columnExists = in_array($row->column, $tables[$row->table]);
			}

			// Alter table
			if (isset($row->alter)) {
				$alterTable = true;
			}

			// Index column
			if (isset($row->index)) {
				if (!isset($indexes[$row->table])) {
					$indexes[$row->table] = $db->getTableIndexes($row->table);
				}

				$indexExists = in_array($row->index, $indexes[$row->table]);
			}

			if ($alterTable || !$columnExists || !$indexExists) {
				$db->setQuery($row->query);
				$db->Query();

				if (!$columnExists) {
					$tables[$row->table][] = $row->column;
				}

				if (!$indexExists) {
					$indexes[$row->table][] = $row->index;
				}

				if ($alterTable) {
					$changes[$row->table][] = $row->alter;
				}
			}
		}
	}

	/**
	 * Process renamed files
	 *
	 * @since	4.0.0
	 * @access	public
	 */
	public function processRenamedFiles($files)
	{
		foreach ($files as $file) {
			$source = $this->getSource($file->old);
			$dest = $this->getDestination($file->new);

			// Check if the source already removed
			$sourceExists = JFile::exists($source);

			if ($sourceExists) {
				// If old file exists, move it
				JFile::move($source, $dest);
				continue;
			}

			// for some reason, the new files are being detected as renamed files.
			// we need to make sure the new files is not exists in local.
			$newFiles = [];
			$newsource = $this->getDestination($file->new);
			$newSourceExists = JFile::exists($newsource);

			// if the file is a precompiled files, we need to skip the file.
			$isPrecompileFiles = strpos($file->new, 'precompiled/');

			if (!$newSourceExists && $isPrecompileFiles === false) {
				// make sure this is a new file. If yes, lets copy to the destination.
				$newFiles[] = $file->new;
			}

			if ($newFiles) {
				$this->processModifiedFiles($newFiles);
			}
		}

		return true;
	}

	/**
	 * Runs SQL queries based on the files
	 *
	 * @since	4.0.0
	 * @access	public
	 */
	public function runQueries($file)
	{
		$this->engine();

		$db = PP::db();

		// Get the contents of the file
		$contents = file_get_contents($file);

		if (JVERSION < 4.0) {
			$queries = JInstallerHelper::splitSql($contents);
		} else {
			$queries = JDatabaseDriver::splitSql($contents);
		}

		foreach ($queries as $query) {
			$query = trim($query);

			if (!empty($query)) {
				$db->setQuery($query);

				try {
					$state = $db->execute();
				} catch (Exception $e) {
					// do nothing.
				}
			}
		}
	}
}
