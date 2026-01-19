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

defined('XTF0F_INCLUDED') || exit();

/**
 * The Interface of an XTF0FConfigDomain class. The methods are used to parse and
 * privision sensible information to consumers. XTF0FConfigProvider acts as an
 * adapter to the XTF0FConfigDomain classes.
 *
 * @since    2.1
 */
interface XTF0FConfigDomainInterface
{
    /**
     * Parse the XML data, adding them to the $ret array
     *
     * @param SimpleXMLElement $xml  The XML data of the component's configuration area
     * @param array            &$ret The parsed data, in the form of a hash array
     *
     * @return void
     */
    public function parseDomain(SimpleXMLElement $xml, array &$ret);

    /**
     * Return a configuration variable
     *
     * @param string &$configuration Configuration variables (hashed array)
     * @param string $var            The variable we want to fetch
     * @param mixed  $default        Default value
     *
     * @return mixed The variable's value
     */
    public function get(&$configuration, $var, $default);
}
