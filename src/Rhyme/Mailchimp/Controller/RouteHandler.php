<?php

/**
 * Copyright (C) 2019 Rhyme Digital, LLC.
 *
 * @link       https://rhyme.digital
 * @license    http://www.gnu.org/licenses/lgpl-3.0.html LGPL
 */

namespace Rhyme\Mailchimp\Controller;

use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerAwareTrait;
use Symfony\Component\HttpFoundation\Response;
use Rhyme\Mailchimp\Frontend\Controller\CampaignHandler;

/**
 * Handles custom scripts
 * *
 * @Route("/mailchimp", defaults={"_scope" = "frontend"})
 */
class RouteHandler extends Controller
{

    /**
     * Handles custom script responses
     *
     * @param $campaign
     * @return Response
     *
     * @Route("/campaign/{campaign}", name="contao_rhyme_mailchimp_campaign")
     */
    public function generateContent($campaign)
    {
        if ($this->container->has('contao.framework')) {
            $this->container->get('contao.framework')->initialize();
        }

        return CampaignHandler::generateHTML($campaign);
    }


}
