<?php

/**
 * Copyright (C) 2019 Rhyme Digital, LLC.
 *
 * @link		http://rhyme.digital
 * @license		http://www.gnu.org/licenses/lgpl-3.0.html LGPL
 */

namespace Rhyme\Mailchimp;

use Symfony\Component\HttpKernel\Bundle\Bundle as SymfonyBundle;
use Rhyme\Mailchimp\DependencyInjection\RhymeMailchimpExtension as RhymeMailchimpExtension;

/**
 * Class RhymeMailchimpBundle
 * @package Rhyme\Mailchimp
 */
class RhymeMailchimpBundle extends SymfonyBundle
{
    /**
     * {@inheritdoc}
     */
    public function getContainerExtension()
    {
        return new RhymeMailchimpExtension();
    }

}