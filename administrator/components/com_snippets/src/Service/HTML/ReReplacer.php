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

namespace RegularLabs\Component\Snippets\Administrator\Service\HTML;

use InvalidArgumentException;
use Joomla\CMS\Language\Text;
use Joomla\Utilities\ArrayHelper;

defined('_JEXEC') or die;

class Snippets
{
    /**
     * Display the published or unpublished state of an item.
     *
     * @param int     $value     The state value.
     * @param int     $i         The ID of the item.
     * @param boolean $canChange An optional prefix for the task.
     *
     * @return  string
     *
     * @throws  InvalidArgumentException
     *
     */
    public function published($value = 0, $i = null, $canChange = true)
    {
        // Note: $i is required but has to be an optional argument in the function call due to argument order
        if (null === $i)
        {
            throw new InvalidArgumentException('$i is a required argument in JHtmlSnippets::published');
        }

        // Array of image, task, title, action
        $states = [
            1  => ['publish', 'items.unpublish', 'JENABLED', 'COM_SNIPPETS_DISABLE_ITEM'],
            0  => ['unpublish', 'items.publish', 'JDISABLED', 'COM_SNIPPETS_ENABLE_ITEM'],
            2  => ['archive', 'items.unpublish', 'JARCHIVED', 'JUNARCHIVE'],
            -2 => ['trash', 'items.publish', 'JTRASHED', 'COM_SNIPPETS_ENABLE_ITEM'],
        ];

        $state = ArrayHelper::getValue($states, (int) $value, $states[0]);
        $icon  = $state[0];

        if ($canChange)
        {
            $html = '<a href="#" onclick="return Joomla.listItemTask(\'cb' . $i . '\',\'' . $state[1] . '\')" class="tbody-icon'
                . ($value == 1 ? ' active' : '') . '" title="' . Text::_($state[3])
                . '"><span class="icon-' . $icon . '" aria-hidden="true"></span></a>';
        }

        return $html;
    }
}
