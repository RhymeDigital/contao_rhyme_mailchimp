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
use Rhyme\Mailchimp\Frontend\Controller\CampaignHandler;


/**
 * Class ScheduleCampaign
 * @package Rhyme\Mailchimp\BackendModule\Campaign
 */
class ScheduleCampaign extends BaseModule
{
    /**
     * Template
     * @var string
     */
    protected $strTemplate = 'be_mod_mailchimp_schedule';

    /**
     * Form ID
     * @var string
     */
    protected static $strFormId = 'tl_schedule_mc_campaign';

    /**
     * Compile
     */
    protected function compile()
    {
        parent::compile();

        if (!$this->objCampaign->canSchedule())
        {
            $this->Template->info = $GLOBALS['TL_LANG']['MSC']['mailchimp_email_scheduled'];
            return;
        }

        $strSend = Date::parse(Config::get('datimFormat'), strtotime($this->objCampaign->getSendTime()));

        $arrDateData = array(
            'label'                   => &$GLOBALS['TL_LANG']['MSC']['mailchimp_email_date'],
            'name'                    => 'mailchimp_schedule',
            'attributes'              => array(' onkeydown="return false"'),
            'eval'                    => array('rgxp'=>'datim', 'datepicker'=>true, 'tl_class'=>'w50 wizard', 'onkeydown'=>'return false;', 'onfocus'=>'this.blur(); return false;'),
        );
        $objDate = new TextField(TextField::getAttributesFromDca($arrDateData, $arrDateData['name'], $strSend, $arrDateData['name'], '', $this));

        // Set up the date/time picker
        $rgxp = $arrDateData['eval']['rgxp'];
        $format = Date::formatToJs(Config::get($rgxp.'Format'));
        $time = ",\n        timePicker: true";

        $wizard = ' ' . Image::getHtml('assets/datepicker/images/icon.svg', '', 'title="'.StringUtil::specialchars($GLOBALS['TL_LANG']['MSC']['datepicker']).'" id="toggle_' . $objDate->id . '" style="cursor:pointer"') . '
  <script>
    window.addEvent("domready", function() {
      new Picker.Date($("ctrl_' . $objDate->id . '"), {
        draggable: false,
        toggle: $("toggle_' . $objDate->id . '"),
        format: "' . $format . '",
        timeWheelStep: 15,
        positionOffset: {x:-211,y:-209}' . $time . ',
        pickerClass: "datepicker_bootstrap",
        useFadeInOut: !Browser.ie' . '' . ',
        startDay: ' . $GLOBALS['TL_LANG']['MSC']['weekOffset'] . ',
        titleFormat: "' . $GLOBALS['TL_LANG']['MSC']['titleFormat'] . '"
      });
    });
  </script>';

        // Handle submission
        if (Input::post('FORM_SUBMIT') == static::$strFormId)
        {
            // Fix for dumb js values
            Input::setPost('mailchimp_schedule', str_replace('  ', ' ', Input::post('mailchimp_schedule')));

            $objDate->validate();

            if (strtotime($objDate->value) <= time() + 60)
            {
                $objDate->addError(sprintf($GLOBALS['TL_LANG']['ERR']['invalidDate'], $objDate->value));
            }

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
                        $this->Template->confirm = $GLOBALS['TL_LANG']['MSC']['mailchimp_email_scheduled'];
                        System::log($GLOBALS['TL_LANG']['MSC']['mailchimp_email_scheduled'].': Contao ID = ' . $this->objCampaign->id . '; Mailchimp ID = ' . $this->objCampaign->campaign_id . ';', __METHOD__, TL_GENERAL);
                    }
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
            $this->Template->date = $objDate->parse().$wizard;
            $this->Template->submitLabel = $GLOBALS['TL_LANG']['MSC']['mailchimp_email_schedule_submit'];
            $this->Template->formId = static::$strFormId;
        }
    }
}