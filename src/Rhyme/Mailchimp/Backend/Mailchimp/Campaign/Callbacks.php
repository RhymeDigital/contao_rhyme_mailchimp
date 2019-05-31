<?php

/**
 * Copyright (C) 2019 Rhyme Digital, LLC.
 *
 * @link       https://rhyme.digital
 * @license    http://www.gnu.org/licenses/lgpl-3.0.html LGPL
 */

namespace Rhyme\Mailchimp\Backend\Mailchimp\Campaign;

use Contao\Input;
use Contao\Image;
use Contao\System;
use Contao\Backend;
use Contao\Database;
use Contao\Versions;
use Contao\StringUtil;
use Contao\Environment;
use Contao\DataContainer;
use Contao\CoreBundle\Exception\AccessDeniedException;
use Rhyme\Mailchimp\Model\ApiKey as MC_ApiKeyModel;
use Rhyme\Mailchimp\Model\Campaign as MC_CampaignModel;
use MailchimpAPI\Mailchimp;
use MailchimpAPI\Responses\MailchimpResponse;
use MailchimpAPI\MailchimpException;

/**
 * Handles backend DCA callbacks
 * *
 */
class Callbacks extends Backend
{
    /**
     * Import the back end user object
     */
    public function __construct()
    {
        parent::__construct();
        $this->import('BackendUser', 'User');
    }

    /**
     * Return the "toggle visibility" button
     *
     * @param array  $row
     * @param string $href
     * @param string $label
     * @param string $title
     * @param string $icon
     * @param string $attributes
     *
     * @return string
     */
    public function toggleIcon($row, $href, $label, $title, $icon, $attributes)
    {
        if (\strlen(Input::get('cid')))
        {
            $this->toggleVisibility(Input::get('cid'), (Input::get('state') == 1), (@func_get_arg(12) ?: null));
            $this->redirect($this->getReferer());
        }

        // Check permissions AFTER checking the cid, so hacking attempts are logged
        if (!$this->User->hasAccess('tl_mailchimp_campaign::published', 'alexf'))
        {
            return '';
        }

        $href .= '&amp;id='.Input::get('id').'&amp;cid='.$row['id'].'&amp;state='.$row['published'];

        if (!$row['published'])
        {
            $icon = 'invisible.svg';
        }

        return '<a href="'.$this->addToUrl($href).'" title="'.StringUtil::specialchars($title).'" data-tid="cid"'.$attributes.'>'.Image::getHtml($icon, $label, 'data-state="' . ($row['published'] ? 1 : 0) . '"').'</a> ';
    }

    /**
     * Toggle the visibility of an element
     *
     * @param integer       $intId
     * @param boolean       $blnVisible
     * @param DataContainer $dc
     *
     * @throws Contao\CoreBundle\Exception\AccessDeniedException
     */
    public function toggleVisibility($intId, $blnVisible, DataContainer $dc=null)
    {
        // Set the ID and action
        Input::setGet('id', $intId);
        Input::setGet('act', 'toggle');

        if ($dc)
        {
            $dc->id = $intId; // see #8043
        }

        // Trigger the onload_callback
        if (\is_array($GLOBALS['TL_DCA']['tl_mailchimp_campaign']['config']['onload_callback']))
        {
            foreach ($GLOBALS['TL_DCA']['tl_mailchimp_campaign']['config']['onload_callback'] as $callback)
            {
                if (\is_array($callback))
                {
                    $this->import($callback[0]);
                    $this->{$callback[0]}->{$callback[1]}($dc);
                }
                elseif (\is_callable($callback))
                {
                    $callback($dc);
                }
            }
        }

        // Check the field access
        if (!$this->User->hasAccess('tl_mailchimp_campaign::published', 'alexf'))
        {
            throw new Contao\CoreBundle\Exception\AccessDeniedException('Not enough permissions to show/hide Mailchimp campaign ID ' . $intId . '.');
        }

        // Set the current record
        if ($dc)
        {
            $objRow = $this->Database->prepare("SELECT * FROM tl_mailchimp_campaign WHERE id=?")
                ->limit(1)
                ->execute($intId);

            if ($objRow->numRows)
            {
                $dc->activeRecord = $objRow;
            }
        }

        $objVersions = new Versions('tl_mailchimp_campaign', $intId);
        $objVersions->initialize();

        // Trigger the save_callback
        if (\is_array($GLOBALS['TL_DCA']['tl_mailchimp_campaign']['fields']['published']['save_callback']))
        {
            foreach ($GLOBALS['TL_DCA']['tl_mailchimp_campaign']['fields']['published']['save_callback'] as $callback)
            {
                if (\is_array($callback))
                {
                    $this->import($callback[0]);
                    $blnVisible = $this->{$callback[0]}->{$callback[1]}($blnVisible, $dc);
                }
                elseif (\is_callable($callback))
                {
                    $blnVisible = $callback($blnVisible, $dc);
                }
            }
        }

        $time = time();

        // Update the database
        $this->Database->prepare("UPDATE tl_mailchimp_campaign SET tstamp=$time, published='" . ($blnVisible ? '1' : '') . "' WHERE id=?")
            ->execute($intId);

        if ($dc)
        {
            $dc->activeRecord->tstamp = $time;
            $dc->activeRecord->published = ($blnVisible ? '1' : '');
        }

        // Trigger the onsubmit_callback
        if (\is_array($GLOBALS['TL_DCA']['tl_mailchimp_campaign']['config']['onsubmit_callback']))
        {
            foreach ($GLOBALS['TL_DCA']['tl_mailchimp_campaign']['config']['onsubmit_callback'] as $callback)
            {
                if (\is_array($callback))
                {
                    $this->import($callback[0]);
                    $this->{$callback[0]}->{$callback[1]}($dc);
                }
                elseif (\is_callable($callback))
                {
                    $callback($dc);
                }
            }
        }

        $objVersions->create();
    }


