<?php

/*
 * @package     Perfect Publisher
 *
 * @author      Extly, CB. <team@extly.com>
 * @copyright   Copyright (c)2012-2025 Extly, CB. All rights reserved.
 * @license     https://www.gnu.org/licenses/gpl-3.0.html GNU/GPL
 *
 * @see         https://www.extly.com
 */

defined('_JEXEC') || exit;

/**
 * AutotweetViewPost.
 *
 * @since       1.0
 */
class AutotweetViewPost extends AutoTweetDefaultView
{
    public $item;

    public $perms;

    /**
     * onAdd.
     *
     * @param string $tpl Param
     */
    protected function onAdd($tpl = null)
    {
        $result = parent::onAdd($tpl);

        Extly::loadAwesome();

        $file = EHtml::getRelativeFile('js', 'com_autotweet/post.min.js');

        if ($file) {
            $dependencies = [];
            $dependencies['post'] = ['extlycore'];
            Extly::initApp(CAUTOTWEETNG_VERSION, $file, $dependencies);
        }

        if (!(bool) $this->item->id && isset($this->item->pubstate) && !$this->perms->editstate) {
            $this->item->pubstate = 'approve';
        }

        return $result;
    }
}
