<?php
/**
 * @package         Conditional Content
 * @version         5.5.7
 * 
 * @author          Peter van Westen <info@regularlabs.com>
 * @link            https://regularlabs.com
 * @copyright       Copyright © 2025 Regular Labs All Rights Reserved
 * @license         GNU General Public License version 2 or later
 */

namespace RegularLabs\Plugin\EditorButton\ConditionalContent\Extension;

use RegularLabs\Library\Document as RL_Document;
use RegularLabs\Library\Plugin\EditorButton as RL_EditorButtonPlugin;

defined('_JEXEC') or die;

final class ConditionalContent extends RL_EditorButtonPlugin
{
    protected $button_icon = '<svg viewBox="0 0 24 24" style="fill:none;" width="24" height="24" fill="none" stroke="currentColor">'
    . '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7v8a2 2 0 002 2h6M8 7V5a2 2 0 012-2h4.586a1 1 0 01.707.293l4.414 4.414a1 1 0 01.293.707V15a2 2 0 01-2 2h-2M8 7H6a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2v-2" />'
    . '</svg>';

    protected function loadScripts(): void
    {
        RL_Document::script('conditionalcontent.button');
    }
}
