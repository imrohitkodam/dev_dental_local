<?php

/*
 * @package     XT Transitional Package from FrameworkOnFramework
 *
 * @author      Extly, CB. <team@extly.com>
 * @copyright   Copyright (c)2012-2024 Extly, CB. All rights reserved.
 *              Based on Akeeba's FrameworkOnFramework
 * @license     https://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
 *
 * @see         https://www.extly.com
 */

// Protect from unauthorized access
defined('XTF0F_INCLUDED') || exit;

class XTF0FIntegrationJoomlaFilesystem extends XTF0FPlatformFilesystem implements XTF0FPlatformFilesystemInterface
{
    public function __construct()
    {
        if (class_exists('JLoader')) {
            JLoader::import('joomla.filesystem.path');
            JLoader::import('joomla.filesystem.folder');
            JLoader::import('joomla.filesystem.file');
        }
    }

    /**
     * Does the file exists?
     *
     * @param $path string   Path to the file to test
     *
     * @return bool
     */
    public function fileExists($path)
    {
        return file_exists($path);
    }

    /**
     * Delete a file or array of files
     *
     * @param mixed $file The file name or an array of file names
     *
     * @return bool True on success
     */
    public function fileDelete($file)
    {
        return unlink($file);
    }

    /**
     * Copies a file
     *
     * @param string $src  The path to the source file
     * @param string $dest The path to the destination file
     *
     * @return bool True on success
     */
    public function fileCopy($src, $dest)
    {
        return copy($src, $dest);
    }

    /**
     * Write contents to a file
     *
     * @param string $file    The full file path
     * @param string &$buffer The buffer to write
     *
     * @return bool True on success
     */
    public function fileWrite($file, &$buffer)
    {
        return file_put_contents($file, $buffer);
    }

    /**
     * Checks for snooping outside of the file system root.
     *
     * @param string $path a file system path to check
     *
     * @return string a cleaned version of the path or exit on error
     *
     * @throws Exception
     */
    public function pathCheck($path)
    {
        return JPath::check($path);
    }

    /**
     * Function to strip additional / or \ in a path name.
     *
     * @param string $path the path to clean
     * @param string $ds   directory separator (optional)
     *
     * @return string the cleaned path
     *
     * @throws UnexpectedValueException
     */
    public function pathClean($path, $ds = \DIRECTORY_SEPARATOR)
    {
        return JPath::clean($path, $ds);
    }

    /**
     * Searches the directory paths for a given file.
     *
     * @param mixed  $paths An path string or array of path strings to search in
     * @param string $file  the file name to look for
     *
     * @return mixed the full path and file name for the target file, or boolean false if the file is not found in any of the paths
     */
    public function pathFind($paths, $file)
    {
        return JPath::find($paths, $file);
    }

    /**
     * Wrapper for the standard file_exists function
     *
     * @param string $path Folder name relative to installation dir
     *
     * @return bool True if path is a folder
     */
    public function folderExists($path)
    {
        return JFolder::exists($path);
    }

    /**
     * Utility function to read the files in a folder.
     *
     * @param string $path          the path of the folder to read
     * @param string $filter        a filter for file names
     * @param mixed  $recurse       true to recursively search into sub-folders, or an integer to specify the maximum depth
     * @param bool   $full          true to return the full path to the file
     * @param array  $exclude       array with names of files which should not be shown in the result
     * @param array  $excludefilter Array of filter to exclude
     * @param bool   $naturalSort   False for asort, true for natsort
     *
     * @return array files in the given folder
     */
    public function folderFiles($path, $filter = '.', $recurse = false, $full = false, $exclude = ['.svn', 'CVS', '.DS_Store', '__MACOSX'],
                                $excludefilter = ['^\..*', '.*~'], $naturalSort = false)
    {
        return JFolder::files($path, $filter, $recurse, $full, $exclude, $excludefilter, $naturalSort);
    }

    /**
     * Utility function to read the folders in a folder.
     *
     * @param string $path          the path of the folder to read
     * @param string $filter        a filter for folder names
     * @param mixed  $recurse       true to recursively search into sub-folders, or an integer to specify the maximum depth
     * @param bool   $full          true to return the full path to the folders
     * @param array  $exclude       array with names of folders which should not be shown in the result
     * @param array  $excludefilter array with regular expressions matching folders which should not be shown in the result
     *
     * @return array folders in the given folder
     */
    public function folderFolders($path, $filter = '.', $recurse = false, $full = false, $exclude = ['.svn', 'CVS', '.DS_Store', '__MACOSX'],
                                  $excludefilter = ['^\..*'])
    {
        return JFolder::folders($path, $filter, $recurse, $full, $exclude, $excludefilter);
    }

    /**
     * Create a folder -- and all necessary parent folders.
     *
     * @param string $path a path to create from the base path
     * @param int    $mode Directory permissions to set for folders created. 0755 by default.
     *
     * @return bool true if successful
     */
    public function folderCreate($path = '', $mode = 0755)
    {
        return JFolder::create($path, $mode);
    }
}
