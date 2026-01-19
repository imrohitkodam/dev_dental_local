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

use RegularLabs\Component\Conditions\Administrator\Condition\HasArraySelection;

defined('_JEXEC') or die;

class PageType extends Content
{
    use HasArraySelection;

    public function pass(): bool
    {
        $components = ['com_content', 'com_contentsubmit'];

        if ( ! in_array($this->request->option, $components))
        {
            return false;
        }

        $view = $this->request->view;

        if ($this->request->view == 'category' && $this->request->layout == 'blog')
        {
            $view = 'categoryblog';
        }

        return $this->passSimple($view);
    }
}
