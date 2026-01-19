<?php

/*
 * @package     XT Transitional Package from FrameworkOnFramework
 *
 * @author      Extly, CB. <team@extly.com>
 * @copyright   Copyright (c)2012-2024 Extly, CB. All rights reserved.
 *              Based on Akeeba's FrameworkOnFramework
 * @license     https://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
 *
 * @see         https://www.extly.com
 */

defined('XTF0F_INCLUDED') || exit;

/**
 * Interface for HAL document renderers
 *
 * @since    2.1
 */
interface XTF0FHalRenderInterface
{
    /**
     * Render a HAL document into a representation suitable for consumption.
     *
     * @param array $options Renderer-specific options
     *
     * @return void
     */
    public function render($options = []);
}
