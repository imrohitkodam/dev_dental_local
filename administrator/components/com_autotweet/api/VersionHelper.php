<?php

/*
 * @package     Perfect Publisher
 *
 * @author      Extly, CB. <team@extly.com>
 * @copyright   Copyright (c)2012-2025 Extly, CB. All rights reserved.
 * @license     https://www.gnu.org/licenses/gpl-3.0.html GNU/GPL
 *
 * @see         https://www.extly.com
 */

defined('_JEXEC') || exit;

define('PERFECT_PUB_PRO', true);
define('PERFECT_PUB_BASIC', false);
define('PERFECT_PUB_FREE', false);

define('JED_ID', 26508);

/**
 * VersionHelper class.
 *
 * @since       1.0
 */
abstract class VersionHelper
{
    public static function initialize()
    {
    }

    /**
     * getLogo.
     *
     * @return string
     */
    public static function getLogo()
    {
        $logo = \Joomla\CMS\Uri\Uri::root().'media/com_autotweet/images/'.(PERFECT_PUB_PRO ? 'perfectpub-logo.svg' : 'perfectpub-logo.svg');

        return $logo;
    }

    /**
     * getTitle.
     *
     * @param string $title Title+
     *
     * @return string
     */
    public static function getTitle($title)
    {
        $name = self::getFlavourName();

        return $name.' - '.$title;
    }

    /**
     * getFlavourName.
     */
    public static function getFlavourName()
    {
        return JText::_('COM_AUTOTWEET_NAME').' '.self::getFlavour();
    }

    /**
     * getFlavour.
     */
    public static function getFlavour()
    {
        if (PERFECT_PUB_PRO) {
            return 'PRO';
        }

        if (PERFECT_PUB_BASIC) {
            return 'Basic';
        }

        if (PERFECT_PUB_FREE) {
            return 'Free';
        }

        return null;
    }

    /**
     * isFreeFlavour.
     */
    public static function isFreeFlavour()
    {
        return 'Free' === self::getFlavour();
    }

    /**
     * getUpdatesSite.
     */
    public static function getUpdatesSite()
    {
        if (PERFECT_PUB_PRO) {
            return 'http://cdn.extly.com/update-perfect-publisher-pro.xml';
        }

        if (PERFECT_PUB_BASIC) {
            return 'http://cdn.extly.com/update-perfect-publisher-basic.xml';
        }

        if (PERFECT_PUB_FREE) {
            return 'http://cdn.extly.com/update-perfect-publisher-free.xml';
        }

        return null;
    }
}
