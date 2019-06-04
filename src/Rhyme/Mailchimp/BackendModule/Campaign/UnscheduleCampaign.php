<?php

/**
 * Copyright (C) 2019 Rhyme Digital, LLC.
 *
 * @link       https://rhyme.digital
 * @license    http://www.gnu.org/licenses/lgpl-3.0.html LGPL
 */

namespace Rhyme\Mailchimp\BackendModule\Campaign;

use Contao\Date;
use Contao\Input;
use Contao\Image;
use Contao\Config;
use Contao\System;
use Contao\TextField;
use Contao\SelectMenu;
use Contao\Controller;
use Contao\StringUtil;
use Contao\BackendModule;

use MailchimpAPI\Mailchimp;
use MailchimpAPI\Responses\MailchimpResponse;
use MailchimpAPI\MailchimpException;

use Rhyme\Mailchimp\BackendModule\Campaign as BaseModule;
use Rhyme\Mailchimp\Model\ApiKey as MC_ApiKeyModel;
use Rhyme\Mailchimp\Model\Campaign as MC_CampaignModel;


/**
 * Class UnscheduleCampaign
 * @package Rhyme\Mailchimp\BackendModule\Campaign
 */
class UnscheduleCampaign extends BaseModule
{
    /**
     * Template
     * @var string
     */
    protected $strTemplate = 'be_mod_mailchimp_unschedule';

    /**
     * Form ID
     * @var string
     */
    protected static $strFormId = 'tl_unschedule_mc_campaign';

    /**
     * Compile
     */
    protected function compile()
    {
        parent::compile();

        // Temporary!!
        $objMailchimp = new Mailchimp($this->objApiKey->api_key);
        $objResponse = $objMailchimp
            ->campaigns($this->objCampaign->campaign_id)
            ->unschedule();
        return;

        $strSend = $this->objCampaign->send_tstamp ? Date::parse(Config::get('datimFormat'), $this->objCampaign->send_tstamp) : '';

        if ($this->objCampaign->send_tstamp ||
            $strSend ||
            in_array($this->objCampaign->status, array('scheduled', 'paused', 'sent')))
        {
            $this->Template->info = sprintf($GLOBALS['TL_LANG']['MSC']['mailchimp_email_scheduled_for'], $strSend);
            return;
        }

        $arrDateData = array(
            'label'                   => &$GLOBALS['TL_LANG']['MSC']['mailchimp_email_date'],
            'name'                    => 'mailchimp_schedule',
            'attributes'              => array(' onkeydown="return false"'),
            'eval'                    => array('rgxp'=>'datim', 'datepicker'=>true, 'tl_class'=>'w50 wizard', 'onkeydown'=>'return false;', 'onfocus'=>'this.blur(); return false;'),
        );
        $objDate = new TextField(TextField::getAttributesFromDca($arrDateData, $arrDateData['name'], $strSend, $arrDateData['name'], '', $this));


        // Handle submission
        if (Input::post('FORM_SUBMIT') == static::$strFormId)
        {
            $objDate->validate();

            if (!$objDate->hasErrors())
            {
                // Need to convert to UTC
                $strUTC = gmdate('c', strtotime($objDate->value));

                try
                {
                    $objMailchimp = new Mailchimp($this->objApiKey->api_key);

                    $objResponse = $objMailchimp
                        ->campaigns($this->objCampaign->campaign_id)
                        ->schedule($strUTC);

                    if (!$objResponse->wasSuccess())
                    {
                        System::log('Mailchimp error: ' . $objResponse->getBody(), __METHOD__, TL_ERROR);
                        $arrBody = json_decode($objResponse->getBody(), true);
                        throw new \Exception('Mailchimp error: ' . $arrBody['detail']);
                    }
                    else
                    {
                        $this->objCampaign->send_tstamp = strtotime($objDate->value);
                        $this->objCampaign->status = 'scheduled';
                        $this->objCampaign->save();

                        $this->Template->confirm = $GLOBALS['TL_LANG']['MSC']['mailchimp_email_scheduled'];
                        System::log($GLOBALS['TL_LANG']['MSC']['mailchimp_email_test_sent'].': Contao ID = ' . $this->objCampaign->id . '; Mailchimp ID = ' . $this->objCampaign->campaign_id . ';', __METHOD__, TL_GENERAL);
                    }
                }
                catch (\Exception $e)
                {
                    System::log($e->getMessage(), __METHOD__, TL_ERROR);
                    $this->Template->errors = $e->getMessage();
                }
            }
        }

        $this->Template->date = $objDate->parse().$wizard;
        $this->Template->submitLabel = $GLOBALS['TL_LANG']['MSC']['mailchimp_email_schedule_submit'];
        $this->Template->formId = static::$strFormId;
    }
}