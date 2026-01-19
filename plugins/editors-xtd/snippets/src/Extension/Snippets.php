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

namespace RegularLabs\Plugin\EditorButton\Snippets\Extension;

use RegularLabs\Library\Document as RL_Document;
use RegularLabs\Library\Parameters as RL_Parameters;
use RegularLabs\Library\Plugin\EditorButton as RL_EditorButtonPlugin;

defined('_JEXEC') or die;

final class Snippets extends RL_EditorButtonPlugin
{
    protected $main_type       = 'component';
    protected $check_installed = ['component', 'plugin'];
    protected $button_icon     = '<svg viewBox="0 0 24 24" style="fill:none;" width="24" height="24" fill="none" stroke="currentColor">'
    . '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14.121 14.121L19 19m-7-7l7-7m-7 7l-2.879 2.879M12 12L9.121 9.121m0 5.758a3 3 0 10-4.243 4.243 3 3 0 004.243-4.243zm0-5.758a3 3 0 10-4.243-4.243 3 3 0 004.243 4.243z" />'
    . '</svg>';

    protected function loadScripts(): void
    {
        $params = RL_Parameters::getComponent($this->_name);

        RL_Document::scriptOptions([
            'syntax_word'              => $params->tag,
            'tag_characters'           => explode('.', $params->tag_characters),
        ], 'snippets_button');

        RL_Document::script('snippets.button');
    }
}
