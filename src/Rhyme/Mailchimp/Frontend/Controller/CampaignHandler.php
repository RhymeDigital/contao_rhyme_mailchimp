<?php

/**
 * Copyright (C) 2019 Rhyme Digital, LLC.
 *
 * @link       https://rhyme.digital
 * @license    http://www.gnu.org/licenses/lgpl-3.0.html LGPL
 */

namespace Rhyme\Mailchimp\Frontend\Controller;

use Contao\Date;
use Contao\File;
use Contao\Input;
use Contao\Config;
use Contao\System;
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
     * Create a new campaign in Mailchimp
     * @param Mailchimp $objMailchimp
     * @param MC_CampaignModel $objCampaign
     * @throws \Exception
     */
    public static function createNewMailchimpCampaign(Mailchimp $objMailchimp, MC_CampaignModel $objCampaign)
    {
        if (!$objCampaign->mc_list ||
            !$objCampaign->mc_subject ||
            !$objCampaign->mc_preview_text ||
            !$objCampaign->name ||
            !$objCampaign->mc_from_name ||
            !$objCampaign->mc_replyto_email
        ) {
            return;
        }

        if (!\class_exists('\MailchimpAPI\Mailchimp'))
        {
            throw new \Exception('Mailchimp API library not found.');
        }

        try
        {
            $arrCampaignData = array(
                'type' => 'regular',
                'recipients' => array(
                    'list_id' => $objCampaign->mc_list
                ),
                'settings' => array(
                    'subject_line' => $objCampaign->mc_subject,
                    'preview_text' => $objCampaign->mc_preview_text,
                    'title' => $objCampaign->name,
                    'from_name' => $objCampaign->mc_from_name,
                    'reply_to' => $objCampaign->mc_replyto_email,
                    'use_conversation' => false,
                    'to_name' => '',
                    'auto_footer' => true
                ),
                'tracking' => array(
                    'opens' => true,
                    'html_clicks' => true,
                    'text_clicks' => true,
                    'goal_tracking' => true,
                    'ecomm360' => false
                )
            );

            // !HOOK: Custom actions before creating campaign
            if (isset($GLOBALS['TL_HOOKS']['preCreateMailchimpCampaign']) && is_array($GLOBALS['TL_HOOKS']['preCreateMailchimpCampaign'])) {
                foreach ($GLOBALS['TL_HOOKS']['preCreateMailchimpCampaign'] as $callback) {
                    $objCallback = System::importStatic($callback[0]);
                    $arrCampaignData = $objCallback->{$callback[1]}($arrCampaignData, $objMailchimp, $objCampaign);
                }
            }

            // Send request
            $objResponse = $objMailchimp
                ->campaigns()
                ->post($arrCampaignData);

            // !HOOK: Custom actions after creating campaign
            if (isset($GLOBALS['TL_HOOKS']['postCreateMailchimpCampaign']) && is_array($GLOBALS['TL_HOOKS']['postCreateMailchimpCampaign'])) {
                foreach ($GLOBALS['TL_HOOKS']['postCreateMailchimpCampaign'] as $callback) {
                    $objCallback = System::importStatic($callback[0]);
                    $objCallback->{$callback[1]}($objResponse, $objMailchimp, $objCampaign);
                }
            }

            if (!$objResponse->wasSuccess())
            {
                throw new \Exception('Mailchimp error: ' . $objResponse->getBody());
            }
            else
            {
                $arrResponseData = json_decode($objResponse->getBody(), true);

                // Save the campaign data
                $objCampaign->campaign_id = $arrResponseData['id'];
                $objCampaign->mc_archive_url = $arrResponseData['archive_url'];
                $objCampaign->mc_long_archive_url = $arrResponseData['long_archive_url'];
                $objCampaign->save();

                // Todo: add options for more than "url" content
                $arrContentData = array(
                    'url' => Environment::get('url').'/mailchimp/campaign/'.$objCampaign->campaign_id
                );

                // !HOOK: Custom actions before creating content
                if (isset($GLOBALS['TL_HOOKS']['preCreateMailchimpCampaignContent']) && is_array($GLOBALS['TL_HOOKS']['preCreateMailchimpCampaignContent'])) {
                    foreach ($GLOBALS['TL_HOOKS']['preCreateMailchimpCampaignContent'] as $callback) {
                        $objCallback = System::importStatic($callback[0]);
                        $arrContentData = $objCallback->{$callback[1]}($arrContentData, $objMailchimp, $objCampaign, $arrResponseData);
                    }
                }

                // Set the content to the local URL - Todo: add other options
                $objResponse = $objMailchimp
                    ->campaigns($objCampaign->campaign_id)
                    ->content()
                    ->put($arrContentData);

                // !HOOK: Custom actions after creating content
                if (isset($GLOBALS['TL_HOOKS']['postCreateMailchimpCampaignContent']) && is_array($GLOBALS['TL_HOOKS']['postCreateMailchimpCampaignContent'])) {
                    foreach ($GLOBALS['TL_HOOKS']['postCreateMailchimpCampaignContent'] as $callback) {
                        $objCallback = System::importStatic($callback[0]);
                        $objCallback->{$callback[1]}($objResponse, $objMailchimp, $objCampaign);
                    }
                }

                if (!$objResponse->wasSuccess())
                {
                    throw new \Exception('Mailchimp error: ' . $objResponse->getBody());
                }
                else
                {
                    System::log('Mailchimp campaign created successfully: Contao ID = ' . $objCampaign->id . '; Mailchimp ID = ' . $objCampaign->campaign_id . ';', __METHOD__, TL_GENERAL);
                }
            }
        }
        catch (\Exception $e)
        {
            System::log($e->getMessage(), __METHOD__, TL_ERROR);
            throw new \Exception($e->getMessage());
        }
    }


    /**
     * Update an existing campaign in Mailchimp
     * @param Mailchimp $objMailchimp
     * @param MC_CampaignModel $objCampaign
     * @throws \Exception
     */
    public static function updateMailchimpCampaign(Mailchimp $objMailchimp, MC_CampaignModel $objCampaign)
    {
        if (!\class_exists('\MailchimpAPI\Mailchimp'))
        {
            throw new \Exception('Mailchimp API library not found.');
        }

        try
        {
            $arrCampaignData = array(
                'recipients' => array(
                    'list_id' => $objCampaign->mc_list
                ),
                'settings' => array(
                    'subject_line' => $objCampaign->mc_subject,
                    'preview_text' => $objCampaign->mc_preview_text,
                    'title' => $objCampaign->name,
                    'from_name' => $objCampaign->mc_from_name,
                    'reply_to' => $objCampaign->mc_replyto_email,
                )
            );

            // !HOOK: Custom actions before updating campaign
            if (isset($GLOBALS['TL_HOOKS']['preUpdateMailchimpCampaign']) && is_array($GLOBALS['TL_HOOKS']['preUpdateMailchimpCampaign'])) {
                foreach ($GLOBALS['TL_HOOKS']['preUpdateMailchimpCampaign'] as $callback) {
                    $objCallback = System::importStatic($callback[0]);
                    $arrCampaignData = $objCallback->{$callback[1]}($arrCampaignData, $objMailchimp, $objCampaign);
                }
            }

            $objResponse = $objMailchimp
                ->campaigns($objCampaign->campaign_id)
                ->patch($arrCampaignData);

            // !HOOK: Custom actions after updating campaign
            if (isset($GLOBALS['TL_HOOKS']['postUpdateMailchimpCampaign']) && is_array($GLOBALS['TL_HOOKS']['postUpdateMailchimpCampaign'])) {
                foreach ($GLOBALS['TL_HOOKS']['postUpdateMailchimpCampaign'] as $callback) {
                    $objCallback = System::importStatic($callback[0]);
                    $objCallback->{$callback[1]}($objResponse, $objMailchimp, $objCampaign);
                }
            }

            if (!$objResponse->wasSuccess())
            {
                throw new \Exception('Mailchimp error: ' . $objResponse->getBody());
            }
            else
            {
                System::log('Mailchimp campaign updated successfully: Contao ID = ' . $objCampaign->id . '; Mailchimp ID = ' . $objCampaign->campaign_id . ';', __METHOD__, TL_GENERAL);
            }
        }
        catch (\Exception $e)
        {
            System::log($e->getMessage(), __METHOD__, TL_ERROR);
            throw new \Exception($e->getMessage());
        }
    }


    /**
     * Unschedule a campaign in Mailchimp
     * @param Mailchimp $objMailchimp
     * @param MC_CampaignModel $objCampaign
     * @throws \Exception
     */
    public static function unscheduleMailchimpCampaign(Mailchimp $objMailchimp, MC_CampaignModel $objCampaign)
    {
        if (!\class_exists('\MailchimpAPI\Mailchimp'))
        {
            throw new \Exception('Mailchimp API library not found.');
        }

        try
        {
            // !HOOK: Custom actions before unscheduling the campaign
            if (isset($GLOBALS['TL_HOOKS']['preUnscheduleMailchimpCampaign']) && is_array($GLOBALS['TL_HOOKS']['preUnscheduleMailchimpCampaign'])) {
                foreach ($GLOBALS['TL_HOOKS']['preUnscheduleMailchimpCampaign'] as $callback) {
                    $objCallback = System::importStatic($callback[0]);
                    $objCallback->{$callback[1]}($objMailchimp, $objCampaign);
                }
            }

            // Try unscheduling - Todo: check status?
            $objResponse = $objMailchimp
                ->campaigns($objCampaign->campaign_id)
                ->unschedule();

            // !HOOK: Custom actions after unscheduling campaign
            if (isset($GLOBALS['TL_HOOKS']['postUnscheduleMailchimpCampaign']) && is_array($GLOBALS['TL_HOOKS']['postUnscheduleMailchimpCampaign'])) {
                foreach ($GLOBALS['TL_HOOKS']['postUnscheduleMailchimpCampaign'] as $callback) {
                    $objCallback = System::importStatic($callback[0]);
                    $objCallback->{$callback[1]}($objResponse, $objMailchimp, $objCampaign);
                }
            }

            if (!$objResponse->wasSuccess())
            {
                throw new \Exception('Mailchimp error: ' . $objResponse->getBody());
            }
            else
            {
                System::log('Mailchimp campaign unscheduled successfully: Contao ID = ' . $objCampaign->id . '; Mailchimp ID = ' . $objCampaign->campaign_id . ';', __METHOD__, TL_GENERAL);
            }
        }
        catch (\Exception $e)
        {
            System::log($e->getMessage(), __METHOD__, TL_ERROR);
            throw new \Exception($e->getMessage());
        }
    }


    /**
     * Generate the campaign HTML
     * @param $campaignId integer
     * @return Response
     */
    public static function generateHTML($campaignId)
    {
        // Make sure we have an ID
        $strCampaign = trim($campaignId);
        if (!$strCampaign)
        {
            System::log('No campaign selected.', __METHOD__, TL_ERROR);
            return new Response('No campaign selected.');
        }

        // Make sure the campaign is published
        $objCampaign = MC_CampaignModel::findPublishedByCampaignId($strCampaign);
        if ($objCampaign === null)
        {
            System::log('Invalid campaign selected.', __METHOD__, TL_ERROR);
            return new Response('Invalid campaign selected.');
        }

        $arrElements = array();
        $objTemplate = new FrontendTemplate($objCampaign->html_tpl);
        $objTemplate->setData($objCampaign->row());

        // Get elements
        $objElements = ContentModel::findPublishedByPidAndTable($objCampaign->id, MC_CampaignModel::getTable());
        while ($objElements !== null && $objElements->next())
        {
            // Add campaign data to element
            $objElements->current()->mc_campaign_data = $objCampaign->row();

            // Parse the element
            $strBuffer = Controller::getContentElement($objElements->current()->id);
            if (trim($strBuffer))
            {
                $arrElements[$objElements->current()->id] = array
                (
                    'id'            => $objElements->current()->id,
                    'model'         => $objElements->current(),
                    'html'          => $strBuffer
                );
            }
        }

        $objTemplate->elements = $arrElements;

        // Get our reset styles
        $objResetTemplate = new FrontendTemplate($objCampaign->reset_styles_tpl);
        $objTemplate->reset_styles = $objResetTemplate->parse();

        // Get our styles
        $objStyleTemplate = new FrontendTemplate($objCampaign->styles_tpl);
        $objTemplate->styles = $objStyleTemplate->parse();

        // Other properties
        $objTemplate->language = $GLOBALS['TL_LANGUAGE'];
        $objTemplate->language = $GLOBALS['TL_LANGUAGE'];
        $objTemplate->charset = Config::get('characterSet');
        $objTemplate->base = Environment::get('base');

        $strBuffer = Controller::replaceInsertTags($objTemplate->minifyHtml($objTemplate->parse()));

        // URL decode image paths (see contao/core#6411)
        // Make image paths absolute
        $blnOverrideRoot = false;
        $strBuffer = preg_replace_callback('@(src=")([^"]+)(")@', function ($args) use (&$blnOverrideRoot) {
            if (preg_match('@^(http://|https://|mailto:)@', $args[2]) || stripos($args[2], 'data:image') !== false) {
                return $args[1] . $args[2] . $args[3];
            }
            $blnOverrideRoot = true;
            return $args[1] . Environment::get('base') . '' . rawurldecode($args[2]) . $args[3];
        }, $strBuffer);

        // Make link paths absolute
        $blnOverrideRoot = false;
        $strBuffer = preg_replace_callback('@(href=")([^"]+)(")@', function ($args) use (&$blnOverrideRoot) {
            if (preg_match('@^(http://|https://|mailto:)@', $args[2])) {
                return $args[1] . $args[2] . $args[3];
            }
            $blnOverrideRoot = true;
            return $args[1] . Environment::get('base') . '' . rawurldecode($args[2]) . $args[3];
        }, $strBuffer);

        return new Response($strBuffer);
    }


    /**
     * Get content element image "src" so we don't use cached assets
     * @param $ceId
     * @return Response
     */
    public static function getContentElementSrcImageContent($ceId)
    {
        $strBuffer = Controller::getContentElement($ceId);
        if ($strBuffer)
        {
            $strBuffer = static::getSectionOfString($strBuffer, '<img ', '>');
            if ($strBuffer)
            {
                // Allow bypassing of src
                $srcAttribute = 'src';
                if (stripos($strBuffer, 'data-src="') !== false)
                {
                    $srcAttribute = 'data-src';
                    $strBuffer = static::getSectionOfString($strBuffer, 'data-src="', '"');
                }
                else
                {
                    $strBuffer = static::getSectionOfString($strBuffer, 'src="', '"');
                }

                if ($strBuffer)
                {
                    $strSrc = str_ireplace(array($srcAttribute.'="', '"'), '', $strBuffer);
                    if (file_exists(TL_ROOT.'/'.$strSrc))
                    {
                        try
                        {
                            $objFile = new File($strSrc);

                            // Send the right headers
                            header("Content-Type: image/".$objFile->extension);
                            header("Content-Length: " . $objFile->size);

                            // Dump the picture and stop the script
                            fpassthru($objFile->handle);
                            exit;
                        }
                        catch (\Exception $e) {
                            return new Response('');
                        }
                    }
                }
            }
        }

        return new Response('');
    }


    /**
     * Return a section of a string using a start and end (use "<input" and ">" to get any input elements)
     * @param string
     * @param string
     * @param string
     * @param boolean
     * @param integer
     * @return string
     */
    public static function getSectionOfString($strSubject, $strStart, $strEnd, $blnCaseSensitive=true, $intSearchStart=0)
    {
        // First index of start string
        $varStart = $blnCaseSensitive ? strpos($strSubject, $strStart, $intSearchStart) : stripos($strSubject, $strStart, $intSearchStart);

        if ($varStart === false)
        {
            return false;
        }

        // First index of end string
        $varEnd = $blnCaseSensitive ? strpos($strSubject, $strEnd, ($varStart + strlen($strStart))) : stripos($strSubject, $strEnd, ($varStart + strlen($strStart)));

        if ($varEnd === false)
        {
            return false;
        }

        // Return the string including the start string, end string, and everything in between
        return substr($strSubject, $varStart, ($varEnd + strlen($strEnd) - $varStart));
    }


    /**
     * Use output buffer to var dump to a string
     *
     * @param	string
     * @return	string
     */
    public static function varDumpToString($var)
    {
        ob_start();
        var_dump($var);
        $result = ob_get_clean();
        return $result;
    }


}