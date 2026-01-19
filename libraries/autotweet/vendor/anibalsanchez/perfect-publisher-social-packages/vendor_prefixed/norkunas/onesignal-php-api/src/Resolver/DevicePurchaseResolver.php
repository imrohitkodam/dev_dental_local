<?php
/* This file has been prefixed by <PHP-Prefixer> for "XT Social Libraries" */

declare(strict_types=1);

namespace XTS_BUILD\OneSignal\Resolver;

use XTS_BUILD\Symfony\Component\OptionsResolver\OptionsResolver;

class DevicePurchaseResolver implements ResolverInterface
{
    /**
     * {@inheritdoc}
     */
    public function resolve(array $data): array
    {
        $data = (new OptionsResolver())
            ->setDefined('existing')
            ->setAllowedTypes('existing', 'bool')
            ->setRequired('purchases')
            ->setAllowedTypes('purchases', 'array')
            ->resolve($data);

        foreach ($data['purchases'] as $key => $purchase) {
            $data['purchases'][$key] = (new OptionsResolver())
                ->setRequired('sku')
                ->setAllowedTypes('sku', 'string')
                ->setRequired('amount')
                ->setAllowedTypes('amount', 'float')
                ->setRequired('iso')
                ->setAllowedTypes('iso', 'string')
                ->resolve($purchase);
        }

        return $data;
    }
}
