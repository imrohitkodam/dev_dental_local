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

final class BlueskyHelper
{
    private $identifier;

    private $password;

    private $blueskyApi;

    /**
     * @param string $identifier
     * @param string $password
     */
    public function __construct($identifier = '', $password = '')
    {
        $this->identifier = $identifier;
        $this->password = $password;
        $this->blueskyApi = new BlueskyApi();
    }

    public function login()
    {
        $this->blueskyApi->auth($this->identifier, $this->password);
    }

    public function getAccountDid()
    {
        return $this->blueskyApi->getAccountDid();
    }

    public function uploadBlob($imagefile)
    {
        $blob = file_get_contents($imagefile);
        $mimeContentType = mime_content_type($imagefile);

        $response =  $this->blueskyApi->request('POST', 'com.atproto.repo.uploadBlob', [], $blob, $mimeContentType);

        return $response->blob;
    }

    public function createRecord($parameters)
    {
        return $this->blueskyApi->request('POST', 'com.atproto.repo.createRecord', $parameters);
    }

    /**
     * @return bool
     */
    public function verify()
    {
        try {
            $this->blueskyApi->auth($this->identifier, $this->password);

            $message = [
                'status' => true,
                'error_message' => null,
                'user' => $this->blueskyApi->getAccountDid(),
                'url' => 'https://bsky.app/profile/'.$this->identifier,
            ];
        } catch (Exception $exception) {
            $message = [
                'status' => false,
                'error_message' => $exception->getMessage(),
            ];
        }

        return $message;
    }
}
