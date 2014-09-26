<?php

namespace Vacatia\TripAdvisorBundle\DependencyInjection;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;
use Symfony\Component\DependencyInjection\Loader;
use Doctrine\Common\Cache\CacheProvider;

/**
 * This is the class that loads and manages your bundle configuration
 *
 * To learn more see {@link http://symfony.com/doc/current/cookbook/bundles/extension.html}
 */
class VacatiaTripAdvisorExtension extends Extension
{
    /**
     * {@inheritDoc}
     */
    public function load(array $configs, ContainerBuilder $container)
    {
        $configuration = new Configuration();
        $config = $this->processConfiguration($configuration, $configs);

        $loader = new Loader\XmlFileLoader($container, new FileLocator(__DIR__.'/../Resources/config'));
        $loader->load('services.xml');

        foreach ($config as $key => $value) {
            if (is_array($value)) {
                foreach ($value as $_key => $_value) {
                    $container->setParameter(
                        sprintf('%s.%s.%s', Configuration::ROOT_NODE, $key, $_key),
                        $_value
                    );
                }
            } else {
                $container->setParameter(
                    sprintf('%s.%s', Configuration::ROOT_NODE, $key),
                    $value
                );
            }
        }

//        $cacheService = $container->get($config['cache']['service']);
//        if (!($cacheService instanceof CacheProvider)) {
//            throw new \Exception(
//                'The cache service must be an instance of \\Doctrine\\Common\\Cache\\CacheProvider'
//            );
//        }

        $container->setAlias(
            sprintf('%s.%s', Configuration::ROOT_NODE, 'cache.service'),
            $config['cache']['service']
        );
    }
}
