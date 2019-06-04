<?php

/**
 * Copyright (C) 2019 Rhyme Digital, LLC.
 *
 * @link       https://rhyme.digital
 * @license    http://www.gnu.org/licenses/lgpl-3.0.html LGPL
 */

namespace Rhyme\Mailchimp\BackendModule\Campaign;

use Contao\Input;
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
 * Class SendTest
 * @package Rhyme\Mailchimp\BackendModule\Campaign
 */
class SendTest extends BaseModule
{
    /**
     * Template
     * @var string
     */
    protected $strTemplate = 'be_mod_mailchimp_test';

    /**
     * Form ID
     * @var string
     */
    protected static $strFormId = 'tl_send_mc_test';

    /**
     * Compile
     */
    protected function compile()
    {
        parent::compile();

        $arrEmailsData = array(
            'label'                   => &$GLOBALS['TL_LANG']['MSC']['mailchimp_email_test_recipients'],
			'name'                    => 'mailchimp_email_recipients',
			'eval'                    => array('mandatory'=>true, 'maxlength'=>1022, 'rgxp'=>'emails', 'tl_class'=>'w50'),
        );
        $objEmails = new TextField(TextField::getAttributesFromDca($arrEmailsData, $arrEmailsData['name'], '', $arrEmailsData['name'], '', $this));

        $arrTypesData = array(
            'label'                   => &$GLOBALS['TL_LANG']['MSC']['mailchimp_email_test_type'],
            'name'                    => 'mailchimp_email_types',
            'reference'               => $GLOBALS['TL_LANG']['MSC']['mailchimp_email_test_types'],
            'options'                 => array_keys($GLOBALS['TL_LANG']['MSC']['mailchimp_email_test_types']),
            'eval'                    => array('mandatory'=>true, 'maxlength'=>1022, 'tl_class'=>'w50'),
        );
        $objTypes = new SelectMenu(SelectMenu::getAttributesFromDca($arrTypesData, $arrTypesData['name'], '', $arrTypesData['name'], '', $this));

        // Handle submission
        if (Input::post('FORM_SUBMIT') == static::$strFormId)
        {
            $objEmails->validate();
            $objTypes->validate();

            if (!$objEmails->hasErrors() && !$objTypes->hasErrors())
            {

                try
                {
                    $arrEmails = StringUtil::trimsplit(',', $objEmails->value);
                    $objMailchimp = new Mailchimp($this->objApiKey->api_key);

                    $objResponse = $objMailchimp
                        ->campaigns($this->objCampaign->campaign_id)
                        ->test($arrEmails, $objTypes->value);

                    if (!$objResponse->wasSuccess())
                    {
                        System::log('Mailchimp error: ' . $objResponse->getBody(), __METHOD__, TL_ERROR);
                        $arrBody = json_decode($objResponse->getBody(), true);
                        throw new \Exception('Mailchimp error: ' . $arrBody['detail']);
                    }
                    else
                    {
                        $this->Template->confirm = $GLOBALS['TL_LANG']['MSC']['mailchimp_email_test_sent'];
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

        $this->Template->emails = $objEmails->parse();
        $this->Template->types = $objTypes->parse();
        $this->Template->submitLabel = $GLOBALS['TL_LANG']['MSC']['mailchimp_email_test_send'];
        $this->Template->formId = static::$strFormId;
    }
}