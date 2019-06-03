<?php

/**
 * Copyright (C) 2019 Rhyme Digital, LLC.
 *
 * @link       https://rhyme.digital
 * @license    http://www.gnu.org/licenses/lgpl-3.0.html LGPL
 */

namespace Rhyme\Mailchimp\Frontend\Controller;

use Contao\Input;
use Contao\Config;
use Contao\Database;
use Contao\Controller;
use Contao\Environment;
use Contao\ContentModel;
use Contao\FrontendTemplate;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use MailchimpAPI\Mailchimp;
use MailchimpAPI\Responses\MailchimpResponse;
use MailchimpAPI\MailchimpException;
use Rhyme\Mailchimp\Model\Campaign as MC_CampaignModel;

/**
 * Handles custom scripts
 * *
 */
class CampaignHandler extends Controller
{

    /**
     * Load the database object
     *
     * Make the constructor public, so pages can be instantiated (see #6182)
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Generate the campaign HTML
     * @param $campaignId integer
     * @return Response
     */
    public static function generateHTML($campaignId)
    {
        // Make sure we have an ID
        $intCampaign = intval($campaignId);
        if (!$intCampaign)
        {
            static::log('No campaign selected.', __METHOD__, TL_ERROR);
            return new Response('No campaign selected.');
        }

        // Make sure the campaign is published
        $objCampaign = MC_CampaignModel::findPublishedById($intCampaign);
        if ($objCampaign === null)
        {
            static::log('Invalid campaign selected.', __METHOD__, TL_ERROR);
            return new Response('Invalid campaign selected.');
        }

        $arrElements = array();
        $objTemplate = new FrontendTemplate($objCampaign->html_tpl);

        // Get elements
        $objElements = ContentModel::findPublishedByPidAndTable($intCampaign, MC_CampaignModel::getTable());
        while ($objElements !== null && $objElements->next())
        {
            $strBuffer = Controller::getContentElement($objElements->current()->id);
            if (trim($strBuffer))
            {
                // URL decode image paths (see contao/core#6411)
                // Make image paths absolute
                $blnOverrideRoot = false;
                $strBuffer = preg_replace_callback('@(src=")([^"]+)(")@', function ($args) use (&$blnOverrideRoot) {
                    if (preg_match('@^(http://|https://)@', $args[2])) {
                        return $args[1] . $args[2] . $args[3];
                    }
                    $blnOverrideRoot = true;
                    return $args[1] . Environment::get('base') . '/' . rawurldecode($args[2]) . $args[3];
                }, $strBuffer);

                $arrElements[$objElements->current()->id] = array
                (
                    'id'            => $objElements->current()->id,
                    'model'         => $objElements->current(),
                    'html'          => $strBuffer
                );
            }
        }

        $objTemplate->elements = $arrElements;

        // Get our styles
        $objStyleTemplate = new FrontendTemplate($objCampaign->styles_tpl);
        $objTemplate->styles = $objStyleTemplate->parse();

        // Other properties
        $objTemplate->language = $GLOBALS['TL_LANGUAGE'];
        $objTemplate->charset = Config::get('characterSet');
        $objTemplate->base = Environment::get('base');

        return new Response($objTemplate->parse());
    }


}