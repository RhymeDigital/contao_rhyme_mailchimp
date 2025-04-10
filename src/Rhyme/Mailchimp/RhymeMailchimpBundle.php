<?php

/**
 * Copyright (C) 2019 Rhyme Digital, LLC.
 *
 * @link		http://rhyme.digital
 * @license		http://www.gnu.org/licenses/lgpl-3.0.html LGPL
 */

namespace Rhyme\Mailchimp;

use Symfony\Component\DependencyInjection\Extension\ExtensionInterface;
use Symfony\Component\HttpKernel\Bundle\Bundle;
use Rhyme\Mailchimp\DependencyInjection\RhymeMailchimpExtension as RhymeMailchimpExtension;

/**
 * Class RhymeMailchimpBundle
 * @package Rhyme\Mailchimp
 */
class RhymeMailchimpBundle extends Bundle
{
    /**
     * {@inheritdoc}
     */
    public function getContainerExtension(): ?ExtensionInterface
    {
        return new RhymeMailchimpExtension();
    }

}