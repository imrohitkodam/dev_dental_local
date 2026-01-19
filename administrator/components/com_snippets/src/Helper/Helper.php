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

namespace RegularLabs\Component\Snippets\Administrator\Helper;

use RegularLabs\Library\DB as RL_DB;

defined('_JEXEC') or die;

class Helper
{
    public static $extension = 'com_snippets';

    /**
     * Determines if the plugin for Snippets to work is enabled.
     *
     * @return    boolean
     */
    public static function isEnabled()
    {
        $db = RL_DB::get();

        $query = $db->getQuery(true)
            ->select($db->quote('enabled'))
            ->from($db->quoteName('#__extensions'))
            ->where($db->quoteName('folder') . ' = ' . $db->quote('system'))
            ->where($db->quoteName('element') . ' = ' . $db->quote('snippets'));

        $db->setQuery($query);

        return (bool) $db->loadResult();
    }
}
