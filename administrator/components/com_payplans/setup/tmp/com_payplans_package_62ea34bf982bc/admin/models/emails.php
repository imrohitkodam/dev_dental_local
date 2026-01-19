<?php
/**
* @package		PayPlans
* @copyright	Copyright (C) Stack Ideas Sdn Bhd. All rights reserved.
* @license		GNU/GPL, see LICENSE.php
* PayPlans is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/
defined('_JEXEC') or die('Unauthorized Access');

PP::import('admin:/includes/model');

class PayPlansModelEmails extends PayPlansModel
{
	// Let the parent know that we are trying to filter by app table
	protected $_name = 'emails';

	public function __construct()
	{
		parent::__construct('emails');
	}

	/**
	 * Retrieves a list of email template files
	 *
	 * @since	4.2.0
	 * @access	public
	 */
	public function getFiles()
	{
		$folder = $this->getFolder();

		// Retrieve the list of files
		$rows = JFolder::files($folder, '.', true, true);
		$files = array();

		foreach ($rows as $row) {

			$row = PP::normalizeSeparator($row);
			$fileName = basename($row);

			$disallowed = array(
				'\.orig',
				'\.mjml',
				'\.html',
				'blank\.php',
			);

			$disallowedPattern = implode('|', $disallowed);
			preg_match('/' . $disallowedPattern . '/', $fileName, $match);

			if ($match) {
				continue;
			}

			// Get the file object
			$file = $this->getTemplate($row);

			$files[] = $file;
		}

		return $files;
	}

	/**
	 * Generates the path to an email template
	 *
	 * @since	4.2.0
	 * @access	public
	 */
	public function getFolder()
	{
		$folder = PP_THEMES . '/wireframe/emails';
		$folder = PP::normalizeSeparator($folder);

		return $folder;
	}

	/**
	 * Generates the path to the overriden folder
	 *
	 * @since	4.2.0
	 * @access	public
	 */
	public function getOverrideFolder($file)
	{
		$currentTemplate = PP::getJoomlaTemplate();
		$path = JPATH_ROOT . '/templates/' . $currentTemplate . '/html/com_payplans/emails/' . ltrim($file, '/');

		return $path;
	}

	/**
	 * Retrieves a list of email templates
	 *
	 * @since	4.2.0
	 * @access	public
	 */
	public function getTemplate($absolutePath, $contents = false)
	{
		$file = new stdClass();
		$file->name = basename($absolutePath);
		$file->path = $absolutePath;

		$file->relative = str_ireplace($this->getFolder(), '', $file->path);

		// trim the first slash from the relative path for some of the case which doesn't have the subfolder like template.php
		$relativePath = ltrim($file->relative, '/');
		$descPrefix = '';

		// Ensure if there got subfolder then only process this
		if (strrpos($relativePath, '/') !== false) {

			$descPrefix = explode('/', $relativePath);

			array_walk($descPrefix, function($value, $key) {
				$value = strtoupper($value);
			});

			// Get the subfolder name here for append the language constants
			$descPrefix = '_' . $descPrefix[0];
		}

		$file->desc = str_ireplace('.php', '', $file->name);
		$file->desc = strtoupper(str_ireplace(array('.', '-'), '_', $file->desc));
		$file->desc = JText::_('COM_PP_EMAILS' . $descPrefix . '_' . $file->desc);

		// Determine if the email template file has already been overriden.
		$overridePath = $this->getOverrideFolder($file->relative);

		$file->override = JFile::exists($overridePath);
		$file->overridePath = $overridePath;
		$file->contents = '';

		if ($contents) {
			if ($file->override) {
				$file->contents = file_get_contents($file->overridePath);
			} else {
				$file->contents = file_get_contents($file->path);
			}
		}

		$structureFiles = array(
			'/template.php',
			'/structure/button.php',
			'/structure/header.php'
		);

		$file->structure = false;

		if (in_array($file->relative, $structureFiles)) {
			$file->structure = true;
		}
		return $file;
	}
}