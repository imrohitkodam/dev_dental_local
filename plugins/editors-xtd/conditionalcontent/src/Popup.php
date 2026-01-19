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

namespace RegularLabs\Plugin\EditorButton\ConditionalContent;

defined('_JEXEC') or die;

use Joomla\CMS\Form\Form as JForm;
use RegularLabs\Library\Document as RL_Document;
use RegularLabs\Library\Input as RL_Input;
use RegularLabs\Library\Plugin\EditorButtonPopup as RL_EditorButtonPopup;
use RegularLabs\Library\RegEx as RL_RegEx;

class Popup extends RL_EditorButtonPopup
{
    protected $extension         = 'conditionalcontent';
    protected $require_core_auth = false;

    protected function loadScripts(): void
    {
        $params = $this->getParams();

        $this->editor_name = RL_Input::getString('editor', 'text');
        // Remove any dangerous character to prevent cross site scripting
        $this->editor_name = RL_RegEx::replace('[\'\";\s]', '', $this->editor_name);

        RL_Document::scriptOptions([
            'tag_show'       => $params->tag_show ?? 'show',
            'tag_hide'       => $params->tag_hide ?? 'hide',
            'tag_characters' => explode('.', $params->tag_characters),
            'editor_name'    => $this->editor_name,
        ], 'conditionalcontent_button');

        RL_Document::script('regularlabs.regular');
        RL_Document::script('regularlabs.admin-form');
        RL_Document::script('regularlabs.admin-form-descriptions');
        RL_Document::script('conditionalcontent.popup');

        $xmlfile = dirname(__FILE__, 2) . '/forms/popup.xml';

        $this->form = new JForm('conditionalcontent');
        $this->form->loadFile($xmlfile, 1, '//config');

        $script = "document.addEventListener('DOMContentLoaded', function(){RegularLabs.ConditionalContentPopup.init()});";
        RL_Document::scriptDeclaration($script, 'Conditional Content Button', true, 'after');
    }
}
