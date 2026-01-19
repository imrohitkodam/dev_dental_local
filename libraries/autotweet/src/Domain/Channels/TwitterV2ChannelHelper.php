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

use XTS_BUILD\GuzzleHttp\Client as HttpClient;

/**
 * TwitterV2ChannelHelper.
 *
 * @since       1.0
 */
class TwitterV2ChannelHelper extends ChannelHelper
{
    public const SCOPE_PERMISSIONS = [
        'tweet.read',
        'tweet.write',
        'users.read',
        'offline.access',
        'media.write',
    ];

    protected $consumerKey;

    protected $consumerSecret;

    protected $accessToken;

    protected $accessTokenSecret;

    protected $clientId;

    protected $clientSecret;


    /**
     * ChannelHelper.
     *
     * @param object $channel params
     */
    public function __construct($channel)
    {
        parent::__construct($channel);

        if ($channel->id) {
            $this->consumerKey = $this->channel->params->get('consumer_key');
            $this->consumerSecret = $this->channel->params->get('consumer_secret');
            $this->accessToken = $this->channel->params->get('access_token');
            $this->accessTokenSecret = $this->channel->params->get('access_token_secret');
            $this->clientId = $this->channel->params->get('client_id');
            $this->clientSecret = $this->channel->params->get('client_secret');
        }
    }

    public function isAuth()
    {
        $bearerToken = $this->channel->params->get('bearer_token');

        if (empty($bearerToken)) {
            return false;
        }

        try {
            $provider = $this->getOAuth2Provider();
            $bearerToken = $this->getBearerToken();

            if (!$bearerToken) {
                return false;
            }

            if ($bearerToken->hasExpired()) {
                $bearerToken = $this->refresh($bearerToken);
            }

            // Test OAuth2
            $twitterUser = $provider->getResourceOwner($bearerToken);
            $userData = $twitterUser->toArray();

            // Test Api v1.1
            $connection = $this->getTwitterApiConnectionV1();
            $response = $connection->get('account/verify_credentials');

            if (200 !== (int) $connection->getLastHttpCode()) {
                if (isset($response->errors)) {
                    $error = array_shift($response->errors);
                    $message = $error->code.' - '.$error->message;
                } else {
                    $message = 'Error '.$connection->getLastHttpCode();
                }

                throw new Exception($message, $error->code);
            }

            $this->channel->setToken($this->channel->id, 'user_data', $userData);
            $this->setChannel($this->channel);

            return $userData;
        } catch (Exception $exception) {
            $this->channel->setToken($this->channel->id, 'bearer_token', null);
            $this->setChannel($this->channel);

            \Joomla\CMS\Factory::getApplication()->enqueueMessage(
                $exception->getMessage(),
                'error'
            );
        }

        return false;
    }

    public function getAuthorizationUrl()
    {
        if (empty($this->clientId) || empty($this->clientSecret)) {
            return false;
        }

        $provider = $this->getOAuth2Provider();
        $authUrl = $provider->getAuthorizationUrl(['scope' => self::SCOPE_PERMISSIONS]);
        $session = \Joomla\CMS\Factory::getSession();
        $session->set('channelId', $this->channel->id);
        $session->set('oauth2state', $provider->getState());
        $session->set('oauth2verifier', $provider->getPkceVerifier());

        return $authUrl;
    }

    public function authenticate($code, $oauth2verifier)
    {
        $provider = $this->getOAuth2Provider();
        $bearerToken = $provider->getAccessToken('authorization_code', [
            'code' => $code,
            'code_verifier' => $oauth2verifier,
        ]);
        $this->channel->setToken($this->channel->id, 'bearer_token', json_encode($bearerToken));
        $this->setChannel($this->channel);

        return $this->isAuth();
    }

    public function refresh($bearerToken)
    {
        // $provider = $this->getOAuth2ProviderToRefreshToken();
        $provider = $this->getOAuth2Provider();
        $bearerToken = $provider->getAccessToken('refresh_token', [
            'refresh_token' => $bearerToken->getRefreshToken(),
        ]);

        $this->channel->setToken($this->channel->id, 'bearer_token', json_encode($bearerToken));
        $this->setChannel($this->channel);

        return $bearerToken;
    }

    public function getSocialUrl($userData)
    {
        return 'https://twitter.com/'.$userData['username'];
    }

    public function getBearerToken()
    {
        $bearerToken = $this->channel->params->get('bearer_token');
        $options = json_decode($bearerToken, true);

        if (!$options) {
            return null;
        }

        return new XTS_BUILD\League\OAuth2\Client\Token\AccessToken($options);
    }

    public function sendMessage($message, $data)
    {
        $imageFile = null;

        try {
            $imageUrl = $data->image_url;

            if (($this->isMediaModeTextOnlyPost()) || (empty($imageUrl))) {
                return $this->publishTweet($message, null);
            }

            $imageFile = ImageUtil::getInstance()->downloadImage($imageUrl);

            return $this->publishTweet(
                $message,
                $imageFile,
                $data
            );
        } catch (Exception $exception) {
            $result = [
                false,
                $exception->getMessage(),
            ];
        }

        if ($imageFile) {
            ImageUtil::getInstance()->releaseImage($imageFile);
        }

        return $result;
    }

