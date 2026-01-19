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

use XTS_BUILD\League\OAuth2\Client\Token\AccessToken;

class TwitterV2OAuthChannel extends \XTS_BUILD\Smolblog\OAuth2\Client\Provider\Twitter
{
    public function getResourceOwnerDetailsUrl(AccessToken $accessToken): string
    {
        return 'https://api.twitter.com/2/users/me?user.fields=id,name,username';
    }
}
