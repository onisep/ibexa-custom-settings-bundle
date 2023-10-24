<?php

namespace Onisep\IbexaCustomSettingsBundle;

use Onisep\IbexaCustomSettingsBundle\DependencyInjection\IbexaCustomSettingsExtension;
use Onisep\IbexaCustomSettingsBundle\Security\IbexaCustomSettingsProvider;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Bundle\Bundle;

class IbexaCustomSettingsBundle extends Bundle
{
    protected $name = 'IbexaCustomSettingsBundle';

    protected function getContainerExtensionClass(): string
    {
        return IbexaCustomSettingsExtension::class;
    }

    public function build(ContainerBuilder $container): void
    {
        parent::build($container);

        $extension = $container->getExtension('ibexa');
        $extension->addPolicyProvider(new IbexaCustomSettingsProvider());
    }
}
