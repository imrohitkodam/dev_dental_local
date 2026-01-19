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
 * Implementation of the Hypertext Application Language link in PHP.
 *
 * @since    2.1
 */
class XTF0FHalLink
{
    /**
     * For indicating the target URI. Corresponds with the ’Target IRI’ as
     * defined in Web Linking (RFC 5988). This attribute MAY contain a URI
     * Template (RFC6570) and in which case, SHOULD be complemented by an
     * additional templated attribtue on the link with a boolean value true.
     *
     * @var string
     */
    protected $_href = '';

    /**
     * This attribute SHOULD be present with a boolean value of true when the
     * href of the link contains a URI Template (RFC6570).
     *
     * @var bool
     */
    protected $_templated = false;

    /**
     * For distinguishing between Resource and Link elements that share the
     * same relation
     *
     * @var string
     */
    protected $_name = null;

    /**
     * For indicating what the language of the result of dereferencing the link should be.
     *
     * @var string
     */
    protected $_hreflang = null;

    /**
     * For labeling the destination of a link with a human-readable identifier.
     *
     * @var string
     */
    protected $_title = null;

    /**
     * Public constructor of a XTF0FHalLink object
     *
     * @param string $href      See $this->_href
     * @param bool   $templated See $this->_templated
     * @param string $name      See $this->_name
     * @param string $hreflang  See $this->_hreflang
     * @param string $title     See $this->_title
     *
     * @throws RuntimeException If $href is empty
     */
    public function __construct($href, $templated = false, $name = null, $hreflang = null, $title = null)
    {
        if (empty($href)) {
            throw new RuntimeException('A HAL link must always have a non-empty href');
        }

        $this->_href = $href;
        $this->_templated = $templated;
        $this->_name = $name;
        $this->_hreflang = $hreflang;
        $this->_title = $title;
    }

    /**
     * Magic getter for the protected properties
     *
     * @param string $name The name of the property to retrieve, sans the underscore
     *
     * @return mixed Null will always be returned if the property doesn't exist
     */
    public function __get($name)
    {
        $property = '_'.$name;

        if (property_exists($this, $property)) {
            return $this->$property;
        } else {
            return null;
        }
    }

    /**
     * Magic setter for the protected properties
     *
     * @param string $name  The name of the property to set, sans the underscore
     * @param mixed  $value The value of the property to set
     *
     * @return void
     */
    public function __set($name, $value)
    {
        if (('href' == $name) && empty($value)) {
            return;
        }

        $property = '_'.$name;

        if (property_exists($this, $property)) {
            $this->$property = $value;
        }
    }

    /**
     * Is this a valid link? Checks the existence of required fields, not their
     * values.
     *
     * @return bool
     */
    public function check()
    {
        return !empty($this->_href);
    }
}
