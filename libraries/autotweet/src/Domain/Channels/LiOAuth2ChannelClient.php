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

use XTS_BUILD\Happyr\LinkedIn\Exception\LoginError;
use XTS_BUILD\Happyr\LinkedIn\Http\GlobalVariableGetter;
use XTS_BUILD\Happyr\LinkedIn\Http\RequestManager;
use XTS_BUILD\Happyr\LinkedIn\Http\ResponseConverter;
use XTS_BUILD\Happyr\LinkedIn\Http\UrlGenerator;
use XTS_BUILD\Happyr\LinkedIn\Http\UrlGeneratorInterface;
use XTS_BUILD\Happyr\LinkedIn\Storage\DataStorageInterface;
use XTS_BUILD\Http\Client\HttpClient;
use XTS_BUILD\Http\Message\MessageFactory;
use XTS_BUILD\Psr\Http\Message\ResponseInterface;

/* Exlty Client Class created to publish the XTS_BUILD\Happyr\LinkedIn\RequestManager,
    and set the GuzzleMessageFactory

 XTS_BUILD\Happyr\LinkedIn\Http\ResponseConverter::convertToArray(): Argument #1 ($response) must be of type XTS_BUILD\Psr\Http\Message\ResponseInterface,
      Nyholm\Psr7\Response given,
      called in .../libraries/autotweet/vendor/anibalsanchez/perfect-publisher-social-packages/vendor_prefixed/happyr/linkedin-api-client/src/Authenticator.php on line 125

*/

/**
 * Class LinkedIn lets you talk to LinkedIn api.
 *
 * When a new user arrives and want to authenticate here is whats happens:
 * 1. You redirect him to whatever url getLoginUrl() returns.
 * 2. The user logs in on www.linkedin.com and authorize your application.
 * 3. The user returns to your site with a *code* in the the $_REQUEST.
 * 4. You call isAuthenticated() or getAccessToken()
 * 5. We don't got an access token (only a *code*). So getAccessToken() calls fetchNewAccessToken()
 * 6. fetchNewAccessToken() gets the *code* from the $_REQUEST and calls getAccessTokenFromCode()
 * 7. getAccessTokenFromCode() makes a request to www.linkedin.com and exchanges the *code* for an access token
 * 8. When you have the access token you should store it in a database and/or query the API.
 * 9. When you make a second request to the API we have the access token in memory, so we don't go through all these
 *    authentication steps again.
 */
class LiOAuth2ChannelClient implements XTS_BUILD\Happyr\LinkedIn\LinkedInInterface
{
    /**
     * The OAuth access token received in exchange for a valid authorization
     * code.  null means the access token has yet to be determined.
     *
     * @var AccessToken
     */
    protected $accessToken = null;

    /**
     * @var string format
     */
    private $format;

    /**
     * @var string responseFormat
     */
    private $responseDataType;

    /**
     * @var ResponseInterface
     */
    private $lastResponse;

    /**
     * @var RequestManager
     */
    private $requestManager;

    /**
     * @var Authenticator
     */
    private $authenticator;

    /**
     * @var UrlGeneratorInterface
     */
    private $urlGenerator;

    /**
     * Constructor.
     *
     * @param string $appId
     * @param string $appSecret
     * @param string $format           'json', 'xml'
     * @param string $responseDataType 'array', 'string', 'simple_xml' 'psr7', 'stream'
     */
    public function __construct($appId, $appSecret, $format = 'json', $responseDataType = 'array')
    {
        $this->format = $format;
        $this->responseDataType = $responseDataType;

        $this->requestManager = new RequestManager();
        $this->authenticator = new XTS_BUILD\Happyr\LinkedIn\Authenticator($this->requestManager, $appId, $appSecret);
    }

    public function isAuthenticated()
    {
        $accessToken = $this->getAccessToken();
        if (null === $accessToken) {
            return false;
        }

        $user = $this->api('GET', '/v1/people/~:(id,firstName,lastName)', ['format' => 'json', 'response_data_type' => 'array']);

        return !empty($user['id']);
    }

    public function api($method, $resource, array $options = [])
    {
        // Add access token to the headers
        $options['headers']['Authorization'] = sprintf('Bearer %s', (string) $this->getAccessToken());

        // Do logic and adjustments to the options
        $requestFormat = $this->filterRequestOption($options);

        // Generate an url
        $url = $this->getUrlGenerator()->getUrl(
            'api',
            $resource,
            $options['query'] ?? []
        );

        $body = $options['body'] ?? null;
        $this->lastResponse = $this->getRequestManager()->sendRequest($method, $url, $options['headers'], $body);

        // Get the response data format
        if (isset($options['response_data_type'])) {
            $responseDataType = $options['response_data_type'];
        } else {
            $responseDataType = $this->getResponseDataType();
        }

        return ResponseConverter::convert($this->lastResponse, $requestFormat, $responseDataType);
    }

