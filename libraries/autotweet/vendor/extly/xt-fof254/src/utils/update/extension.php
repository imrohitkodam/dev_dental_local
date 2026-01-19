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

// Protect from unauthorized access
defined('XTF0F_INCLUDED') || exit;

/**
 * A helper class to read and parse "extension" update XML files over the web
 */
class XTF0FUtilsUpdateExtension
{
    /**
     * Reads an "extension" XML update source and returns all listed update entries.
     *
     * If you have a "collection" XML update source you should do something like this:
     * $collection = new XTF0FUtilsUpdateCollection();
     * $extensionUpdateURL = $collection->getExtensionUpdateSource($url, 'component', 'com_foobar', JVERSION);
     * $extension = new XTF0FUtilsUpdateExtension();
     * $updates = $extension->getUpdatesFromExtension($extensionUpdateURL);
     *
     * @param string $url The extension XML update source URL to read from
     *
     * @return array An array of update entries
     */
    public function getUpdatesFromExtension($url)
    {
        // Initialise
        $ret = [];

        // Get and parse the XML source
        $xtf0FDownload = new XTF0FDownload();
        $xmlSource = $xtf0FDownload->getFromURL($url);

        try {
            $xml = new SimpleXMLElement($xmlSource, \LIBXML_NONET);
        } catch (Exception $exception) {
            return $ret;
        }

        // Sanity check
        if (('updates' !== $xml->getName())) {
            unset($xml);

            return $ret;
        }

        // Let's populate the list of updates
        /** @var SimpleXMLElement $update */
        foreach ($xml->children() as $update) {
            // Sanity check
            if ('update' !== $update->getName()) {
                continue;
            }

            $entry = [
                'infourl'			=> ['title' => '', 'url' => ''],
                'downloads'			=> [],
                'tags'				=> [],
                'targetplatform'	=> [],
            ];

            $properties = get_object_vars($update);

            foreach ($properties as $nodeName => $nodeContent) {
                switch ($nodeName) {
                    default:
                        $entry[$nodeName] = $nodeContent;
                        break;

                    case 'infourl':
                    case 'downloads':
                    case 'tags':
                    case 'targetplatform':
                        break;
                }
            }

            $infourlNode = $update->xpath('infourl');
            $entry['infourl']['title'] = (string) $infourlNode[0]['title'];
            $entry['infourl']['url'] = (string) $infourlNode[0];

            $downloadNodes = $update->xpath('downloads/downloadurl');
            foreach ($downloadNodes as $downloadNode) {
                $entry['downloads'][] = [
                    'type'		=> (string) $downloadNode['type'],
                    'format'	=> (string) $downloadNode['format'],
                    'url'		=> (string) $downloadNode,
                ];
            }

            $tagNodes = $update->xpath('tags/tag');
            foreach ($tagNodes as $tagNode) {
                $entry['tags'][] = (string) $tagNode;
            }

            /** @var SimpleXMLElement $targetPlatformNode */
            $targetPlatformNode = $update->xpath('targetplatform');

            $entry['targetplatform']['name'] = (string) $targetPlatformNode[0]['name'];
            $entry['targetplatform']['version'] = (string) $targetPlatformNode[0]['version'];
            $client = $targetPlatformNode[0]->xpath('client');
            $entry['targetplatform']['client'] = (is_array($client) && count($client)) ? (string) $client[0] : '';
            $folder = $targetPlatformNode[0]->xpath('folder');
            $entry['targetplatform']['folder'] = is_array($folder) && count($folder) ? (string) $folder[0] : '';

            $ret[] = $entry;
        }

        unset($xml);

        return $ret;
    }
}
