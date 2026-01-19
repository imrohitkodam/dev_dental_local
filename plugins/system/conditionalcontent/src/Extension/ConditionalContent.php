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

namespace RegularLabs\Plugin\System\ConditionalContent\Extension;

use RegularLabs\Library\Html as RL_Html;
use RegularLabs\Library\Plugin\System as RL_SystemPlugin;
use RegularLabs\Plugin\System\ConditionalContent\Params;
use RegularLabs\Plugin\System\ConditionalContent\Protect;
use RegularLabs\Plugin\System\ConditionalContent\Replace;

defined('_JEXEC') or die;

final class ConditionalContent extends RL_SystemPlugin
{
    public $_can_disable_by_url = false;
    public $_enable_in_admin    = true;
    public $_lang_prefix        = 'COC';

    public function init(): void
    {
        $params = Params::get();

        $this->_enable_in_admin = $params->enable_admin;
    }

    public function processArticle(
        string &$string,
        string $area = 'article',
        string $context = '',
        mixed  $article = null,
        int    $page = 0
    ): void
    {
        Replace::replaceTags($string, $area, $context, $article);
    }

    protected function changeDocumentBuffer(string &$buffer): bool
    {
        return Replace::replaceTags($buffer, 'component');
    }

    protected function changeFinalHtmlOutput(string &$html): bool
    {
        // only do stuff in body
        [$pre, $body, $post] = RL_Html::getBody($html);
        Replace::replaceTags($body, 'body');
        $html = $pre . $body . $post;

        return true;
    }

    protected function cleanFinalHtmlOutput(string &$html): void
    {
        Protect::unprotectTags($html);
        //RL_Protect::removeInlineComments($html, 'ConditionalContent');
    }

    protected function handleOnAfterRenderModule(object &$module, array &$params): void
    {
        if ( ! isset($module->content))
        {
            return;
        }

        Replace::replaceTags($module->content, 'module');
    }
}
