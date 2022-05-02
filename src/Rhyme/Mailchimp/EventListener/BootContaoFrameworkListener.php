<?php

/**
 * Noho
 *
 * Copyright (c) 2019 Rhyme.Digital
 *
 * @license LGPL-3.0+
 */

namespace Rhyme\Mailchimp\EventListener;

use Contao\CoreBundle\Framework\ContaoFramework;
use Symfony\Component\HttpKernel\Event\RequestEvent;

/**
 * Boots the Contao Framework
 */
class BootContaoFrameworkListener
{
    /**
     * @var ContaoFramework
     */
    private $framework;

    /**
     * Constructor.
     *
     * @param ContaoFramework $framework The Contao framework service
     */
    public function __construct(ContaoFramework $framework)
    {
        $this->framework = $framework;
    }

    /**
     * Boots the Contao Framework.
     *
     * @param RequestEvent $event The event object
     */
    public function onKernelRequest(RequestEvent $event)
    {
        $this->framework->initialize();
    }
}