    public function getLoginUrl($options = [])
    {
        $urlGenerator = $this->getUrlGenerator();

        // Set redirect_uri to current URL if not defined
        if (!isset($options['redirect_uri'])) {
            $options['redirect_uri'] = $urlGenerator->getCurrentUrl();
        }

        return $this->getAuthenticator()->getLoginUrl($urlGenerator, $options);
    }

    /**
     * See docs for LinkedIn::api().
     *
     * @param string $resource
     */
    public function get($resource, array $options = [])
    {
        return $this->api('GET', $resource, $options);
    }

    /**
     * See docs for LinkedIn::api().
     *
     * @param string $resource
     */
    public function post($resource, array $options = [])
    {
        return $this->api('POST', $resource, $options);
    }

    public function clearStorage()
    {
        $this->getAuthenticator()->clearStorage();

        return $this;
    }

    public function hasError()
    {
        return GlobalVariableGetter::has('error');
    }

    public function getError()
    {
        if ($this->hasError()) {
            return new LoginError(GlobalVariableGetter::get('error'), GlobalVariableGetter::get('error_description'));
        }

        return null;
    }

    public function setFormat($format)
    {
        $this->format = $format;

        return $this;
    }

    public function setResponseDataType($responseDataType)
    {
        $this->responseDataType = $responseDataType;

        return $this;
    }

    public function getLastResponse()
    {
        return $this->lastResponse;
    }

    public function getAccessToken()
    {
        if (null === $this->accessToken && null !== $newAccessToken = $this->getAuthenticator()->fetchNewAccessToken($this->getUrlGenerator())) {
            $this->setAccessToken($newAccessToken);
        }

        // return the new access token or null if none found
        return $this->accessToken;
    }

    public function setAccessToken($accessToken)
    {
        if (!($accessToken instanceof XTS_BUILD\Happyr\LinkedIn\AccessToken)) {
            $accessToken = new XTS_BUILD\Happyr\LinkedIn\AccessToken($accessToken);
        }

        $this->accessToken = $accessToken;

        return $this;
    }

    public function setUrlGenerator(UrlGeneratorInterface $urlGenerator)
    {
        $this->urlGenerator = $urlGenerator;

        return $this;
    }

    public function setStorage(DataStorageInterface $dataStorage)
    {
        $this->getAuthenticator()->setStorage($dataStorage);

        return $this;
    }

    public function setHttpClient(HttpClient $httpClient)
    {
        $this->getRequestManager()->setHttpClient($httpClient);

        return $this;
    }

    public function setHttpMessageFactory(MessageFactory $messageFactory)
    {
        $this->getRequestManager()->setMessageFactory($messageFactory);

        return $this;
    }

    /**
     * @return RequestManager
     */
    public function getRequestManager()
    {
        return $this->requestManager;
    }

    /**
     * Modify and filter the request options. Make sure we use the correct query parameters and headers.
     *
     * @return string the request format to use
     */
    protected function filterRequestOption(array &$options)
    {
        if (isset($options['json'])) {
            $options['format'] = 'json';
            $options['body'] = json_encode($options['json']);
        } elseif (!isset($options['format'])) {
            // Make sure we always have a format
            $options['format'] = $this->getFormat();
        }

        // Set correct headers for this format
        switch ($options['format']) {
            case 'xml':
                $options['headers']['Content-Type'] = 'text/xml';
                break;
            case 'json':
                $options['headers']['Content-Type'] = 'application/json';
                $options['headers']['x-li-format'] = 'json';
                $options['query']['format'] = 'json';
                break;
            default:
                // Do nothing
        }

        return $options['format'];
    }

    /**
     * Get the default format to use when sending requests.
     *
     * @return string
     */
    protected function getFormat()
    {
        return $this->format;
    }

    /**
     * Get the default data type to be returned as a response.
     *
     * @return string
     */
    protected function getResponseDataType()
    {
        return $this->responseDataType;
    }

    /**
     * @return UrlGeneratorInterface
     */
    protected function getUrlGenerator()
    {
        if (null === $this->urlGenerator) {
            $this->urlGenerator = new UrlGenerator();
        }

        return $this->urlGenerator;
    }

    /**
     * @return Authenticator
     */
    protected function getAuthenticator()
    {
        return $this->authenticator;
    }
}
