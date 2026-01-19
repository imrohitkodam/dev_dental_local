<?php
/**
 * @author      Extly, CB. <team@extly.com>
 * @copyright   Copyright (c)2012-2025 Extly, CB. All rights reserved.
 * @license     https://www.gnu.org/licenses/gpl-3.0.html GNU/GPL
 *
 * @see        https://www.extly.com
 */
defined('_JEXEC') || exit;

$session = \Joomla\CMS\Factory::getSession();
$session->set('channelId', $this->item->id);
$channeltypeId = $this->input->get('channeltype_id', AutotweetModelChanneltypes::TYPE_TWITTERV2_CHANNEL, 'cmd');

?>
<!-- com_autotweet_OUTPUT_START -->
<p style="text-align:center;">
    <span class="loaderspinner">&nbsp;</span>
</p>
<div class="xt-alert xt-alert-warning">
    <b>EXPERIMENTAL</b>: Twitter X API v2 is throwing authorization
    errors for some accounts. If your tweets aren't published,
    re-check the App configuration, follow the tutorial again, and
    re-authorize the channel.
</div>
<?php

echo JText::_('COM_AUTOTWEET_CHANNEL_TWITTERV2_DESC').'<hr>';

$required = ['class' => 'required', 'required' => 'required'];

// consumer_key
echo EHtml::textControl($this->item->xtform->get('consumer_key'), 'xtform[consumer_key]', 'COM_AUTOTWEET_CHANNEL_TWITTERV2_API_KEY', 'COM_AUTOTWEET_CHANNEL_TWITTERV2_API_KEY_DESC', 'consumer_key', 60, $required);

// consumer_secret
$textControl = EHtml::textControl($this->item->xtform->get('consumer_secret'), 'xtform[consumer_secret]', 'COM_AUTOTWEET_CHANNEL_TWITTERV2_API_KEY_SECRET', 'COM_AUTOTWEET_CHANNEL_TWITTERV2_API_KEY_SECRET_DESC', 'consumer_secret', 60, $required);
echo str_replace('type="text"', 'type="password"', $textControl);

// access_token
echo EHtml::textControl($this->item->xtform->get('access_token'), 'xtform[access_token]', 'COM_AUTOTWEET_CHANNEL_TWITTERV2_ACCESS_TOKEN', 'COM_AUTOTWEET_CHANNEL_TWITTERV2_ACCESS_TOKEN_DESC', 'access_token', 60, $required);

// access_token_secret
$textControl = EHtml::textControl($this->item->xtform->get('access_token_secret'), 'xtform[access_token_secret]', 'COM_AUTOTWEET_CHANNEL_TWITTERV2_ACCESS_TOKEN_SECRET', 'COM_AUTOTWEET_CHANNEL_TWITTERV2_ACCESS_TOKEN_SECRET_DESC', 'access_token_secret', 60, $required);
echo str_replace('type="text"', 'type="password"', $textControl);

// client_id
echo EHtml::textControl($this->item->xtform->get('client_id'), 'xtform[client_id]', 'COM_AUTOTWEET_CHANNEL_TWITTERV2_CLIENT_ID', 'COM_AUTOTWEET_CHANNEL_TWITTERV2_CLIENT_ID_DESC', 'client_id', 60, $required);

// client_secret
$textControl = EHtml::textControl($this->item->xtform->get('client_secret'), 'xtform[client_secret]', 'COM_AUTOTWEET_CHANNEL_TWITTERV2_CLIENT_SECRET', 'COM_AUTOTWEET_CHANNEL_TWITTERV2_CLIENT_SECRET_DESC', 'client_secret', 60, $required);
echo str_replace('type="text"', 'type="password"', $textControl);

$user = null;
$userId = null;
$bearerToken = null;

$authUrl = '#';
$authUrlButtonStyle = 'disabled';
$validationGroupStyle = 'hide';

// New channel, not even saved
if (!(bool) $this->item->id) {
    $message = JText::_('COM_AUTOTWEET_CHANNEL_TWITTERV2_NOAUTHORIZATION');
    require_once __DIR__ . '/auth_button.php';
} else {
    $twitterV2ChannelHelper = new TwitterV2ChannelHelper($this->item);
    $userData = $twitterV2ChannelHelper->isAuth();
    $authUrl = $twitterV2ChannelHelper->getAuthorizationUrl();

    // New channel, but saved
    if ($userData) {
        $user = $userData['username'];
        $userId = $userData['id'];
        $this->item->xtform->set('social_url', $twitterV2ChannelHelper->getSocialUrl($userData));

        $validationGroupStyle = null;

        $bearerToken = $twitterV2ChannelHelper->getBearerToken();
        require_once __DIR__ . '/validation_button.php';
    } else {
        $message = JText::_('COM_AUTOTWEET_CHANNEL_TWITTERV2_AUTHORIZATION');

        if (empty($authUrl)) {
            $authUrl = '#';
            $message = JText::_('COM_AUTOTWEET_CHANNEL_TWITTERV2_NOAUTHORIZATION');
            require_once __DIR__ . '/auth_button.php';
        } else {
            $authUrlButtonStyle = null;

            require_once __DIR__ . '/auth_button.php';
            require_once __DIR__ . '/validation_button.php';
        }
    }
}

?>
<hr>
<div class="xt-alert xt-alert-info">
    <ul class="unstyled">
        <li>
            <i class="xticon far fa-thumbs-up"></i>
            <a href="#"
                onclick="window.open('https://www.extly.com/docs/perfect_publisher/user_guide/tutorials/', '_blank')">
                Tutorial: How to publish to Twitter</a>
        </li>
        <li>
            <i class="xticon fab fa-youtube"></i>
            <a href="#"
                onclick="window.open('https://www.extly.com/docs/perfect_publisher/user_guide/tutorials/', '_blank')">
                How to publish from Joomla to Twitter - Video</a>
        </li>
        <li>
            <i class="xticon far fa-thumbs-up"></i>
            <a href="#"
                onclick="window.open('https://www.extly.com/docs/perfect_publisher/user_guide/tutorials/', '_blank')">
                How to publish from Joomla</a>
        </li>
    </ul>
</div>

<!-- com_autotweet_OUTPUT_START -->
