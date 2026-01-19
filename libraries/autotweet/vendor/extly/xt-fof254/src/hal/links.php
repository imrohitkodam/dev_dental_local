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
 * Implementation of the Hypertext Application Language links in PHP. This is
 * actually a collection of links.
 *
 * @since    2.1
 */
class XTF0FHalLinks
{
    /**
     * The collection of links, sorted by relation
     *
     * @var array
     */
    private $_links = [];

    /**
     * Add a single link to the links collection
     *
     * @param string       $rel       The relation of the link to the document. See RFC 5988
     *                                http://tools.ietf.org/html/rfc5988#section-6.2.2 A document
     *                                MUST always have a "self" link.
     * @param XTF0FHalLink $xtf0FHalLink The actual link object
     * @param bool         $overwrite When false and a link of $rel relation exists, an array of
     *                                links is created. Otherwise the existing link is overwriten
     *                                with the new one
     *
     * @return bool True if the link was added to the collection
     */
    public function addLink($rel, XTF0FHalLink $xtf0FHalLink, $overwrite = true)
    {
        if (!$xtf0FHalLink->check()) {
            return false;
        }

        if (!array_key_exists($rel, $this->_links) || $overwrite) {
            $this->_links[$rel] = $xtf0FHalLink;
        } elseif (array_key_exists($rel, $this->_links) && !$overwrite) {
            if (!is_array($this->_links[$rel])) {
                $this->_links[$rel] = [$this->_links[$rel]];
            }

            $this->_links[$rel][] = $xtf0FHalLink;
        } else {
            return false;
        }

        return null;
    }

    /**
     * Add multiple links to the links collection
     *
     * @param string $rel       The relation of the links to the document. See RFC 5988.
     * @param array  $links     An array of XTF0FHalLink objects
     * @param bool   $overwrite When false and a link of $rel relation exists, an array
     *                          of links is created. Otherwise the existing link is
     *                          overwriten with the new one
     *
     * @return bool True if the link was added to the collection
     */
    public function addLinks($rel, array $links, $overwrite = true)
    {
        if ($links === []) {
            return false;
        }

        $localOverwrite = $overwrite;

        foreach ($links as $link) {
            if ($link instanceof XTF0FHalLink) {
                $this->addLink($rel, $link, $localOverwrite);
            }

            // After the first time we call this with overwrite on we have to
            // turn it off so that the other links are added to the set instead
            // of overwriting the first item that's already added.
            if ($localOverwrite) {
                $localOverwrite = false;
            }
        }

        return null;
    }

    /**
     * Returns the collection of links
     *
     * @param string $rel Optional; the relation to return the links for
     *
     * @return array|XTF0FHalLink
     */
    public function getLinks($rel = null)
    {
        if (empty($rel)) {
            return $this->_links;
        } elseif (isset($this->_links[$rel])) {
            return $this->_links[$rel];
        } else {
            return [];
        }
    }
}
