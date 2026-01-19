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
 * Implementation of the Hypertext Application Language document in PHP. It can
 * be used to provide hypermedia in a web service context.
 *
 * @since    2.1
 */
class XTF0FHalDocument
{
    /**
     * The collection of links of this document
     *
     * @var XTF0FHalLinks
     */
    private $xtf0FHalLinks = null;

    /**
     * The data (resource state or collection of resource state objects) of the
     * document.
     *
     * @var array
     */
    private $_data = null;

    /**
     * Embedded documents. This is an array of XTF0FHalDocument instances.
     *
     * @var array
     */
    private $_embedded = [];

    /**
     * When $_data is an array we'll output the list of data under this key
     * (JSON) or tag (XML)
     *
     * @var string
     */
    private $_dataKey = '_list';

    /**
     * Public constructor
     *
     * @param mixed $data The data of the document (usually, the resource state)
     */
    public function __construct($data = null)
    {
        $this->_data = $data;
        $this->xtf0FHalLinks = new XTF0FHalLinks();
    }

    /**
     * Add a link to the document
     *
     * @param string       $rel       The relation of the link to the document.
     *                                See RFC 5988 http://tools.ietf.org/html/rfc5988#section-6.2.2 A document MUST always have
     *                                a "self" link.
     * @param XTF0FHalLink $xtf0FHalLink The actual link object
     * @param bool         $overwrite When false and a link of $rel relation exists, an array of links is created. Otherwise the
     *                                existing link is overwriten with the new one
     *
     * @see XTF0FHalLinks::addLink
     *
     * @return bool True if the link was added to the collection
     */
    public function addLink($rel, XTF0FHalLink $xtf0FHalLink, $overwrite = true)
    {
        return $this->xtf0FHalLinks->addLink($rel, $xtf0FHalLink, $overwrite);
    }

    /**
     * Add links to the document
     *
     * @param string $rel       The relation of the link to the document. See RFC 5988
     * @param array  $links     An array of XTF0FHalLink objects
     * @param bool   $overwrite When false and a link of $rel relation exists, an array of
     *                          links is created. Otherwise the existing link is overwriten
     *                          with the new one
     *
     * @see XTF0FHalLinks::addLinks
     *
     * @return bool
     */
    public function addLinks($rel, array $links, $overwrite = true)
    {
        return $this->xtf0FHalLinks->addLinks($rel, $links, $overwrite);
    }

    /**
     * Add data to the document
     *
     * @param stdClass $data      The data to add
     * @param bool     $overwrite Should I overwrite existing data?
     *
     * @return void
     */
    public function addData($data, $overwrite = true)
    {
        if (is_array($data)) {
            $data = (object) $data;
        }

        if ($overwrite) {
            $this->_data = $data;
        } else {
            if (!is_array($this->_data)) {
                $this->_data = [$this->_data];
            }

            $this->_data[] = $data;
        }
    }

    /**
     * Add an embedded document
     *
     * @param string           $rel       The relation of the embedded document to its container document
     * @param XTF0FHalDocument $document  The document to add
     * @param bool             $overwrite Should I overwrite existing data with the same relation?
     *
     * @return bool
     */
    public function addEmbedded($rel, self $document, $overwrite = true)
    {
        if (!array_key_exists($rel, $this->_embedded) || !$overwrite) {
            $this->_embedded[$rel] = $document;
        } elseif (array_key_exists($rel, $this->_embedded) && !$overwrite) {
            if (!is_array($this->_embedded[$rel])) {
                $this->_embedded[$rel] = [$this->_embedded[$rel]];
            }

            $this->_embedded[$rel][] = $document;
        } else {
            return false;
        }

        return null;
    }

    /**
     * Returns the collection of links of this document
     *
     * @param string $rel The relation of the links to fetch. Skip to get all links.
     *
     * @return array
     */
    public function getLinks($rel = null)
    {
        return $this->xtf0FHalLinks->getLinks($rel);
    }

    /**
     * Returns the collection of embedded documents
     *
     * @param string $rel Optional; the relation to return the embedded documents for
     *
     * @return array|XTF0FHalDocument
     */
    public function getEmbedded($rel = null)
    {
        if (empty($rel)) {
            return $this->_embedded;
        } elseif (isset($this->_embedded[$rel])) {
            return $this->_embedded[$rel];
        } else {
            return [];
        }
    }

    /**
     * Return the data attached to this document
     *
     * @return array|stdClass
     */
    public function getData()
    {
        return $this->_data;
    }

    /**
     * Instantiate and call a suitable renderer class to render this document
     * into the specified format.
     *
     * @param string $format The format to render the document into, e.g. 'json'
     *
     * @return string The rendered document
     *
     * @throws RuntimeException If the format is unknown, i.e. there is no suitable renderer
     */
    public function render($format = 'json')
    {
        $class_name = 'XTF0FHalRender'.ucfirst($format);

        if (!class_exists($class_name, true)) {
            throw new RuntimeException(sprintf("Unsupported HAL Document format '%s'. Render aborted.", $format));
        }

        $renderer = new $class_name($this);

        return $renderer->render(
            [
                'data_key'		=> $this->_dataKey,
            ]
        );
    }
}
