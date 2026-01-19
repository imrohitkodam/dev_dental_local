<?php
/**
* @package		EasySocial
* @copyright	Copyright (C) Stack Ideas Sdn Bhd. All rights reserved.
* @license		GNU/GPL, see LICENSE.php
* EasySocial is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/
defined('_JEXEC') or die('Unauthorized Access');

jimport('joomla.filesystem.folder');
jimport('joomla.filesystem.file');

class com_EasySocialInstallerScript
{
	private $path = null;

	/**
	 * Cleanup older css and script files
	 *
	 * @since	3.0.0
	 * @access	public
	 */
	public function cleanup()
	{
		// 1. Cleanup script files
		$path = JPATH_ROOT . '/media/com_easysocial/scripts';
		$sections = array('admin', 'site');

		foreach ($sections as $section) {
			$files = JFolder::files($path, $section . '[\.\-].+\.js', true, true);

			foreach ($files as $file) {
				JFile::delete($file);
			}
		}

		// 2. Cleanup css files
		$locations = array(JPATH_ROOT, JPATH_ADMINISTRATOR);

		foreach ($locations as $location) {
			$path = $location . '/components/com_easysocial/themes';

			$files = JFolder::files($path, '.min.css$', true, true);

			if ($files) {
				foreach ($files as $file) {
					JFile::delete($file);
				}
			}
		}
	}

	/**
	 * Loads up the EasyBlog library
	 *
	 * @since	3.0.0
	 * @access	public
	 */
	public function engine()
	{
		$file = JPATH_ADMINISTRATOR . '/components/com_easysocial/includes/easysocial.php';

		if (!JFile::exists($file)) {
			return false;
		}

		// Include foundry framework
		require_once($file);
	}

	/**
	 * Convert utf8mb4 to utf8
	 *
	 * @since	3.0.0
	 * @access	public
	 */
	public function convertUtf8mb4QueryToUtf8($query)
	{
		if ($this->hasUTF8mb4Support()) {
			return $query;
		}

		// If it's not an ALTER TABLE or CREATE TABLE command there's nothing to convert
		$beginningOfQuery = substr($query, 0, 12);
		$beginningOfQuery = strtoupper($beginningOfQuery);

		if (!in_array($beginningOfQuery, array('ALTER TABLE ', 'CREATE TABLE'))) {
			return $query;
		}

		// Replace utf8mb4 with utf8
		return str_replace('utf8mb4', 'utf8', $query);
	}

	/**
	 * Determines if the file is an install.mysql.sql file
	 *
	 * @since	3.0.0
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
	 * @since	3.0.0
	 * @access	public
	 */
	public function isMySQL()
	{
		$jConfig = JFactory::getConfig();
		$dbType = $jConfig->get('dbtype');

		return $dbType == 'mysql' || $dbType == 'mysqli';
	}

	/**
	 * Determine if mysql can support utf8mb4 or not.
	 *
	 * @since	3.0.0
	 * @access	public
	 */
	public function hasUTF8mb4Support()
	{
		static $_cache = null;

		if (is_null($_cache)) {
			$client_version = '5.0.0';

			if (function_exists('mysqli_get_client_info')) {
				$client_version = mysqli_get_client_info();
			} else if (function_exists('mysql_get_client_info')) {
				$client_version = mysql_get_client_info();
			}

			if (strpos($client_version, 'mysqlnd') !== false) {
				$client_version = preg_replace('/^\D+([\d.]+).*/', '$1', $client_version);
				$_cache = version_compare($client_version, '5.0.9', '>=');
			} else {
				$_cache = version_compare($client_version, '5.5.3', '>=');
			}
		}

		return $_cache;
	}

	/**
	 * Determines if the file is part of an SQL query
	 *
	 * @since	3.0.0
	 * @access	public
	 */
	public function isQueries($file)
	{
		if (stristr($file, 'administrator/components/com_easysocial/queries') !== false) {
			return true;
		}

		return false;
	}

	/**
	 * Retrieves the currently installed EasyBlog script version
	 *
	 * @since	3.0.0
	 * @access	public
	 */
	public function getScriptVersion()
	{
		$this->engine();

		$table = ES::table('Config');
		$exists = $table->load(array('type' => 'scriptversion'));

		if ($exists) {
			return $table->value;
		}

		return false;
	}

	/**
	 * Retrieves the currently installed EasyBlog database version
	 *
	 * @since	3.0.0
	 * @access	public
	 */
	public function getDatabaseVersion()
	{
		$this->engine();

		$table = ES::table('Config');
		$exists = $table->load(array('type' => 'dbversion'));

		if ($exists) {
			return $table->value;
		}

		return false;
	}

	/**
	 * Reads the JSON metadata file
	 *
	 * @since	3.0.0
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
	 * @since	3.0.0
	 * @access	public
	 */
	public function getSource($file)
	{
		return $this->path . '/archive/' . $file;
	}

	/**
	 * Gets the file destination path
	 *
	 * @since	3.0.0
	 * @access	public
	 */
	public function getDestination($file)
	{
		return JPATH_ROOT . '/' . $file;
	}

	/**
	 * Triggered before the installation is complete
	 *
	 * @since	3.0.0
	 * @access	public
	 */
	public function preflight($action = 'update', $installer)
	{
		// Not used currently.
		// We can perform other pre-flight scripts before the update executes.

		$this->installUserColumns();
	}

	/**
	 * install required columns in user table if not exists
	 *
	 * @since	3.0
	 * @access	public
	 */
	public function installUserColumns()
	{
		$db = JFactory::getDBO();

		$verifiedColumnSql = "ALTER TABLE `#__social_users` ADD `verified` TINYINT(3) NOT NULL DEFAULT '0'";
		$paramColumnSql = "ALTER TABLE `#__social_users` ADD `social_params` LONGTEXT NOT NULL";
		$affiliationColumnSql = "ALTER TABLE `#__social_users` ADD `affiliation_id` VARCHAR(32) NOT NULL AFTER `verified`";
		$robotsColumnSql = "ALTER TABLE `#__social_users` ADD COLUMN `robots` VARCHAR(16) DEFAULT 'inherit'";

		// $columns = array(
		// 	'verified' => $verifiedColumnSql,
		// 	'social_params' => $paramColumnSql,
		// 	'affiliation_id' => $affiliationColumnSql,
		// 	'robots' => $robotsColumnSql
		// );

		$columns = array(
			'robots' => $robotsColumnSql
		);

		$query = "SHOW FIELDS FROM `#__social_users`";
		$db->setQuery($query);

		$rows = $db->loadObjectList();
		$fields	= array();

		foreach ($rows as $row) {
			$fields[] = $row->Field;
		}

		// do checking here:
		foreach ($columns as $column => $query) {
			$columnExist = in_array($column, $fields);

			// if not exists, lets add this column.
			if (!$columnExist) {
				$db->setQuery($query);
				$db->execute();
			}
		}

		return true;
	}
	/**
	 * Performs update on the extension
	 *
	 * @since	3.0.0
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
		$this->updateVersion('dbversion', $this->version);

		// Update the script version
		$this->updateVersion('scriptversion', $this->version);

		// post installation
		$this->postInstall();

	}

	/**
	 * Delete record in updates table
	 *
	 * @since	3.0.0
	 * @access	public
	 */
	public function postInstall()
	{

		// install points
		$this->installPoints();

		// install alerts
		$this->installAlerts();

		// install points
		$this->installPrivacy();

		// install points
		$this->installAccess();

		// install points
		$this->installBadges();

		// delete joomla update records
		$this->deleteUpdateRecord();

		// check if sef cache folder writable or not.
		$this->verifySefCacheWrite();
	}

	/**
	 * Verify if system has the write permision on sef cache folder
	 *
	 * @since	3.1.8
	 * @access	public
	 */
	public function verifySefCacheWrite()
	{
		$cacheLib = ES::Filecache();

		$fileFolder = SOCIAL_FILE_CACHE_DIR;
		$filepath = $cacheLib->getFilePath();
		$hasError = false;

		// default warning message on folder
		ES::language()->loadSite();
		$warning = JText::sprintf('COM_ES_SEF_CACHE_WARNING_NO_FOLDER_PERMISSION', '/media/com_easysocial/cache');

		// check if folder exists or not.
		if (!JFolder::exists($fileFolder)) {
			$canWrite = @JFolder::create($fileFolder);
			$hasError = $canWrite ? false : true;
		}

		// check if folder is writable or not.
		if (!$hasError) {
			// check if can write into the folder.
			$testFile = str_replace('-cache.php', '-test.php', $filepath);
			$canWrite = @JFile::write($testFile, '');
			$hasError = !$canWrite;

			if ($canWrite) {
				// delete the test file.
				JFile::delete($testFile);
			}
		}

		if (!$hasError && JFile::exists($filepath)) {

			// warning message on file.
			$relativePath = str_replace(JPATH_ROOT, '', $filepath);
			$warning = JText::sprintf('COM_ES_SEF_CACHE_WARNING_NO_FILE_PERMISSION', $relativePath);

			// can write into this file?
			$canWrite = @JFile::append($filepath, '');
			$hasError = !$canWrite;
		}

		if ($hasError) {
			// has error. we will disable the sef cache setting.
			$this->disableSEFCache($warning);
		}

		return true;
	}

	/**
	 * Method to disable the sef caching
	 *
	 * @since	3.1.8
	 * @access	public
	 */
	private function disableSEFCache($warningMsg = '')
	{
		$config = ES::config();

		// append user id disabled. let update the seo use id to false.
		$config->set('seo.cachefile.enabled', "0");
		$config->set('seo.cachefile.warning', $warningMsg);

		// Convert the config object to a json string.
		$jsonString = $config->toString();

		$configTable = ES::table('Config');
		if (!$configTable->load('site')) {
			$configTable->type  = 'site';
		}

		$configTable->set('value' , $jsonString);
		$state = $configTable->store();

		return $state;
	}

	/**
	 * Install points if any
	 *
	 * @since   3.0.0
	 * @access  public
	 */
	public function installPoints()
	{
		$path = $this->path . '/archive';

		// Retrieve the points model to scan for the path
		$model 	= ES::model('Points');

		// Scan and install badges
		$points = JFolder::files($path, '.points', true, true);

		if ($points) {
			foreach ($points as $point) {
				$model->install($point);
			}
		}

		return true;
	}

	/**
	 * Install Alerts if any
	 *
	 * @since	3.0.0
	 * @access	public
	 */
	public function installAlerts()
	{
		$path = $this->path . '/archive';

		// Retrieve the privacy model to scan for the path
		$model = ES::model('Alert');

		// Scan and install privacy
		$files = JFolder::files($path, '.alert', false, true);

		if ($files) {
			foreach ($files as $file) {
				$model->install($file);
			}
		}

		return true;
	}

	/**
	 * Install Privacy if any
	 *
	 * @since	3.0.0
	 * @access	public
	 */
	public function installPrivacy()
	{
		$path = $this->path . '/archive';

		// Retrieve the privacy model to scan for the path
		$model = ES::model('Privacy');

		// Scan and install privacy
		$files = JFolder::files($path, '.privacy', false, true);

		if ($files) {
			foreach ($files as $file) {
				$model->install($file);
			}
		}

		return true;
	}

	/**
	 * Install Access if any
	 *
	 * @since	3.0.0
	 * @access	public
	 */
	public function installAccess()
	{
		$path = $this->path . '/archive';

		// Scan and install alert files
		$model = ES::model('AccessRules');
		$files = JFolder::files($path, '.access$', true, true);

		if ($files) {
			foreach ($files as $file) {
				$model->install($file);
			}
		}

		return true;
	}


	/**
	 * Install Badges if any
	 *
	 * @since	3.0.0
	 * @access	public
	 */
	public function installBadges()
	{
		$path = $this->path . '/archive';

		// Retrieve the Badges model to scan for the path
		$model = ES::model('Badges');

		// Scan and install badges
		$badges = JFolder::files($path, '.badge$', true, true);

		if ($badges) {
			foreach ($badges as $badge) {
				$model->install($badge);
			}
		}

		return true;
	}



	/**
	 * Delete record in updates table
	 *
	 * @since   3.0.0
	 * @access  public
	 */
	public function deleteUpdateRecord()
	{
		$db = JFactory::getDBO();

		$query = 'DELETE FROM ' . $db->quoteName('#__updates') . ' WHERE ' . $db->quoteName('extension_id') . '=' . $db->Quote($this->getExtensionId());
		$db->setQuery($query);
		$db->execute();
	}

	/**
	 * Retrieves the extension id
	 *
	 * @since	3.0.0
	 * @access	public
	 */
	public function getExtensionId()
	{
		$db = ES::db();
		$sql = $db->sql();

		$sql->select('#__extensions', 'id');
		$sql->where('element', 'com_easysocial');

		$db->setQuery($sql);

		// Get the extension id
		$extensionId = $db->loadResult();

		return $extensionId;
	}


	/**
	 * Updates the database version in EasyBlog
	 *
	 * @since	3.0.0
	 * @access	public
	 */
	public function updateVersion($type = 'dbversion', $version)
	{
		$config = ES::table('Config');
		$config->load(array('type' => $type));
		$config->type = $type;
		$config->value = $version;

		// Save the configuration
		$config->store($config->type);
	}

	/**
	 * Process meta file
	 *
	 * @since	3.0.0
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
	 * @since	3.0.0
	 * @access	public
	 */
	public function processAddedFiles($files)
	{
		return $this->processModifiedFiles($files);
	}

	/**
	 * Process deleted files
	 *
	 * @since	3.0.0
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
	 * @since	3.0.0
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

			JFile::copy($source, $dest);
		}

		return true;
	}

	/**
	 * Process maintenance files
	 *
	 * @since	3.0.0
	 * @access	public
	 */
	public function processMaintenanceFiles($files)
	{
		// Get current DB version
		$version = $this->getDatabaseVersion();
		$updates = array();
		$db = ES::db();

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

			if (! JFile::exists($filePath)) {
				continue;
			}

			// Determines if the current installed version is lower than the file version
			$outdated = version_compare($version, $fileVersion) === -1;

			if (!$outdated) {
				// continue;
			}

			// Process PHP files
			if (stristr($fileName, '.php') !== false) {

				// To fix the problem with 3.2.1 where the apps model is already rendered when upgrading, it will not re-reload the apps model
				if ($fileName == 'UpdateAppsOrdering.php') {
					$this->rebuildAppsOrdering();
					continue;
				}

				// Get folder version
				$folderVersion = basename(dirname($file));
				$updatesFolder = JPATH_ADMINISTRATOR . '/components/com_easysocial/updates/' . $folderVersion;
				$exists = JFolder::exists($updatesFolder);

				if (!$exists) {
					JFolder::create($updatesFolder);
				}

				// Copy the php files into the respective location
				$dest = $updatesFolder . '/' . $fileName;

				if (!JFile::exists($dest)) {
					JFile::copy($filePath, $dest);
				}

				$maintenance = ES::maintenance();
				$maintenance->runScript($dest);
			}
		}
	}

	/**
	 * Process precompiled css and script files
	 *
	 * @since	4.0.4
	 * @access	public
	 */
	public function processPrecompiledFiles()
	{
		// Since EasySocial 4.0.4, we now store all the minified js files separately so we need to copy it accordingly
		$ds = DIRECTORY_SEPARATOR;
		$scripts = $this->path . $ds . 'archive' . $ds . 'precompiled' . $ds . 'scripts';

		$files = JFolder::files($scripts, '.', true, true);

		foreach ($files as $file) {
			$relativePath = ltrim(str_ireplace($scripts, '', $file), '/');
			$destination = JPATH_ROOT . '/media/com_easysocial/scripts/' . $relativePath;

			JFile::copy($file, $destination);
		}


		// 2. Copy admin css file
		$adminFolder = $this->path . '/archive/precompiled/stylesheets/admin/default';
		$files = JFolder::files($adminFolder);
		$target = JPATH_ROOT . '/administrator/components/com_easysocial/themes/default';
		$exists = JFolder::exists($target);

		if ($exists) {
			foreach ($files as $file) {
				$source = $adminFolder . '/' . $file;
				$dest = $target . '/styles/' . $file;

				JFile::copy($source, $dest);
			}
		}

		// 3. Copy site css files
		$stylesheets = $this->path . '/archive/precompiled/stylesheets/site';

		$folders = JFolder::folders($stylesheets, '.', false, true);

		foreach ($folders as $stylesheet) {
			$theme = basename($stylesheet);
			$target = JPATH_ROOT . '/components/com_easysocial/themes/' . $theme;
			$exists = JFolder::exists($target);

			if (!$exists) {
				continue;
			}

			// Ensure that the styles folder really exist
			if (!JFolder::exists($target . '/styles')) {
				continue;
			}

			$files = JFolder::files($stylesheet);

			foreach ($files as $file) {
				$source = $stylesheet . '/' . $file;
				$dest = $target . '/styles/' . $file;

				JFile::copy($source, $dest);
			}
		}
	}

	/**
	 * Process JSON files
	 *
	 * @since	3.0.0
	 * @access	public
	 */
	public function processJSON($filePath)
	{
		$db = ES::db();

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
				$db->execute();

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
	 * @since	3.0.0
	 * @access	public
	 */
	public function processRenamedFiles($files)
	{
		foreach ($files as $file) {
			$source = $this->getSource($file->old);
			$dest = $this->getDestination($file->new);

			// Check if the source already removed
			$sourceExists = JFile::exists($source);

			if (!$sourceExists) {
				continue;
			}

			// If old file exists, move it
			JFile::move($source, $dest);
		}

		return true;
	}

	/**
	 * Runs SQL queries based on the files
	 *
	 * @since	3.0.0
	 * @access	public
	 */
	public function runQueries($file)
	{
		$this->engine();

		$db = ES::db();

		// Get the contents of the file
		$contents = file_get_contents($file);
		
		if (JVERSION < 4.0) {
			$queries = JInstallerHelper::splitSql($contents);
		} else {
			$queries = JDatabaseDriver::splitSql($contents);
		}

		foreach ($queries as $query) {
			$query = trim($query);

			if ($this->isMySQL() && !$this->hasUTF8mb4Support()) {
				$query = $this->convertUtf8mb4QueryToUtf8($query);
			}

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

	/**
	 * Custom fix to fix updates to 3.2.1 to prevent errors in the app model
	 *
	 * @since	3.2.1
	 * @access	public
	 */
	public function rebuildAppsOrdering($group = false)
	{
		$db = ES::db();

		$groups = array('user', 'group', 'page', 'event');

		if ($group && in_array($group, $groups)) {
			$groups = array($group);
		}

		foreach ($groups as $group) {
			$querySet1 = "SET @ordering_interval = 1";
			$querySet2 = "SET @new_ordering = 0";

			$query = "UPDATE `#__social_apps` SET";
			$query .= " `ordering` = (@new_ordering := @new_ordering + @ordering_interval)";
			$query .= " WHERE `group` = " . $db->Quote($group);
			$query .= " AND `type` = " . $db->Quote('apps');
			$query .= " ORDER BY `ordering` ASC";

			// execute ordering_interval variable initiation.
			$db->setQuery($querySet1);
			$db->execute();

			// execute new_ordering variable initiation.
			$db->setQuery($querySet2);
			$db->execute();

			// now perform the update
			$db->setQuery($query);
			$db->execute();
		}

		return true;
	}
}
