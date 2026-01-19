<?php
/**
* @package		EasyBlog
* @copyright	Copyright (C) 2010 - 2017 Stack Ideas Sdn Bhd. All rights reserved.
* @license		GNU/GPL, see LICENSE.php
* EasyBlog is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/
defined('_JEXEC') or die('Unauthorized Access');

jimport('joomla.filesystem.folder');
jimport('joomla.filesystem.file');

class com_EasyBlogInstallerScript
{
	private $path = null;

	/**
	 * Cleanup older css and script files
	 *
	 * @since	5.2.0
	 * @access	public
	 */
	public function cleanup()
	{
		// 1. Cleanup script files
		$path = JPATH_ROOT . '/media/com_easyblog/scripts';
		$sections = array('admin', 'composer', 'dashboard', 'site');

		foreach ($sections as $section) {
			$files = JFolder::files($path, $section . '[\.\-].+\.js', true, true);

			foreach ($files as $file) {
				JFile::delete($file);
			}
		}

		// 2. Cleanup css files
		$locations = array(JPATH_ROOT, JPATH_ADMINISTRATOR);

		foreach ($locations as $location) {
			$path = $location . '/components/com_easyblog/themes';

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
	 * @since	5.2.0
	 * @access	public
	 */
	public function engine()
	{
		$file = JPATH_ADMINISTRATOR . '/components/com_easyblog/includes/easyblog.php';

		if (!JFile::exists($file)) {
			return false;
		}

		// Include foundry framework
		require_once($file);
	}

	/**
	 * Convert utf8mb4 to utf8
	 *
	 * @since	5.2.0
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
	 * @since	5.2.0
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
	 * @since	5.2.0
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
	 * @since	5.2.0
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
	 * @since	5.2.0
	 * @access	public
	 */
	public function isQueries($file)
	{
		if (stristr($file, 'administrator/components/com_easyblog/queries') !== false) {
			return true;
		}

		return false;
	}

	/**
	 * Retrieves the currently installed EasyBlog script version
	 *
	 * @since	5.2.0
	 * @access	public
	 */
	public function getScriptVersion()
	{
		$this->engine();

		$table = EB::table('Configs');
		$exists = $table->load(array('name' => 'scriptversion'));

		if ($exists) {
			return $table->params;
		}

		return false;
	}

	/**
	 * Retrieves the currently installed EasyBlog database version
	 *
	 * @since	5.2.0
	 * @access	public
	 */
	public function getDatabaseVersion()
	{
		$this->engine();

		$table = EB::table('Configs');
		$exists = $table->load(array('name' => 'dbversion'));

		if ($exists) {
			return $table->params;
		}

		return false;
	}

	/**
	 * Reads the JSON metadata file
	 *
	 * @since	5.2.0
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
	 * @since	5.2.0
	 * @access	public
	 */
	public function getSource($file)
	{
		return $this->path . '/archive/' . $file;
	}

	/**
	 * Gets the file destination path
	 *
	 * @since	5.2.0
	 * @access	public
	 */
	public function getDestination($file)
	{
		return JPATH_ROOT . '/' . $file;
	}

	/**
	 * Triggered before the installation is complete
	 *
	 * @since	5.2.0
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
	 * @since	5.2.0
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

		// this should run at the last.
		$this->runPostInstall();
	}

	/**
	 * run post installation process here.
	 *
	 * @since	5.3.0
	 * @access	public
	 */
	public function runPostInstall()
	{
		// we need to make sure any new blocks will be added into system.
		$this->installBlocks();

		// Install action logs
		$this->installActionLogs();

		// check if there are any new acls or not.
		$this->updateACL();

		// manual fix on releases
		$this->runReleasesMaintenance();
	}

	/**
	 * Inserts necessary data for action logs
	 *
	 * @since	5.3.0
	 * @access	public
	 */
	public function runReleasesMaintenance()
	{
		// add here if we need to run any 'extra' fix for a release.
	}

	/**
	 * Inserts necessary data for action logs
	 *
	 * @since	5.3.0
	 * @access	public
	 */
	public function installActionLogs()
	{
		$version = explode('.', JVERSION);
		$version = $version[0] . '.' . $version[1];

		$hasActionLogs = version_compare('3.9', $version) !== 1;

		if (!$hasActionLogs) {
			return true;
		}

		$db = EB::db();

		$query = 'SELECT COUNT(1) FROM `#__action_logs_extensions` WHERE `extension`=' . $db->quote('com_easyblog');

		$db->setQuery($query);

		$exists = $db->loadResult() > 0;

		// Record already exists
		if ($exists) {
			return true;
		}

		$query = 'INSERT INTO `#__action_logs_extensions` (`extension`) VALUES (' . $db->Quote('com_easyblog') . ')';

		$db->setQuery($query);
		$db->Query();
		
		return true;
	}

	/**
	 * run blocks installation
	 *
	 * @since	5.3.0
	 * @access	private
	 */
	private function installBlocks()
	{
		// Construct to the place where we store all the blocks
		$path = JPATH_ROOT . '/administrator/components/com_easyblog/defaults/blocks';

		// Retrieve the list of files of each blocks
		$files = JFolder::files($path, '.', true, true, array('.svn', 'CVS', '.DS_Store', '__MACOSX', 'index.html'));

		if ($files) {
			foreach ($files as $file) {
				$block = json_decode(file_get_contents($file));

				// If for whatever reason the contents cannot be decoded, we should not allow it to continue.
				if (!$block) {
					continue;
				}

				$table = EB::table('Block');
				$table->load(array('element' => $block->element));

				// Check for previous publishing state for existing block.
				if ($table->id) {
					$block->published = $table->published;
				}

				$table->bind($block);

				// Save the block
				$table->store();
			}
		}
	}

	/**
	 * Update the ACL for EasyBlog
	 *
	 * @since	5.3.0
	 * @access	public
	 */
	private function updateACL()
	{
		$db = EB::db();

		// Intelligent fix to delete all records from the #__easyblog_acl_group when it contains ridiculous amount of entries
		$query = 'SELECT COUNT(1) FROM ' . $db->nameQuote('#__easyblog_acl_group');
		$db->setQuery($query);

		$total = $db->loadResult();

		if ($total > 20000) {
			$query = 'DELETE FROM ' . $db->nameQuote('#__easyblog_acl_group');
			$db->setQuery($query);
			$db->Query();
		}

		// First, remove all records from the acl table.
		$query = 'DELETE FROM ' . $db->nameQuote('#__easyblog_acl');
		$db->setQuery($query);
		$db->Query();

		// Get the list of acl
		$contents = file_get_contents(JPATH_ROOT . '/administrator/components/com_easyblog/defaults/acl.json');
		$acls = json_decode($contents);

		foreach ($acls as $acl) {

			$query = array();
			$query[] = 'INSERT INTO ' . $db->qn('#__easyblog_acl') . '(' . $db->qn('id') . ',' . $db->qn('action') . ',' . $db->qn('group') . ',' . $db->qn('description') . ',' . $db->qn('published') . ')';
			$query[] = 'VALUES(' . $db->Quote($acl->id) . ',' . $db->Quote($acl->action) . ',' . $db->Quote($acl->group) . ',' . $db->Quote($acl->desc) . ',' . $db->Quote($acl->published) . ')';
			$query = implode(' ', $query);

			$db->setQuery($query);
			$db->Query();
		}

		// Once the acl is initialized, we need to create default values for all the existing groups on the site.
		$this->assignACL();

	}

	/**
	 * Assign acl rules to existing Joomla groups
	 *
	 * @since	5.3.0
	 * @access	public
	 */
	private function assignACL()
	{
		// Get the db
		$db = EB::db();

		// Retrieve all user groups from the site
		$query = array();
		$query[] = 'SELECT a.' . $db->qn('id') . ', a.' . $db->qn('title') . ' AS ' . $db->qn('name') . ', COUNT(DISTINCT b.' . $db->qn('id') . ') AS ' . $db->qn('level');
		$query[] = ', GROUP_CONCAT(b.' . $db->qn('id') . ' SEPARATOR \',\') AS ' . $db->qn('parents');
		$query[] = 'FROM ' . $db->qn('#__usergroups') . ' AS a';
		$query[] = 'LEFT JOIN ' . $db->qn('#__usergroups') . ' AS b';
		$query[] = 'ON a.' . $db->qn('lft') . ' > b.'  . $db->qn('lft');
		$query[] = 'AND a.' . $db->qn('rgt') . ' < b.' . $db->qn('rgt');
		$query[] = 'GROUP BY a.' . $db->qn('id');
		$query[] = 'ORDER BY a.' . $db->qn('lft') . ' ASC';

		$query = implode(' ', $query);
		$db->setQuery($query);

		// Default values
		$groups = array();
		$result = $db->loadColumn();

		// Get a list of default acls
		$query = array();
		$query[] = 'SELECT ' . $db->qn('id') . ' FROM ' . $db->qn('#__easyblog_acl');
		$query[] = 'ORDER BY ' . $db->qn('id') . ' ASC';

		$query = implode(' ', $query);
		$db->setQuery($query);

		// Get those acls
		$installedAcls = $db->loadColumn();

		// Default admin groups
		$adminGroups = array(7, 8);

		if (!empty($result)) {

			foreach ($result as $id) {

				$id = (int) $id;

				// Every other group except admins and super admins should only have restricted access
				if (in_array($id, $adminGroups)) {
					$groups[$id] = $installedAcls;
				} else {

					$allowedAcl = array();

					// Default guest / public group
					if ($id == 1 || $id == 9) {
						$allowedAcl = array(18, 19, 37, 39);
					} else {
						// other groups
						$allowedAcl = array(1, 3, 4, 6, 8, 10, 11, 12, 13, 14, 15, 16 ,17, 18, 19, 21, 23, 24, 25, 27, 28, 30, 33, 34, 35, 36 , 37, 39, 40, 41, 42, 46, 48);
					}

					$groups[$id] = $allowedAcl;
				}
			}
		}

		// Insert default filter for all groups.
		$tagFilter = 'script,applet,iframe';
		$attrFilter = 'onclick,onblur,onchange,onfocus,onreset,onselect,onsubmit,onabort,onkeydown,onkeypress,onkeyup,onmouseover,onmouseout,ondblclick,onmousemove,onmousedown,onmouseup,onerror,onload,onunload';

		// Go through each groups now
		foreach ($groups as $groupId => $acls) {

			$query = array();
			$query[] = 'SELECT COUNT(1) FROM ' . $db->qn('#__easyblog_acl_filters');
			$query[] = 'WHERE ' . $db->qn('content_id') . '=' . $db->Quote($groupId);
			$query = implode(' ', $query);

			$db->setQuery($query);
			$filterExists = $db->loadResult() > 0 ? true : false;

			// If the filters doesn't exist, insert them
			if (!$filterExists) {

				$filter = EB::table('ACLFilter');
				$filter->content_id = $groupId;
				$filter->disallow_tags = in_array($groupId, $adminGroups) ? '' : $tagFilter;
				$filter->disallow_attributes = in_array($groupId, $adminGroups) ? '' : $attrFilter;

				$filter->store();
			}

			// Now we need to insert the acl rules
			$query = array();
			$insertQuery = array();
			$query[] = 'SELECT COUNT(1) FROM ' . $db->qn('#__easyblog_acl_group');
			$query[] = 'WHERE ' . $db->qn('content_id') . '=' . $db->Quote($groupId);
			$query[] = 'AND ' . $db->qn('type') . '=' . $db->Quote('group');

			$query = implode(' ', $query);

			$db->setQuery($query);
			$exists = $db->loadResult() > 0 ? true : false;

			// Reinitialize the query again.
			$query = 'INSERT INTO ' . $db->qn('#__easyblog_acl_group') . ' (' . $db->qn('content_id') . ',' . $db->qn('acl_id') . ',' . $db->qn('status') . ',' . $db->qn('type') . ') VALUES';

			if (!$exists) {

				foreach ($acls as $acl) {
					$insertQuery[] = '(' . $db->Quote($groupId) . ',' . $db->Quote($acl) . ',' . $db->Quote('1') . ',' . $db->Quote('group') . ')';
				}

				//now we need to get the unassigend acl and set it to '0';
				$disabledACLs = array_diff($installedAcls, $acls);

				if ($disabledACLs) {
					foreach ($disabledACLs as $disabledAcl) {
						$insertQuery[] = '(' . $db->Quote($groupId) . ',' . $db->Quote($disabledAcl) . ',' . $db->Quote('0') . ',' . $db->Quote('group') . ')';
					}
				}

			} else {

				// Get a list of acl that is already associated with the group
				$sub = array();
				$sub[] = 'SELECT ' . $db->qn('acl_id') . ' FROM ' . $db->qn('#__easyblog_acl_group');
				$sub[] = 'WHERE ' . $db->qn('content_id') . '=' . $db->Quote($groupId);
				$sub[] = 'AND ' . $db->qn('type') . '=' . $db->Quote('group');

				$sub = implode(' ', $sub);
				$db->setQuery($sub);

				$existingGroupAcl = $db->loadColumn();

				// Perform a diff to see which acl rules are missing
				$diff = array_diff($installedAcls, $existingGroupAcl);

				// If there's a difference,
				if ($diff) {
					foreach ($diff as $aclId) {

						$value = 0;

						if (in_array($aclId, $acls)) {
							$value = 1;
						}

						$insertQuery[] = '(' . $db->Quote($groupId) . ',' . $db->Quote($aclId) . ',' . $db->Quote($value) . ',' . $db->Quote('group') . ')';
					}
				}
			}

			// Only run this when there is something to insert
			if ($insertQuery) {
				$insertQuery = implode(',', $insertQuery);
				$query .= $insertQuery;

				$db->setQuery($query);
				$db->Query();
			}
		}
	}

	/**
	 * Updates the database version in EasyBlog
	 *
	 * @since	5.2.0
	 * @access	public
	 */
	public function updateVersion($type = 'dbversion', $version)
	{
		$config = EB::table('Configs');
		$config->load(array('name' => $type));
		$config->name = $type;
		$config->params = $version;

		// Save the configuration
		$config->store($config->name);
	}

	/**
	 * Process meta file
	 *
	 * @since	5.2.0
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
	 * @since	5.2.0
	 * @access	public
	 */
	public function processAddedFiles($files)
	{
		return $this->processModifiedFiles($files);
	}

	/**
	 * Process deleted files
	 *
	 * @since	5.2.0
	 * @access	public
	 */
	public function processDeletedFiles($files)
	{
		foreach ($files as $file) {
			$path = rtrim(JPATH_ROOT, '/') . '/' . $file;
			$exists = JFile::exists($path);

			if ($exists) {
				JFile::delete($path);
			}
		}

		return true;
	}

	/**
	 * Process modified files
	 *
	 * @since	5.2.0
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
	 * @since	5.2.0
	 * @access	public
	 */
	public function processMaintenanceFiles($files)
	{
		// Get current DB version
		$version = $this->getDatabaseVersion();
		$updates = array();
		$db = EB::db();

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
				$updatesFolder = JPATH_ADMINISTRATOR . '/components/com_easyblog/updates/' . $folderVersion;
				$exists = JFolder::exists($updatesFolder);

				if (!$exists) {
					JFolder::create($updatesFolder);
				}

				// Copy the php files into the respective location
				$dest = $updatesFolder . '/' . $fileName;

				if (!JFile::exists($dest)) {
					JFile::copy($filePath, $dest);
				}

				$maintenance = EB::maintenance();
				$maintenance->runScript($dest);
			}
		}
	}

	/**
	 * Process precompiled css and script files
	 *
	 * @since	5.2.0
	 * @access	public
	 */
	public function processPrecompiledFiles()
	{
		$scripts = $this->path . '/archive/precompiled/scripts';

		// 1. Copy script files first
		$files = JFolder::files($scripts, '.', false, true);

		foreach ($files as $script) {
			$dest = JPATH_ROOT . '/media/com_easyblog/scripts/' . basename($script);

			JFile::copy($script, $dest);
		}

		// 2. Copy admin css file
		$adminFolder = $this->path . '/archive/precompiled/stylesheets/admin/default';
		$files = JFolder::files($adminFolder);
		$target = JPATH_ROOT . '/administrator/components/com_easyblog/themes/default';
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
			$target = JPATH_ROOT . '/components/com_easyblog/themes/' . $theme;
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
	 * @since	5.2.0
	 * @access	public
	 */
	public function processJSON($filePath)
	{
		$db = EB::db();
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
	 * @since	5.2.0
	 * @access	public
	 */
	public function processRenamedFiles($files)
	{
		foreach ($files as $file) {
			// old file
			$source = $this->getDestination($file->old);

			// renamed file
			$dest = $this->getDestination($file->new);
			$source2cp = $this->getSource($file->new);

			// Check if the source already removed
			$sourceExists = JFile::exists($source);

			if (!$sourceExists) {
				continue;
			}

			// we need to do delete and copy operation just incase
			// if the content of the file being updated.
			$sourceExists = JFile::exists($source2cp);
			if ($sourceExists) {
				// check if the dest folder exists or not.
				$folder = dirname($dest);
				if (!JFolder::exists($folder)) {
					JFolder::create($folder);
				}

				// copy file
				JFile::copy($source2cp, $dest);

				// now delete the old file
				JFile::delete($source);

				continue;
			}

			// If old file exists, move it
			// fall back to move method
			JFile::move($source, $dest);
		}

		return true;
	}

	/**
	 * Runs SQL queries based on the files
	 *
	 * @since	5.2.0
	 * @access	public
	 */
	public function runQueries($file)
	{
		$this->engine();

		$db = EB::db();

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
}
