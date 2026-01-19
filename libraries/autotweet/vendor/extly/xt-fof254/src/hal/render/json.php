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
 * Implements the HAL over JSON renderer
 *
 * @since    2.1
 */
class XTF0FHalRenderJson implements XTF0FHalRenderInterface
{
    /**
     * The document to render
     *
     * @var XTF0FHalDocument
     */
    protected $_document;

    /**
     * When data is an array we'll output the list of data under this key
     *
     * @var string
     */
    private $_dataKey = '_list';

    /**
     * Public constructor
     *
     * @param XTF0FHalDocument &$document The document to render
     */
    public function __construct(&$document)
    {
        $this->_document = $document;
    }

    /**
     * Render a HAL document in JSON format
     *
     * @param array $options Rendering options. You can currently only set json_options (json_encode options)
     *
     * @return string The JSON representation of the HAL document
     */
    public function render($options = [])
    {
        if (isset($options['data_key'])) {
            $this->_dataKey = $options['data_key'];
        }

        $jsonOptions = $options['json_options'] ?? 0;

        $serialiseThis = new stdClass();

        // Add links
        $collection = $this->_document->getLinks();
        $serialiseThis->_links = new stdClass();

        foreach ($collection as $rel => $links) {
            if (!is_array($links)) {
                $serialiseThis->_links->$rel = $this->_getLink($links);
            } else {
                $serialiseThis->_links->$rel = [];

                foreach ($links as $link) {
                    $serialiseThis->_links->$rel[] = $this->_getLink($link);
                }
            }
        }

        // Add embedded documents

        $collection = $this->_document->getEmbedded();

        if (!empty($collection)) {
            $serialiseThis->_embedded->$rel = new stdClass();

            foreach ($collection as $rel => $embeddeddocs) {
                if (!is_array($embeddeddocs)) {
                    $embeddeddocs = [$embeddeddocs];
                }

                foreach ($embeddeddocs as $embeddeddoc) {
                    $renderer = new self($embeddeddoc);
                    $serialiseThis->_embedded->$rel[] = $renderer->render($options);
                }
            }
        }

        // Add data
        $data = $this->_document->getData();

        if (is_object($data)) {
            $data = $data instanceof XTF0FTable ? $data->getData() : (array) $data;
            if ($data !== []) {
                foreach ($data as $k => $v) {
                    $serialiseThis->$k = $v;
                }
            }
        } elseif (is_array($data)) {
            $serialiseThis->{$this->_dataKey} = $data;
        }

        return json_encode($serialiseThis, $jsonOptions);
    }

    /**
     * Converts a XTF0FHalLink object into a stdClass object which will be used
     * for JSON serialisation
     *
     * @param XTF0FHalLink $xtf0FHalLink The link you want converted
     *
     * @return stdClass The converted link object
     */
    protected function _getLink(XTF0FHalLink $xtf0FHalLink)
    {
        $ret = [
            'href'	=> $xtf0FHalLink->href,
        ];

        if ($xtf0FHalLink->templated) {
            $ret['templated'] = 'true';
        }

        if (!empty($xtf0FHalLink->name)) {
            $ret['name'] = $xtf0FHalLink->name;
        }

        if (!empty($xtf0FHalLink->hreflang)) {
            $ret['hreflang'] = $xtf0FHalLink->hreflang;
        }

        if (!empty($xtf0FHalLink->title)) {
            $ret['title'] = $xtf0FHalLink->title;
        }

        return (object) $ret;
    }
}
