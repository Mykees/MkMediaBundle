<?php

namespace Mykees\MediaBundle\DependencyInjection;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;
use Symfony\Component\DependencyInjection\Loader;

/**
 * This is the class that loads and manages your bundle configuration
 *
 * To learn more see {@link http://symfony.com/doc/current/cookbook/bundles/extension.html}
 */
class MykeesMediaExtension extends Extension
{
    /**
     * {@inheritdoc}
     */
    public function load(array $configs, ContainerBuilder $container)
    {
        $configuration = new Configuration();
        $config = $this->processConfiguration($configuration, $configs);

        $container->setParameter("mykees.media.extension", isset($config["allowExtension"]) ? $config["allowExtension"] : null);
        $container->setParameter("mykees.media.path", isset($config["path"]) ? $config["path"] : null);
        $container->setParameter("mykees.media.resize", isset($config["resize"]) ? $config["resize"] : null);
        $container->setParameter("mykees.media.mode", isset($config["mode"]) ? $config["mode"] : null);

        $loader = new Loader\YamlFileLoader($container, new FileLocator(__DIR__.'/../Resources/config'));
        $loader->load('services.yml');
    }
}
