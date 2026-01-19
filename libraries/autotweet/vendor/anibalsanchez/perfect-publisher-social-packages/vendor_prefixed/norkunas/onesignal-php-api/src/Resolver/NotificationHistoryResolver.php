<?php
/* This file has been prefixed by <PHP-Prefixer> for "XT Social Libraries" */

declare(strict_types=1);

namespace XTS_BUILD\OneSignal\Resolver;

use XTS_BUILD\OneSignal\Config;
use XTS_BUILD\Symfony\Component\OptionsResolver\OptionsResolver;

class NotificationHistoryResolver implements ResolverInterface
{
    private $config;

    public function __construct(Config $config)
    {
        $this->config = $config;
    }

    /**
     * {@inheritdoc}
     */
    public function resolve(array $data): array
    {
        return (new OptionsResolver())
            ->setRequired('events')
            ->setAllowedTypes('events', 'string')
            ->setAllowedValues('events', ['sent', 'clicked'])
            ->setRequired('email')
            ->setAllowedTypes('email', 'string')
            ->setDefault('app_id', $this->config->getApplicationId())
            ->setAllowedTypes('app_id', 'string')
            ->resolve($data);
    }
}
