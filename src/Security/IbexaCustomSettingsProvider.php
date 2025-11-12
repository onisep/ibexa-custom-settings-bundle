<?php

declare(strict_types=1);

namespace Onisep\IbexaCustomSettingsBundle\Security;

use Ibexa\Bundle\Core\DependencyInjection\Security\PolicyProvider\YamlPolicyProvider;

class IbexaCustomSettingsProvider extends YamlPolicyProvider
{
    /**
     * {@inheritdoc}
     */
    protected function getFiles(): array
    {
        return [__DIR__ . '/../Resources/config/policies.yaml'];
    }
}
