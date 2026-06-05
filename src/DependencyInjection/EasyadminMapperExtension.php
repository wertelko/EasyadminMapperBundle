<?php

namespace Wertelko\EasyadminMapperBundle\DependencyInjection;

use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Extension\Extension;
use Symfony\Component\DependencyInjection\Extension\PrependExtensionInterface;
use Symfony\Component\DependencyInjection\Loader\YamlFileLoader;

class EasyadminMapperExtension extends Extension implements PrependExtensionInterface
{
    public function load(array $configs, ContainerBuilder $container): void
    {
        $loader = new YamlFileLoader(
            $container,
            new FileLocator(dirname(__DIR__, 2) . '/config')
        );

        $loader->load('services.yaml');
    }

    public function prepend(ContainerBuilder $container)
    {
        // Register asset package so asset('...', 'EasyadminMapper') works
        $container->prependExtensionConfig('framework', [
            'assets' => [
                'packages' => [
                    'EasyadminMapper' => [
                        'base_path' => 'public',
                    ],
                ],
            ],
        ]);
    }
}