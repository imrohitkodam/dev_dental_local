<?php

declare(strict_types=1);

/*
 * @package     Perfect Publisher
 *
 * @author      Extly, CB. <team@extly.com>
 * @copyright   Copyright (c)2012-2025 Extly, CB. All rights reserved.
 * @license     https://www.gnu.org/licenses/gpl-3.0.html GNU/GPL
 *
 * @see         https://www.extly.com
 */

class XTJoomlaCompatibility
{
    public function getSiteName(): string
    {
        if (class_exists('\Joomla\CMS\Factory')) {
            return Joomla\CMS\Factory::getConfig()->get('sitename');
        }

        return JFactory::getConfig()->get('sitename');
    }

    public function getText(string $key): string
    {
        if (class_exists('\Joomla\CMS\Language\Text')) {
            return Joomla\CMS\Language\Text::_($key);
        }

        return JText::_($key);
    }

    public function getLogLevel(string $level): int
    {
        if (class_exists('\Joomla\CMS\Log\Log')) {
            return constant('\Joomla\CMS\Log\Log::'.$level);
        }

        return constant('\JLog::'.$level);
    }

    public function enqueueMessage(string $message, string $type): void
    {
        if (class_exists('\Joomla\CMS\Factory')) {
            Joomla\CMS\Factory::getApplication()->enqueueMessage($message, $type);
        } else {
            JFactory::getApplication()->enqueueMessage($message, $type);
        }
    }
}