    protected function uploadMedia($imagefile, $statusMessage, $data)
    {
        $connection = $this->getTwitterApiConnectionV1();
        $media = $connection->upload(
            'media/upload',
            [
                'media' => $imagefile,
            ]
        );

        if (isset($media->error) || 200 !== $connection->getLastHttpCode()) {
            $message = 'Error '.$connection->getLastHttpCode();

            $message = [
                false,
                $message,
            ];

            return $message;
        }

        $parameters = [];
        $parameters['media']['media_ids'] = [implode(',', [$media->media_id_string])];

        // https://developer.twitter.com/en/docs/twitter-api/v1/media/upload-media/api-reference/post-media-metadata-create
        $metadata = $connection->post(
            'media/metadata/create',
            [
                'media_id' => $media->media_id_string,
                'alt_text' => [
                    'text' => PostHelper::getAltText($statusMessage, $data),
                ],
            ],
            true
        );

        if (isset($metadata->error)) {
            $message = 'Error '.$connection->getLastHttpCode().' - '.$metadata->error;

            $message = [
                false,
                $message,
            ];

            return $message;
        }

        return $parameters;
    }

    private function publishTweet($statusMessage, $imagefile = null, $data = null)
    {
        $instance = AutotweetLogger::getInstance();
        $instance->log(\Joomla\CMS\Log\Log::INFO, 'publishTweet: '.$statusMessage.' - '.$imagefile);

        $parameters = [
            'text' => $statusMessage,
        ];

        if ($imagefile) {
            $instance->log(\Joomla\CMS\Log\Log::INFO, 'publishTweet media/upload '.$imagefile);
            $mediaParameters = $this->uploadMedia($imagefile, $statusMessage, $data);

            if (!isset($mediaParameters['media'])) {
                return $parameters;
            }

            $parameters = array_merge($parameters, $mediaParameters);
        }

        $instance->log(\Joomla\CMS\Log\Log::INFO, 'publishTweet tweets', $parameters);

        $connection = $this->getTwitterApiConnectionV2();

        if (!$connection) {
            return [
                false,
                'Connection failed',
            ];
        }

        $response = $connection->post('tweets', $parameters, true);

        if (201 === (int) $connection->getLastHttpCode()) {
            $userData = $this->channel->params->get('user_data');

            if (is_string($userData)) {
                $userData = json_decode($userData);
            }

            $postUrl = $response->data->id;

            if (isset($userData->username)) {
                $postUrl = 'https://twitter.com/'
                    .$userData->username.'/status/'.$response->data->id;
            }

            return [
                true,
                'OK - '.$postUrl,
            ];
        }

        if (isset($response->errors)) {
            $error = array_shift($response->errors);
            $message = $error->code.' - '.$error->message;
        } else {
            $message = 'Error '.$connection->getLastHttpCode();
            $body = $connection->getLastBody();

            if (isset($body->title)) {
                $message = $message.' - '.$body->title;
            }

            if (isset($response->detail)) {
                $message = $message.' - '.$body->detail;
            }
        }

        $message = [
            false,
            $message,
        ];

        return $message;
    }

    /**
     * getUserTimeline.
     *
     * @param string $twUsername  Param
     * @param int    $twMaxTweets Param
     *
     * @return array
     */
    public function getUserTimeline($twUsername, $twMaxTweets)
    {
        $connection = $this->getTwitterApiConnectionV2();

        $response = $connection->get(
            'users/'.$this->channel->params->get('user_id').'/tweets',
            [
                'max_results' => $twMaxTweets,
            ]
        );

        return (array) $response;
    }

    protected function getOAuth2Provider()
    {
        require_once JPATH_ROOT.'/administrator/components/com_autotweet/controllers/twitterv2channels.php';

        $collaborators = ['httpClient' => new HttpClient(
            ['verify' => CAUTOTWEETNG_CAINFO]
        )];

        return new TwitterV2OAuthChannel([
            'clientId'          => $this->clientId,
            'clientSecret'      => $this->clientSecret,
            'redirectUri'       => AutotweetControllerTwitterV2Channels::getCallbackUrl(),
        ], $collaborators);
    }

    protected function getOAuth2ProviderToRefreshToken()
    {
        require_once JPATH_ROOT.'/administrator/components/com_autotweet/controllers/twitterv2channels.php';

        $collaborators = [
            'httpClient' => new HttpClient([
                'verify' => CAUTOTWEETNG_CAINFO,
                'auth' => [$this->clientId, $this->clientSecret]
            ]),
            'optionProvider' => new XTS_BUILD\League\OAuth2\Client\OptionProvider\HttpBasicAuthOptionProvider()
        ];

        return new TwitterV2OAuthChannel([
            'clientId'          => $this->clientId,
            'clientSecret'      => $this->clientSecret,
            'redirectUri'       => AutotweetControllerTwitterV2Channels::getCallbackUrl(),
            'verify'            => CAUTOTWEETNG_CAINFO,
        ], $collaborators);
    }

    private function getTwitterApiConnectionV1()
    {
        return new \XTS_BUILD\Abraham\TwitterOAuth\TwitterOAuth(
            $this->consumerKey,
            $this->consumerSecret,
            $this->accessToken,
            $this->accessTokenSecret
        );
    }

    private function getTwitterApiConnectionV2()
    {
        $bearerToken = $this->getBearerToken();

        if (!$bearerToken) {
            return false;
        }

        if ($bearerToken->hasExpired()) {
            $bearerToken = $this->refresh($bearerToken);
        }

        $twitterOAuth = new \XTS_BUILD\Abraham\TwitterOAuth\TwitterOAuth(
            $this->consumerKey,
            $this->consumerSecret,
            null,
            $bearerToken->getToken()
        );
        $twitterOAuth->setApiVersion('2');

        return $twitterOAuth;
    }
}
