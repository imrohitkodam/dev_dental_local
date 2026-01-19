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

namespace RegularLabs\Component\Conditions\Administrator\Condition\Content;

defined('_JEXEC') or die;

use RegularLabs\Component\Conditions\Administrator\Condition\Condition;
use RegularLabs\Library\Article as RL_Article;

abstract class Content extends Condition
{
    public function getArticle(): ?object
    {
        if ($this->article)
        {
            return $this->article;
        }

        $this->article = RL_Article::get($this->request->id);

        return $this->article;
    }

    protected function isArticle(): bool
    {
        if (empty($this->request->id))
        {
            return false;
        }

        return ($this->request->option == 'com_content' && $this->request->view == 'article')
            || ($this->request->option == 'com_flexicontent' && $this->request->view == 'item');
    }
}
