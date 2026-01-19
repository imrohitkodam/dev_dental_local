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

namespace RegularLabs\Plugin\System\AdvancedModules\Extension;

use Joomla\CMS\Form\Form as JForm;
use RegularLabs\Library\Document as RL_Document;
use RegularLabs\Library\Plugin\System as RL_SystemPlugin;
use RegularLabs\Library\Protect as RL_Protect;
use RegularLabs\Library\RegEx as RL_RegEx;
use RegularLabs\Plugin\System\AdvancedModules\Document;
use RegularLabs\Plugin\System\AdvancedModules\Helper;
use RegularLabs\Plugin\System\AdvancedModules\ModuleForm;
use RegularLabs\Plugin\System\AdvancedModules\Params;

defined('_JEXEC') or die;

final class AdvancedModules extends RL_SystemPlugin
{
    static $_extra_events = [
        'onRenderModule'      => 'onRenderModule',
        'onPrepareModuleList' => 'onPrepareModuleList',
    ];
    public $_can_disable_by_url = false;
    public $_enable_in_admin    = true;
    public $_lang_prefix        = 'AMM';
    public $_page_types         = ['html'];
    public $_title              = 'ADVANCEDMODULEMANAGER';

    public function handleOnPrepareModuleList(?array &$modules): void
    {
        if (RL_Document::isAdmin())
        {
            return;
        }

        Helper::prepareModuleList($modules);
    }

    public function handleOnRenderModule(?object &$module): void
    {
        if (RL_Document::isAdmin())
        {
            return;
        }

        Helper::renderModule($module);
    }

    protected function changeFinalHtmlOutput(string &$html): bool
    {
        Document::removeAssignmentsTabFromMenuItems($html);
        Document::replaceLinks($html);

        return true;
    }

    protected function cleanFinalHtmlOutput(string &$html): void
    {
    }

    protected function extraChecks(): bool
    {
        if ( ! RL_Protect::isComponentInstalled('advancedmodules'))
        {
            return false;
        }

        return parent::extraChecks();
    }

    protected function handleOnContentPrepareForm(JForm $form, object $data): void
    {
        if ( ! RL_Document::isHtml())
        {
            return;
        }

        ModuleForm::cleanup($form);
    }
}
