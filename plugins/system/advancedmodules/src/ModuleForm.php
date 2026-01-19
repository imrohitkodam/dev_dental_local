<?php
/**
 * @package         Advanced Module Manager
 * @version         10.4.8
 * 
 * @author          Peter van Westen <info@regularlabs.com>
 * @link            https://regularlabs.com
 * @copyright       Copyright © 2025 Regular Labs All Rights Reserved
 * @license         GNU General Public License version 2 or later
 */

namespace RegularLabs\Plugin\System\AdvancedModules;

defined('_JEXEC') or die;

use Joomla\CMS\Form\Form as JForm;
use RegularLabs\Library\Document as RL_Document;

class ModuleForm
{
    public static function cleanup($form)
    {
        if ( ! ($form instanceof JForm))
        {
            return;
        }

        if ( ! RL_Document::isClient('site'))
        {
            return;
        }

        // Check we are manipulating a valid form.
        if ($form->getName() !== 'com_config.modules')
        {
            return;
        }

        $form->removeField('ordering');
        $form->removeField('publish_up');
        $form->removeField('publish_down');
        $form->removeField('language');
        $form->setFieldAttribute('note', 'label', 'JGLOBAL_DESCRIPTION');
    }
}
