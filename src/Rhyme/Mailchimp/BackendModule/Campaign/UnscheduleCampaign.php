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
use Contao\CheckBox;
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
use Rhyme\Mailchimp\Frontend\Controller\CampaignHandler;


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

        if (!$this->objCampaign->canUnschedule())
        {
            $this->Template->info = $GLOBALS['TL_LANG']['MSC']['mailchimp_email_unscheduled'];
            return;
        }

        $arrConfirmData = array(
            'label'                   => &$GLOBALS['TL_LANG']['MSC']['mailchimp_email_confirm_unschedule'],
            'name'                    => 'mailchimp_unschedule',
            'options'                 => array('1'=>$GLOBALS['TL_LANG']['MSC']['mailchimp_email_confirm_unschedule'][0]),
            'eval'                    => array('tl_class'=>'clr m12', 'mandatory'=>true),
        );
        $objCheckbox = new CheckBox(CheckBox::getAttributesFromDca($arrConfirmData, $arrConfirmData['name'], '', $arrConfirmData['name'], '', $this));


        // Handle submission
        if (Input::post('FORM_SUBMIT') == static::$strFormId)
        {
            $objCheckbox->validate();

            if (!$objCheckbox->hasErrors())
            {
                try
                {
                    $objMailchimp = new Mailchimp($this->objApiKey->api_key);

                    CampaignHandler::unscheduleMailchimpCampaign($objMailchimp, $this->objCampaign);

                    $this->Template->confirm = $GLOBALS['TL_LANG']['MSC']['mailchimp_email_unscheduled'];
                    System::log($GLOBALS['TL_LANG']['MSC']['mailchimp_email_unscheduled'].': Contao ID = ' . $this->objCampaign->id . '; Mailchimp ID = ' . $this->objCampaign->campaign_id . ';', __METHOD__, TL_GENERAL);
                }
                catch (\Exception $e)
                {
                    System::log($e->getMessage(), __METHOD__, TL_ERROR);
                    $this->Template->errors = $e->getMessage();
                }
            }
        }

        if (!$this->Template->confirm)
        {
            $this->Template->checkbox = $objCheckbox->parse();
            $this->Template->submitLabel = $GLOBALS['TL_LANG']['MSC']['mailchimp_email_unschedule_submit'];
            $this->Template->formId = static::$strFormId;
        }
    }
}