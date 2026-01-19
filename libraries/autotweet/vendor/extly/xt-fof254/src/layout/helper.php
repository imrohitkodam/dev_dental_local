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

/**
 * Helper to render a XTF0FLayout object, storing a base path
 *
 * @since    x.y
 */
class XTF0FLayoutHelper extends JLayoutHelper
{
    /**
     * Method to render the layout.
     *
     * @param string $layoutFile  Dot separated path to the layout file, relative to base path
     * @param object $displayData Object which properties are used inside the layout file to build displayed output
     * @param string $basePath    Base path to use when loading layout files
     *
     * @return string
     */
    public static function render($layoutFile, $displayData = null, $basePath = '')
    {
        $basePath = empty($basePath) ? self::$defaultBasePath : $basePath;

        // Make sure we send null to XTF0FLayoutFile if no path set
        $basePath = empty($basePath) ? null : $basePath;

        $xtf0FLayoutFile = new XTF0FLayoutFile($layoutFile, $basePath);
        $renderedLayout = $xtf0FLayoutFile->render($displayData);

        return $renderedLayout;
    }
}
