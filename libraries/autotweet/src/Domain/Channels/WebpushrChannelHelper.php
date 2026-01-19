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

/**
 * WebpushrChannelHelper.
 *
 * @since       1.0
 */
class WebpushrChannelHelper extends ChannelHelper
{
    protected $webpushrClient;

    protected $restApiKey;

    protected $restAuthenticationToken;

    /**
     * ChannelHelper.
     *
     * @param object $channel    params
     * @param string $restApiKey params
     */
    public function __construct($channel, $restApiKey = null, $restAuthenticationToken = null)
    {
        parent::__construct($channel);

        if ($channel->id) {
            $this->restApiKey = $this->channel->params->get('rest_api_key');
            $this->restAuthenticationToken = $this->channel->params->get('rest_authentication_token');
        }

        if ($restApiKey) {
            $this->restApiKey = $restApiKey;
        }

        if ($restAuthenticationToken) {
            $this->restAuthenticationToken = $restAuthenticationToken;
        }
    }

    /**
     * isAuth().
     *
     * @return bool
     */
    public function isAuth()
    {
        if (empty($this->restApiKey)) {
            $this->restApiKey = null;

            return false;
        }

        if (empty($this->restAuthenticationToken)) {
            $this->restAuthenticationToken = null;

            return false;
        }

        try {
            $this->getApiInstance();

            // Avoid Nyholm\Psr7 on Joomla 4
            // $factory = XTS_BUILD\Http\Discovery\MessageFactoryDiscovery::find();
            $guzzleMessageFactory = new XTS_BUILD\Http\Message\MessageFactory\GuzzleMessageFactory();

            $uri = new \XTS_BUILD\GuzzleHttp\Psr7\Uri('https://api.webpushr.com/v1/authentication');
            $request = $guzzleMessageFactory->createRequest(
                'POST',
                $uri,
                $this->getAuthAttributes()
            );

            $response = $this->webpushrClient->sendRequest($request);
            $httpStatusCode = $response->getStatusCode();

            // HTTP_STATUS_UNAUTHORIZED
            return 401 !== $httpStatusCode;
        } catch (Exception $exception) {
            $logger = AutotweetLogger::getInstance();
            $logger->log(\Joomla\CMS\Log\Log::ERROR, $exception->getMessage());

            // Just in case, it is shown someday
            \Joomla\CMS\Factory::getApplication()->enqueueMessage($exception->getMessage(), 'error');
        }

        return false;
    }

    /**
     * sendMessage.
     *
     * @param string $message Param
     * @param object $data    Params
     *
     * @return array
     */
    public function sendMessage($message, $data)
    {
        $instance = AutotweetLogger::getInstance();
        $instance->log(\Joomla\CMS\Log\Log::INFO, 'sendWebpushrMessage', $message);

        $result = [false, 'Webpushr Unknown Error', null];

        try {
            $this->getApiInstance();

            $title = \Joomla\CMS\Factory::getConfig()->get('sitename');

            // https://www.webpushr.com/docs/send-push-to-all-subscribers
            $postVars = [
                'title' => $title,
                'message' => $message,
                'target_url' => $data->org_url,
            ];

            $imageUrl = $data->image_url;

            if (!$this->isMediaModeTextOnlyPost() && !empty($imageUrl)) {
                $postVars['image'] = $imageUrl;
            }

            // Avoid Nyholm\Psr7 on Joomla 4
            // $factory = XTS_BUILD\Http\Discovery\MessageFactoryDiscovery::find();
            $guzzleMessageFactory = new XTS_BUILD\Http\Message\MessageFactory\GuzzleMessageFactory();

            $uri = new \XTS_BUILD\GuzzleHttp\Psr7\Uri('https://api.webpushr.com/v1/notification/send/all');
            $request = $guzzleMessageFactory->createRequest(
                'POST',
                $uri,
                $this->getAuthAttributes(),
                json_encode($postVars)
            );

            $response = $this->webpushrClient->sendRequest($request);

            $httpStatusCode = $response->getStatusCode();
            $body = (string) $response->getBody();
            $data = json_decode($body, true);

            if (200 === (int) $httpStatusCode) {
                $messageId = $data['ID'];

                $result = [
                    true,
                ];
                $result[] = 'OK - '.$messageId;
            } else {
                $result = [
                    false,
                ];
                $result[] = $body;
            }
        } catch (Exception $exception) {
            return [
                false,
                $exception->getMessage(),
            ];
        }

        return $result;
    }

    /**
     * includeHashTags.
     *
     * @return bool
     */
    public function includeHashTags()
    {
        return $this->channel->params->get('hashtags', true);
    }

    /**
     * Internal service functions.
     *
     * @return object
     */
    protected function getApiInstance()
    {
        if (!$this->webpushrClient) {
            $this->webpushrClient = XTS_BUILD\Http\Discovery\HttpClientDiscovery::find();
        }

        return $this->webpushrClient;
    }

    private function getAuthAttributes(): array
    {
        return [
            'webpushrKey' => $this->restApiKey,
            'webpushrAuthToken' => $this->restAuthenticationToken,
            'Content-Type' => 'application/json',
        ];
    }
}
