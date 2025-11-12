<?php

declare(strict_types=1);

namespace Onisep\IbexaCustomSettingsBundle;

use Onisep\IbexaCustomSettingsBundle\DependencyInjection\IbexaCustomSettingsExtension;
use Onisep\IbexaCustomSettingsBundle\Security\IbexaCustomSettingsProvider;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Bundle\Bundle;

class IbexaCustomSettingsBundle extends Bundle
{
    protected string $name = 'IbexaCustomSettingsBundle';

    #[\Override]
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
