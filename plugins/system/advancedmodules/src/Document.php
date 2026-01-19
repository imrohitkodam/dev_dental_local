<?php
/**
 * @package         Advanced Module Manager
 * @version         10.4.8
 * 
 * @author          Peter van Westen <info@regularlabs.com>
 * @link            https://regularlabs.com
 * @copyright       Copyright © 2025 Regular Labs All Rights Reserved
 * @license         GNU General Public License version 2 or later
 */

namespace RegularLabs\Plugin\System\AdvancedModules;

defined('_JEXEC') or die;

use Joomla\CMS\Factory as JFactory;
use Joomla\CMS\Language\Text as JText;
use Joomla\CMS\Router\Route as JRoute;
use RegularLabs\Library\Document as RL_Document;
use RegularLabs\Library\Input as RL_Input;
use RegularLabs\Library\Language as RL_Language;
use RegularLabs\Library\RegEx as RL_RegEx;

class Document
{
    public static function removeAssignmentsTabFromMenuItems(&$html)
    {
        if ( ! RL_Document::isAdmin(true)
            || RL_Input::getCmd('option') !== 'com_menus'
            || RL_Input::getCmd('view') !== 'item'
            || RL_Input::getInt('client_id') !== 0
        )
        {
            return;
        }

        $parts = explode('<joomla-tab-element id="modules"', $html, 2);

        if (count($parts) !== 2)
        {
            return;
        }

        $last_parts = explode('</joomla-tab-element>', $parts[1], 2);

        if (count($last_parts) !== 2)
        {
            return;
        }

        $html = $parts[0] . $last_parts[1];
    }

    /**
     * Replace links to com_modules with com_advancedmodules
     */
    public static function replaceLinks(&$string)
    {
        if (
            RL_Document::isClient('administrator')
            && RL_Input::getCmd('option') == 'com_modules'
        )
        {
            self::replaceLinksInCoreModuleManager();

            return;
        }

        // Replace remaining links in modules in admin and frontend
        self::replaceRemainingLinks($string);
    }

    private static function replaceLinksInCoreModuleManager()
    {
        RL_Language::load('com_advancedmodules');

        $body = JFactory::getApplication()->getBody();

        $url = 'index.php?option=com_advancedmodules';

        if (RL_Input::getCmd('view') == 'module')
        {
            $url .= '&task=module.edit&id=' . (int) RL_Input::getInt('id');
        }

        $link = '<a style="float:right;" href="' . JRoute::_($url) . '">' . JText::_('AMM_SWITCH_TO_ADVANCED_MODULE_MANAGER') . '</a><div style="clear:both;"></div>';
        $body = RL_RegEx::replace('(</div>\s*</form>\s*(<\!--.*?-->\s*)*</div>)', $link . '\1', $body);

        JFactory::getApplication()->setBody($body);
    }

    private static function replaceRemainingLinks(&$string)
    {
        if ( ! str_contains($string, 'com_modules'))
        {
            return;
        }

        $string = RL_RegEx::replace(
            '((["\'])[^\s"\'%]*\?option=com_)(modules(\2|[^a-z-].*?\2))',
            '\1advanced\3',
            $string
        );

        $string = str_replace(
            [
                '?option=com_advancedmodules&force=1',
                '?option=com_advancedmodules&amp;force=1',
            ],
            '?option=com_modules',
            $string
        );
    }
}
