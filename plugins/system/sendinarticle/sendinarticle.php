<?php
/**
 * @package	AcyMailing for Joomla!
 * @version	5.13.0
 * @author	acyba.com
 * @copyright	(C) 2009-2023 ACYBA S.A.R.L. All rights reserved.
 * @license	GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 */

defined('_JEXEC') or die('Restricted access');
?><?php

class plgSystemSendinarticle extends JPlugin
{
    function onBeforeRender()
    {
        $app = JFactory::getApplication();
        if (empty($app->input)) return;

        $jversion = preg_replace('#[^0-9\.]#i', '', JVERSION);
        if (version_compare($jversion, '4.0.0', '>=')) {
            if (!$app->isClient('administrator')) return;
        } else {
            if (!$app->isAdmin()) return;
        }
        $input = $app->input;

        if ($input->getCmd('option') === 'com_content' && ($input->getCmd('view', 'article') === 'article' || $input->getCmd('view', 'articles') === 'articles')) {
            include_once rtrim(
                    JPATH_ADMINISTRATOR,
                    DIRECTORY_SEPARATOR
                ).DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_acymailing'.DIRECTORY_SEPARATOR.'helpers'.DIRECTORY_SEPARATOR.'helper.php';
            acymailing_addScript(false, ACYMAILING_JS.'acymailing.js?v='.filemtime(ACYMAILING_MEDIA.'js'.DS.'acymailing.js'));
            acymailing_addStyle(false, ACYMAILING_CSS.'acypopup.css?v='.filemtime(ACYMAILING_MEDIA.'css'.DS.'acypopup.css'));
            acymailing_addStyle(false, ACYMAILING_CSS.'acyicon.css?v='.filemtime(ACYMAILING_MEDIA.'css'.DS.'acyicon.css'));

            acymailing_addScript(
                true,
                '
                    document.addEventListener("DOMContentLoaded", function() {
                        var blockedOnclicks = document.querySelectorAll("[acy-onclick]");
                        for(var i in blockedOnclicks) {
                            if(blockedOnclicks.hasOwnProperty(i)){
                                blockedOnclicks[i].setAttribute("onclick", blockedOnclicks[i].getAttribute("acy-onclick"));
                            }
                        }
                    });
                '
            );
        }
    }

    function onContentAfterSave($context, $article, $isNew)
    {
        if ($context != 'com_content.article') return;
        if (empty($article->id)) return;

        $app = JFactory::getApplication();
        if (empty($app->input)) return;
        $jversion = preg_replace('#[^0-9\.]#i', '', JVERSION);
        if (version_compare($jversion, '4.0.0', '>=')) {
            if (!$app->isClient('administrator')) return;
        } else {
            if (!$app->isAdmin()) return;
        }

        include_once rtrim(
                JPATH_ADMINISTRATOR,
                DIRECTORY_SEPARATOR
            ).DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_acymailing'.DIRECTORY_SEPARATOR.'helpers'.DIRECTORY_SEPARATOR.'helper.php';

        $mailArticle = acymailing_loadResult('SELECT body FROM #__acymailing_mail WHERE type = "article"');
        if (!empty($mailArticle)) {
            preg_match('@{joomlacontent:current@', $mailArticle, $matches);
        }

        if (empty($matches)) return;
        $url = acymailing_baseURI().'index.php?option=com_acymailing&ctrl=email&task=chooseListBeforeSend&tmpl=component&articleId='.$article->id;
        $app->enqueueMessage(
            acymailing_translation_sprintf(
                'ACY_SEND_ARTICLE',
                str_replace(
                    ' onclick=',
                    ' acy-onclick=',
                    acymailing_popup($url, acymailing_translation('ACY_HERE'), '', 600)
                ),
                'message'
            )
        );
    }
}

