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
 * A download adapter using URL fopen() wrappers
 */
class XTF0FDownloadAdapterFopen extends XTF0FDownloadAdapterAbstract implements XTF0FDownloadInterface
{
    public function __construct()
    {
        $this->priority = 100;
        $this->supportsFileSize = false;
        $this->supportsChunkDownload = true;
        $this->name = 'fopen';

        // If we are not allowed to use ini_get, we assume that URL fopen is
        // disabled.
        $this->isSupported = function_exists('ini_get') ? ini_get('allow_url_fopen') : false;
    }

    /**
     * Download a part (or the whole) of a remote URL and return the downloaded
     * data. You are supposed to check the size of the returned data. If it's
     * smaller than what you expected you've reached end of file. If it's empty
     * you have tried reading past EOF. If it's larger than what you expected
     * the server doesn't support chunk downloads.
     *
     * If this class' supportsChunkDownload returns false you should assume
     * that the $from and $to parameters will be ignored.
     *
     * @param string $url    The remote file's URL
     * @param int    $from   Byte range to start downloading from. Use null for start of file.
     * @param int    $to     Byte range to stop downloading. Use null to download the entire file ($from is ignored)
     * @param array  $params Additional params that will be added before performing the download
     *
     * @return string the raw file data retrieved from the remote URL
     *
     * @throws Exception A generic exception is thrown on error
     */
    public function downloadAndReturn($url, $from = null, $to = null, array $params = [])
    {
        if (empty($from)) {
            $from = 0;
        }

        if (empty($to)) {
            $to = 0;
        }

        if ($to < $from) {
            $temp = $to;
            $to = $from;
            $from = $temp;

            unset($temp);
        }

        if (!(empty($from) && empty($to))) {
            $options = [
                'http'	=> [
                    'method'	=> 'GET',
                    'header'	=> "Range: bytes={$from}-{$to}\r\n",
                ],
                'ssl' => [
                    'verify_peer'   => true,
                    'cafile'        => __DIR__.'/cacert.pem',
                    'verify_depth'  => 5,
                ],
            ];

            $options = array_merge($options, $params);

            $context = stream_context_create($options);
            $result = @file_get_contents($url, false, $context, $from - $to + 1);
        } else {
            $options = [
                'http'	=> [
                    'method'	=> 'GET',
                ],
                'ssl' => [
                    'verify_peer'   => true,
                    'cafile'        => __DIR__.'/cacert.pem',
                    'verify_depth'  => 5,
                ],
            ];

            $options = array_merge($options, $params);

            $context = stream_context_create($options);
            $result = @file_get_contents($url, false, $context);
        }

        if (false === $result) {
            $error = JText::sprintf('LIB_FOF_DOWNLOAD_ERR_HTTPERROR');
            throw new Exception($error, 1);
        } else {
            return $result;
        }
    }
}