    /**
     * Get MC lists
     * @return array
     */
    public function getMailChimpLists()
    {
        $arrLists = array();
        if (!\class_exists('\MailchimpAPI\Mailchimp'))
        {
            return array();
        }

        $objCampaign = MC_CampaignModel::findByPk(Input::get('id'));
        if ($objCampaign !== null)
        {
            // Get API Key data
            $objApiKey = MC_ApiKeyModel::findByPk($objCampaign->mc_api_key);
            if ($objApiKey === null)
            {
                return array();
            }

            try
            {
                $objMailchimp = new Mailchimp($objApiKey->api_key);
                $objResponse = $objMailchimp->lists()->get();

                if (!$objResponse->wasSuccess())
                {
                    System::log('MailChimp error: ' . $objResponse->getBody(), __METHOD__, TL_ERROR);
                }
                else
                {
                    $arrData = $objResponse->deserialize(true);

                    foreach ($arrData['lists'] as $list)
                    {
                        $arrLists[$list['id']] = $list['name'];
                    }
                }
            }
            catch (MailchimpException $e)
            {
                System::log('MailChimp error: ' . $e->getMessage(), __METHOD__, TL_ERROR);
            }
        }

        return $arrLists;
    }


    /**
     * Add/update a campaign in Mailchimp
     * @param null $dc
     */
    public function campaignSave($dc=null)
    {
        if (!\class_exists('\MailchimpAPI\Mailchimp'))
        {
            return;
        }

        $objCampaign = MC_CampaignModel::findByPk(Input::get('id'));
        if ($objCampaign !== null)
        {
            // Get API Key data
            $objApiKey = MC_ApiKeyModel::findByPk($objCampaign->mc_api_key);
            if ($objApiKey === null)
            {
                return;
            }

            try
            {
                $objResponse = null;
                $objMailchimp = new Mailchimp($objApiKey->api_key);

                // Create
                if (!$objCampaign->campaign_id)
                {
                    $objResponse = $objMailchimp
                        ->campaigns()
                        ->post(array(
                            'type' => 'regular',
                            'recipients' => array(
                                'list_id' => $objCampaign->mc_list
                            ),
                            'settings' => array(
                                'subject_line' => $objCampaign->mc_subject,
                                'preview_text' => $objCampaign->mc_preview_text,
                                'title' => $objCampaign->mc_title,
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
                        ));

                    if (!$objResponse->wasSuccess())
                    {
                        System::log('MailChimp error: ' . $objResponse->getBody(), __METHOD__, TL_ERROR);
                    }
                    else
                    {
                        $arrResponseData = json_decode($objResponse->getBody(), true);

                        // Save the campaign ID
                        $objCampaign->campaign_id = $arrResponseData['id'];
                        $objCampaign->web_id = $arrResponseData['web_id'];
                        $objCampaign->save();

                        $objResponse = $objMailchimp
                            ->campaigns($objCampaign->web_id)
                            ->content()
                            ->put(array(
                                'url' => Environment::get('url').'/mailchimp/campaign/'.$objCampaign->id
                            ));

                        if (!$objResponse->wasSuccess())
                        {
                            System::log('Mailchimp error: ' . $objResponse->getBody(), __METHOD__, TL_ERROR);
                        }
                        else
                        {
                            System::log('Mailchimp campaign created successfully: Contao ID = ' . $objCampaign->id . '; Mailchimp ID = ' . $objCampaign->campaign_id . ';', __METHOD__, TL_GENERAL);
                        }
                    }
                }
            }
            catch (MailchimpException $e)
            {
                System::log('Mailchimp error: ' . $e->getMessage(), __METHOD__, TL_ERROR);
            }
        }
    }

}