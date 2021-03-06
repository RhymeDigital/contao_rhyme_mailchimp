<?php

/**
 * Copyright (C) 2019 Rhyme Digital, LLC.
 *
 * @link       https://rhyme.digital
 * @license    http://www.gnu.org/licenses/lgpl-3.0.html LGPL
 */

namespace Rhyme\Mailchimp\Controller;

use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerAwareTrait;
use Symfony\Component\HttpFoundation\Response;
use Contao\Controller as ContaoController;
use Rhyme\Mailchimp\Frontend\Controller\CampaignHandler;

/**
 * Handles custom scripts
 * *
 * @Route("/mailchimp", defaults={"_scope" = "frontend"})
 */
class RouteHandler extends AbstractController
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

    /**
     * Handles custom script responses
     *
     * @return Response
     *
     * @Route("/campaign", name="contao_rhyme_mailchimp_campaign_empty")
     */
    public function emptyFix()
    {
        if ($this->container->has('contao.framework')) {
            $this->container->get('contao.framework')->initialize();
        }

        ContaoController::redirect('');

        return CampaignHandler::generateHTML($campaign);
    }

    /**
     * Handles custom script responses
     *
     * @param $ceId
     * @return Response
     *
     * @Route("/getCEsrcImgContent/{ceId}", name="contao_rhyme_mailchimp_getcesrcimgcontent")
     */
    public function getContentElementSrcImageContent($ceId)
    {
        if ($this->container->has('contao.framework')) {
            $this->container->get('contao.framework')->initialize();
        }

        return CampaignHandler::getContentElementSrcImageContent($ceId);
    }


}
