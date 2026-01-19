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

namespace RegularLabs\Component\Snippets\Administrator\Field;

defined('_JEXEC') or die;

use Joomla\CMS\Language\Text as JText;
use Joomla\CMS\Layout\FileLayout as JFileLayout;
use RegularLabs\Library\Document as RL_Document;
use RegularLabs\Library\Form\FormField as RL_FormField;
use RegularLabs\Library\Input as RL_Input;
use RegularLabs\Library\Parameters as RL_Parameters;

class DynamicTagsField extends RL_FormField
{
    protected function getInput()
    {
        return '';
    }

    protected function getLabel()
    {
        $params = RL_Parameters::getComponent('snippets');

        RL_Document::scriptOptions([
            'syntax_word'              => $params->tag,
            'tag_characters'           => explode('.', $params->tag_characters),
        ], 'Snippets');

        RL_Document::script('snippets.button');

        $layout = new JFileLayout(
            'button.dynamic-tags',
            JPATH_COMPONENT_ADMINISTRATOR . '/layouts'
        );

        $url = 'index.php?option=com_snippets&view=item&layout=dynamic_tags&tmpl=component&editor=jform_content'
            . '&id=' . RL_Input::getInt('id');

        return $layout->render([
            'title' => JText::_('RL_DYNAMIC_TAGS'),
            'url'   => $url,
            'icon'  => 'fa fa-bolt',
        ]);
    }
}
