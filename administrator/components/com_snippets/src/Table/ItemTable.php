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

namespace RegularLabs\Component\Snippets\Administrator\Table;

use Exception;
use Joomla\CMS\Table\Table;
use Joomla\Database\DatabaseDriver;

defined('_JEXEC') or die;

class ItemTable extends Table
{
    /**
     * @param DatabaseDriver $db Database object.
     */
    public function __construct(DatabaseDriver $db)
    {
        parent::__construct('#__snippets', 'id', $db);
    }

    /**
     * @return  boolean
     */
    public function check()
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
