<?php

declare(strict_types=1);

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

class OneSignalChannelHelper extends ChannelHelper
{
    private OneSignalClient $oneSignalClient;

    private OneSignalCredentials $oneSignalCredentials;

    private XTJoomlaCompatibility $xtJoomlaCompatibility;

    public function __construct(
        object $channel,
        string $appId = null,
        string $appApiAuthenticationKey = null,
        string $orgApiAuthenticationKey = null
    ) {
        parent::__construct($channel);

        $this->xtJoomlaCompatibility = new XTJoomlaCompatibility();
        $this->oneSignalCredentials = $this->createCredentials($channel, $appId, $appApiAuthenticationKey, $orgApiAuthenticationKey);
        $this->oneSignalClient = new OneSignalClient($this->oneSignalCredentials);
    }

    public function isAuth(): bool
    {
        if (!$this->oneSignalCredentials->isValid()) {
            return false;
        }

        try {
            return $this->oneSignalClient->testConnection();
        } catch (Exception $exception) {
            $this->handleAuthError($exception);

            return false;
        }
    }

    public function sendMessage($message, $data): array
    {
        $this->logSendAttempt($message);

        if (!$this->isAuth()) {
            return $this->createErrorResponse('COM_AUTOTWEET_CHANNEL_NOT_AUTH_ERR');
        }

        try {
            $oneSignalNotification = new OneSignalNotification($message, $data, $this->channel, $this->xtJoomlaCompatibility);
            $response = $this->oneSignalClient->sendNotification($oneSignalNotification);

            return $this->createSuccessResponse($response);
        } catch (Exception $exception) {
            return $this->createErrorResponse($exception->getMessage());
        }
    }

    public function includeHashTags(): bool
    {
        return (bool) $this->channel->params->get('hashtags', true);
    }

    private function createCredentials(
        object $channel,
        ?string $appId,
        ?string $appApiAuthenticationKey,
        ?string $orgApiAuthenticationKey
    ): OneSignalCredentials {
        if ($channel->id) {
            return OneSignalCredentials::fromChannel($channel);
        }

        return OneSignalCredentials::fromParameters($appId, $appApiAuthenticationKey, $orgApiAuthenticationKey);
    }

    private function handleAuthError(Throwable $throwable): void
    {
        $logger = AutotweetLogger::getInstance();
        $logger->log($this->xtJoomlaCompatibility->getLogLevel('ERROR'), $throwable->getMessage());

        $this->xtJoomlaCompatibility->enqueueMessage($throwable->getMessage(), 'error');
    }

    private function logSendAttempt(string $message): void
    {
        $logger = AutotweetLogger::getInstance();
        $logger->log($this->xtJoomlaCompatibility->getLogLevel('INFO'), 'sendOneSignalMessage', $message);
    }

    private function createErrorResponse(string $message): array
    {
        return [false, $this->xtJoomlaCompatibility->getText($message)];
    }

    private function createSuccessResponse(array $response): array
    {
        $messageId = $response['id'] ?? null;

        if (empty($messageId)) {
            $error = $this->extractError($response);

            return [false, $error];
        }

        return [true, 'OK - '.$messageId];
    }

    private function extractError(array $response): string
    {
        $errors = $response['errors'] ?? [];

        return array_pop($errors) ?? 'Unknown error';
    }
}
