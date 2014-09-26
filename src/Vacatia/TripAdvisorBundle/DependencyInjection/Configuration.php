<?php

namespace Vacatia\TripAdvisorBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

/**
 * This is the class that validates and merges configuration from your app/config files
 *
 * To learn more see {@link http://symfony.com/doc/current/cookbook/bundles/extension.html#cookbook-bundles-extension-config-class}
 */
class Configuration implements ConfigurationInterface
{
    const ROOT_NODE = 'vacatia_trip_advisor';

    /**
     * {@inheritDoc}
     */
    public function getConfigTreeBuilder()
    {
        $treeBuilder = new TreeBuilder();
        $rootNode = $treeBuilder->root(self::ROOT_NODE);

        $rootNode
            ->children()
                ->scalarNode('key')->isRequired()->end()
                ->arrayNode('cache')
                    ->children()
                        ->scalarNode('service')->isRequired()->end()
                        ->scalarNode('lifetime')->defaultValue(86400)->end()
                    ->end()
                ->end()
            ->end();

        return $treeBuilder;
    }
}
