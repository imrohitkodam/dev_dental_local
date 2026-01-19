<?php
/**
 * @package         Snippets
 * @version         9.3.8
 * 
 * @author          Peter van Westen <info@regularlabs.com>
 * @link            https://regularlabs.com
 * @copyright       Copyright © 2025 Regular Labs All Rights Reserved
 * @license         GNU General Public License version 2 or later
 */

defined('_JEXEC') or die;

use Joomla\Filesystem\File as JFile;
use Joomla\Filesystem\Folder as JFolder;

class PlgEditorsXtdSnippetsInstallerScript
{
    public function postflight($install_type, $adapter)
    {
        if ( ! in_array($install_type, ['install', 'update']))
        {
            return true;
        }

        self::deleteJoomla3Files();

        return true;
    }

    private static function delete($files = [])
    {
        foreach ($files as $file)
        {
            if (is_dir($file))
            {
                JFolder::delete($file);
            }

            if (is_file($file))
            {
                JFile::delete($file);
            }
        }
    }

    private static function deleteJoomla3Files()
    {
        self::delete(
            [
                JPATH_SITE . '/media/snippets/css',
                JPATH_SITE . '/media/snippets/images',
                JPATH_SITE . '/media/snippets/less',
                JPATH_SITE . '/plugins/editors-xtd/snippets/layouts',
                JPATH_SITE . '/plugins/editors-xtd/snippets/fields.xml',
                JPATH_SITE . '/plugins/editors-xtd/snippets/helper.php',
                JPATH_SITE . '/plugins/editors-xtd/snippets/popup.php',
                JPATH_SITE . '/plugins/editors-xtd/snippets/popup.tmpl.php',
            ]
        );
    }
}
