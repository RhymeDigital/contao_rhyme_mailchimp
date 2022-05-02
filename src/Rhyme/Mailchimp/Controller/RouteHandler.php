<?php

/**
 * Copyright (C) 2019 Rhyme Digital, LLC.
 *
 * @link       https://rhyme.digital
 * @license    http://www.gnu.org/licenses/lgpl-3.0.html LGPL
 */

namespace Rhyme\Mailchimp\Controller;

use Symfony\Component\Routing\Annotation\Route;
use Contao\CoreBundle\Controller\AbstractController;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerAwareTrait;
use Symfony\Component\HttpFoundation\Response;
use Contao\Controller as ContaoController;
use Rhyme\Mailchimp\Frontend\Controller\CampaignHandler;
use Symfony\Contracts\Service\ServiceSubscriberInterface;
use Psr\Container\ContainerInterface;

/**
 * Handles custom scripts
 * *
 * @Route("/mailchimp", defaults={"_scope" = "frontend"})
 */
class RouteHandler extends AbstractController implements ServiceSubscriberInterface
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
        $this->initializeContaoFramework();

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
        $this->initializeContaoFramework();

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
        $this->initializeContaoFramework();

        return CampaignHandler::getContentElementSrcImageContent($ceId);
    }

    /**
     * @required
     */
    public function setContainer(ContainerInterface $container): ?ContainerInterface
    {
        $previous = parent::setContainer($container);

        return $previous ?? $container;
    }


}
