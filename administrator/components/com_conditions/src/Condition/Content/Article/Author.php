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

namespace RegularLabs\Component\Conditions\Administrator\Condition\Content\Article;

defined('_JEXEC') or die;

use Joomla\CMS\Factory as JFactory;
use Joomla\CMS\User\UserFactoryInterface as JUserFactoryInterface;
use RegularLabs\Component\Conditions\Administrator\Condition\Content\Content;
use RegularLabs\Component\Conditions\Administrator\Condition\HasArraySelection;
use RegularLabs\Library\User as RL_User;

class Author extends Content
{
    use HasArraySelection;

    public function pass(): bool
    {
        if ( ! $this->isArticle())
        {
            return false;
        }

        if (empty($this->selection))
        {
            return false;
        }

        $item = $this->getArticle();

        $author = $item->created_by ?? 0;

        if ($this->passSimple($author))
        {
            return true;
        }

        $author = JFactory::getContainer()->get(JUserFactoryInterface::class)->loadUserById((int) $author);

        if ($this->passSimple($author->get('username')))
        {
            return true;
        }

        if ( ! in_array('current', $this->selection))
        {
            return false;
        }

        return $author == RL_User::getId();
    }
}
