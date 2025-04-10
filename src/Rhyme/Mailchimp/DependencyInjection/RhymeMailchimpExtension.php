<?php

/**
 * Copyright (C) 2022 Rhyme Digital, LLC.
 *
 * @link		http://rhyme.digital
 * @license		http://www.gnu.org/licenses/lgpl-3.0.html LGPL
 */

namespace Rhyme\Mailchimp\DependencyInjection;

use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader\YamlFileLoader;
use Symfony\Component\DependencyInjection\Extension\Extension;

/**
 * Adds the bundle services to the container.
 */
class RhymeMailchimpExtension extends Extension
{
    /**
     * @var array
     */
    private $files = [
        'services.yml',
        'listener.yml',
    ];

    /**
     * {@inheritdoc}
     */
    public function getAlias(): string
    {
        return 'rhyme_mailchimp';
    }

    /**
     * {@inheritdoc}
     */
    public function load(array $mergedConfig, ContainerBuilder $container): void
    {
        $configuration = new Configuration();
        $processedConfig = $this->processConfiguration($configuration, $mergedConfig);

        $loader = new YamlFileLoader(
            $container,
            new FileLocator(__DIR__.'/../Resources/config')
        );

        foreach ($this->files as $file) {
            $loader->load($file);
        }
    }
}