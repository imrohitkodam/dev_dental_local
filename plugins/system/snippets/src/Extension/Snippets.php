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

namespace RegularLabs\Plugin\System\Snippets\Extension;

use RegularLabs\Library\Html as RL_Html;
use RegularLabs\Library\Plugin\System as RL_SystemPlugin;
use RegularLabs\Library\Protect as RL_Protect;
use RegularLabs\Library\StringHelper as RL_String;
use RegularLabs\Plugin\System\Snippets\Params;
use RegularLabs\Plugin\System\Snippets\Protect;
use RegularLabs\Plugin\System\Snippets\Replace;

defined('_JEXEC') or die;

final class Snippets extends RL_SystemPlugin
{
    public $_lang_prefix = 'SNP';

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
        if ( ! RL_String::contains($html, Params::getTags(true)))
        {
            return false;
        }

        [$pre, $body, $post] = RL_Html::getBody($html);

        Replace::replaceTags($body, 'body');
        Replace::replaceTags($pre, 'head');

        $html = $pre . $body . $post;

        return true;
    }

    protected function cleanFinalHtmlOutput(string &$html): void
    {
        $params = Params::get();

        Protect::unprotectTags($html);

        RL_Protect::removeFromHtmlTagContent($html, Params::getTags(true));
        RL_Protect::removeInlineComments($html, 'Snippets');

        if ( ! $params->place_comments)
        {
            RL_Protect::removeCommentTags($html, 'Snippets');
        }
    }

    protected function extraChecks(): bool
    {
        if ( ! is_file(JPATH_ADMINISTRATOR . '/components/com_snippets/snippets.xml'))
        {
            return false;
        }

        return parent::extraChecks();
    }
}
