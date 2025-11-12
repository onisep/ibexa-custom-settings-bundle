<?php

declare(strict_types=1);

namespace Onisep\IbexaCustomSettingsBundle\DependencyInjection;

use Symfony\Component\Config\FileLocator;
use Symfony\Component\Config\Resource\FileResource;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Extension\Extension;
use Symfony\Component\DependencyInjection\Extension\PrependExtensionInterface;
use Symfony\Component\DependencyInjection\Loader\YamlFileLoader;
use Symfony\Component\Yaml\Yaml;

class IbexaCustomSettingsExtension extends Extension implements PrependExtensionInterface
{
    #[\Override]
    public function getAlias(): string
    {
        return 'ibexa_custom_settings';
    }

    public function load(array $configs, ContainerBuilder $container): void
    {
        $yamlFileLoader = new YamlFileLoader($container, new FileLocator(__DIR__ . '/../Resources/config'));
        $yamlFileLoader->load('services.yaml');
    }

    public function prepend(ContainerBuilder $container)
    {
        $configFile = __DIR__.'/../Resources/config/doctrine.yaml';
        $config = Yaml::parse(file_get_contents($configFile));
        $container->prependExtensionConfig('doctrine', $config);
        $container->addResource(new FileResource($configFile));
    }
}
