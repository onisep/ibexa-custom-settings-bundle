<?php

namespace Onisep\IbexaCustomSettingsBundle\Security;

use eZ\Bundle\EzPublishCoreBundle\DependencyInjection\Security\PolicyProvider\YamlPolicyProvider;

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
