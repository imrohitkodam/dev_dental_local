<?php
/**
 * @package	AcyMailing for Joomla!
 * @version	5.13.0
 * @author	acyba.com
 * @copyright	(C) 2009-2023 ACYBA S.A.R.L. All rights reserved.
 * @license	GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 */

defined('_JEXEC') or die('Restricted access');
?><?php

class acyupdatemeHelper
{
    private $curlCode;
    public $errors = '';

    public function call($path, $method = 'GET', $data = [], $headers = [])
    {
        $url = ACYMAILING_UPDATEME_API_URL.$path;

        $config = acymailing_config();
        $apiKey = $config->get('license_key', '');

        $headers['Content-Type'] = 'application/json';
        $headers['API-KEY'] = $apiKey;

        try {
            $result = $this->makeCurlCall($url, $method, $data, $headers, true);
        } catch (Exception $exception) {
            return [
                'success' => false,
                'message' => $exception->getMessage(),
            ];
        }

        if (!empty($result['error'])) {
            return [
                'success' => false,
                'message' => $result['error'],
            ];
        }

        return [
            'success' => true,
            'result' => $result,
            'code' => $this->curlCode,
        ];
    }

    function makeCurlCall($url, $method = 'GET', $fields = [], $headers = [], $dontVerifySSL = false)
    {

        $ch = curl_init();

        if (ACYMAILING_CMS_SIMPLE === 'joomla' && acymailing_getCMSConfig('proxy_enable', false)) {
            curl_setopt($ch, CURLOPT_PROXY, acymailing_getCMSConfig('proxy_host', '').':'.acymailing_getCMSConfig('proxy_port', ''));
            $user = acymailing_getCMSConfig('proxy_user', '');
            $password = acymailing_getCMSConfig('proxy_pass', '');
            if (!empty($user) && !empty($password)) {
                curl_setopt($ch, CURLOPT_PROXYUSERPWD, $user.':'.$password);
            }
        }

        if ($method === 'GET') {
            if (!empty($fields)) {
                $url .= '?'.http_build_query($fields);
            }
        } else {
            curl_setopt($ch, CURLOPT_POST, true);
            if (!empty($fields)) {
                curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($fields));
            }
        }

        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

        if ($dontVerifySSL) {
            curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
        }

        if (!empty($headers)) {
            $formattedHeaders = [];
            foreach ($headers as $key => $value) {
                $formattedHeaders[] = $key.': '.$value;
            }
            curl_setopt($ch, CURLOPT_HTTPHEADER, $formattedHeaders);
        }

        $result = curl_exec($ch);

        if (curl_errno($ch)) {
            $error = curl_error($ch);

            curl_close($ch);

            return ['error' => $error];
        }

        $this->curlCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        return json_decode($result, true);
    }

    public function getLicenseInfo()
    {
        ob_start();
        $config = acymailing_config();
        $url = 'public/getLicenseInfo';
        $url .= '?level='.urlencode(strtolower($config->get('level', 'starter')));
        if (acymailing_level(1)) {
            $url .= '&domain='.urlencode(rtrim(ACYMAILING_LIVE, '/'));
        }
        $url .= '&version=5';
        $userInformation = $this->call($url);
        $warnings = ob_get_clean();
        $this->errors = (!empty($warnings) && acymailing_isDebug()) ? $warnings : '';

        if (empty($userInformation['success'])) {
            $config->save(['lastlicensecheck' => time()]);

            return '';
        }

        $newConfig = new \stdClass();
        $newConfig->latestversion = $userInformation['result']['latestversion'];
        $newConfig->expirationdate = $userInformation['result']['expiration'];
        $newConfig->lastlicensecheck = time();
        $config->save($newConfig);

        return $newConfig->lastlicensecheck;
    }
}

