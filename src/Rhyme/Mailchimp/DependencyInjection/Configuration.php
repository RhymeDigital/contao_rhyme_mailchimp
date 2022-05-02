<?php

/**
 * Rhyme Mailchimp Bundle
 *
 * Copyright (c) 2022 Rhyme.Digital
 *
 * @license LGPL-3.0+
 */

namespace Rhyme\Mailchimp\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

/**
 * Overrides the Contao configuration structure.
 */
class Configuration implements ConfigurationInterface
{
    /**
     * Generates the configuration tree builder.
     *
     * @return TreeBuilder The tree builder
     */
    public function getConfigTreeBuilder()
    {
        $treeBuilder = new TreeBuilder('rhyme_mailchimp');

        if (method_exists($treeBuilder, 'getRootNode')) {
            $rootNode = $treeBuilder->getRootNode();
        } else {
            // BC layer for symfony/config 4.1 and older
            $rootNode = $treeBuilder->root('rhyme_mailchimp');
        }

        return $treeBuilder;
    }
}