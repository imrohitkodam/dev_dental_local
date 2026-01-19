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
 * A download adapter using the cURL PHP module
 */
class XTF0FDownloadAdapterCurl extends XTF0FDownloadAdapterAbstract implements XTF0FDownloadInterface
{
    protected $headers = [];

    public function __construct()
    {
        $this->priority = 110;
        $this->supportsFileSize = true;
        $this->supportsChunkDownload = true;
        $this->name = 'curl';
        $this->isSupported = function_exists('curl_init') && function_exists('curl_exec') && function_exists('curl_close');
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
        $curlHandle = curl_init();

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

        // Default cURL options
        $options = [
            \CURLOPT_AUTOREFERER     => 1,
            \CURLOPT_SSL_VERIFYPEER  => 1,
            \CURLOPT_SSL_VERIFYHOST  => 2,
            \CURLOPT_SSLVERSION      => 0,
            \CURLOPT_AUTOREFERER     => 1,
            \CURLOPT_URL             => $url,
            \CURLOPT_BINARYTRANSFER  => 1,
            \CURLOPT_RETURNTRANSFER  => 1,
            \CURLOPT_FOLLOWLOCATION  => 1,
            \CURLOPT_CAINFO          => __DIR__.'/cacert.pem',
            \CURLOPT_HEADERFUNCTION  => [$this, 'reponseHeaderCallback'],
        ];

        if (!(empty($from) && empty($to))) {
            $options[\CURLOPT_RANGE] = sprintf('%d-%d', $from, $to);
        }

        // Add any additional options: Since they are numeric, we must use the array operator. If the jey exists in both
        // arrays, only the first one will be used while the second one will be ignored
        $options = $params + $options;

        @curl_setopt_array($curlHandle, $options);

        $this->headers = [];

        $result = curl_exec($curlHandle);

        $errno = curl_errno($curlHandle);
        $errmsg = curl_error($curlHandle);
        $http_status = curl_getinfo($curlHandle, \CURLINFO_HTTP_CODE);

        if (false === $result) {
            $error = JText::sprintf('LIB_FOF_DOWNLOAD_ERR_CURL_ERROR', $errno, $errmsg);
        } elseif (($http_status >= 300) && ($http_status <= 399) && isset($this->headers['Location']) && !empty($this->headers['Location'])) {
            return $this->downloadAndReturn($this->headers['Location'], $from, $to, $params);
        } elseif ($http_status > 399) {
            $result = false;
            $errno = $http_status;
            $error = JText::sprintf('LIB_FOF_DOWNLOAD_ERR_HTTPERROR', $http_status);
        }

        curl_close($curlHandle);

        if (false === $result) {
            throw new Exception($error, $errno);
        } else {
            return $result;
        }
    }

    /**
     * Get the size of a remote file in bytes
     *
     * @param string $url The remote file's URL
     *
     * @return int The file size, or -1 if the remote server doesn't support this feature
     */
    public function getFileSize($url)
    {
        $result = -1;

        $curlHandle = curl_init();

        curl_setopt($curlHandle, \CURLOPT_AUTOREFERER, 1);
        curl_setopt($curlHandle, \CURLOPT_SSL_VERIFYPEER, 1);
        curl_setopt($curlHandle, \CURLOPT_SSL_VERIFYHOST, 2);
        curl_setopt($curlHandle, \CURLOPT_SSLVERSION, 0);

        curl_setopt($curlHandle, \CURLOPT_URL, $url);
        curl_setopt($curlHandle, \CURLOPT_NOBODY, true);
        curl_setopt($curlHandle, \CURLOPT_HEADER, true);
        curl_setopt($curlHandle, \CURLOPT_RETURNTRANSFER, true);
        @curl_setopt($curlHandle, \CURLOPT_FOLLOWLOCATION, true);
        @curl_setopt($curlHandle, \CURLOPT_CAINFO, __DIR__.'/cacert.pem');

        $data = curl_exec($curlHandle);
        curl_close($curlHandle);

        if ($data) {
            $content_length = 'unknown';
            $status = 'unknown';
            $redirection = null;

            if (preg_match("/^HTTP\/1\.[01] (\d\d\d)/", $data, $matches)) {
                $status = (int) $matches[1];
            }

            if (preg_match("/Content-Length: (\d+)/", $data, $matches)) {
                $content_length = (int) $matches[1];
            }

            if (preg_match('/Location: (.*)/', $data, $matches)) {
                $redirection = (int) $matches[1];
            }

            if (200 == $status) {
                $result = $content_length;
            }

            if (($status > 300) && ($status <= 308)) {
                if ($redirection !== null && $redirection !== 0) {
                    return $this->getFileSize($redirection);
                }

                return -1;
            }
        }

        return $result;
    }

    /**
     * Handles the HTTP headers returned by cURL
     *
     * @param resource $ch   cURL resource handle (unused)
     * @param string   $data Each header line, as returned by the server
     *
     * @return int The length of the $data string
     */
    protected function reponseHeaderCallback(&$ch, &$data)
    {
        $strlen = strlen($data);

        if (($strlen) <= 2) {
            return $strlen;
        }

        if ('HTTP' === substr($data, 0, 4)) {
            return $strlen;
        }

        [$header, $value] = explode(': ', trim($data), 2);

        $this->headers[$header] = $value;

        return $strlen;
    }
}
