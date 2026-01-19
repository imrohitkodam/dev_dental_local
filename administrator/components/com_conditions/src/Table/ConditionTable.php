<?php
/**
 * @package         Conditions
 * @version         25.11.2254
 * 
 * @author          Peter van Westen <info@regularlabs.com>
 * @link            https://regularlabs.com
 * @copyright       Copyright © 2025 Regular Labs All Rights Reserved
 * @license         GNU General Public License version 2 or later
 */

namespace RegularLabs\Component\Conditions\Administrator\Table;

use Exception;
use Joomla\CMS\Table\Table;
use Joomla\Database\DatabaseDriver;

defined('_JEXEC') or die;

class ConditionTable extends Table
{
    public function __construct(DatabaseDriver $db)
    {
        parent::__construct('#__conditions', 'id', $db);
    }

    public function check(): bool
    {
        try
        {
            parent::check();
        }
        catch (Exception $e)
        {
            $this->setError($e->getMessage());

            return false;
        }

        $this->name  = trim($this->name);
        $this->alias = trim($this->alias);

        return true;
    }
}
