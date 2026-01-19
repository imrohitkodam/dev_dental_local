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
 * AutotweetControllerTwitterV2Channels.
 *
 * @since       1.0
 */
class AutotweetControllerTwitterV2Channels extends ExtlyController
{
    /**
     * getCallbackUrl.
     *
     * @param int    $channelId Param
     * @param string $callback  Param
     *
     * @return string
     */
    public static function getCallbackUrl()
    {
        return \Joomla\CMS\Uri\Uri::base().
            'index.php?option=com_autotweet&_token='.
            \Joomla\CMS\Factory::getSession()->getFormToken();
    }

    /**
     * callback.
     */
    public function callback()
    {
        // if ($this->csrfProtection) {
        //     $this->_csrfProtection();
        // }

        try {
            $session = \Joomla\CMS\Factory::getSession();
            $channelId = $session->get('channelId');
            $oauth2state = $session->get('oauth2state');
            $oauth2verifier = $session->get('oauth2verifier');

            // Invalidating channelId
            $session->set('channelId', false);

            $channel = XTF0FTable::getAnInstance('Channel', 'AutoTweetTable');
            $result = $channel->load($channelId);

            if (!$result) {
                throw new Exception('LinkedIn '.JText::_('COM_AUTOTWEET_CHANNEL_NOTLOADED'));
            }

            $input = new \Joomla\CMS\Input\Input();
            $state = $input->getString('state');

            if ($state !== $oauth2state) {
                // throw new Exception('Twitter X API v2 OAuth: Invalid state');

                \Joomla\CMS\Factory::getApplication()->enqueueMessage(
                    'Twitter X - API v2: Authorization cancelled.',
                    'notice'
                );

                $url = 'index.php?option=com_autotweet&view=channels';
                $this->setRedirect($url);
                $this->redirect();

                return;
            }

            $twitterV2ChannelHelper = new TwitterV2ChannelHelper($channel);
            $code = $input->getString('code');
            $twitterV2ChannelHelper->authenticate($code, $oauth2verifier);

            // Redirect
            $url = 'index.php?option=com_autotweet&view=channels&task=edit&id='.$channelId;
            $this->setRedirect($url);
            $this->redirect();
        } catch (Exception $exception) {
            $logger = AutotweetLogger::getInstance();
            $logger->log(\Joomla\CMS\Log\Log::ERROR, $exception->getMessage());

            throw $exception;
        }
    }
}
