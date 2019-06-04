<?php

/**
 * Copyright (C) 2019 Rhyme Digital, LLC.
 *
 * @link       https://rhyme.digital
 * @license    http://www.gnu.org/licenses/lgpl-3.0.html LGPL
 */

namespace Rhyme\Mailchimp\BackendModule;

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
 * Class Campaign
 * @package Rhyme\Mailchimp\BackendModule\Campaign
 */
class Campaign extends BackendModule
{
    /**
     * Campaign model
     * @var MC_CampaignModel
     */
    protected $objCampaign;

    /**
     * API key model
     * @var MC_ApiKeyModel
     */
    protected $objApiKey;

    /**
     * Compile
     */
    protected function compile()
    {
        if (!Input::get('id'))
        {
            Controller::redirect('contao?do=mailchimp_campaigns');
        }

        // Get campaign
        $this->objCampaign = MC_CampaignModel::findByPk(Input::get('id'));
        if ($this->objCampaign === null)
        {
            $this->Template->errors = 'Missing campaign ID.';
            return;
        }

        // Get API key model
        $this->objApiKey = MC_ApiKeyModel::findByPk($this->objCampaign->mc_api_key);
        if ($this->objApiKey === null)
        {
            $this->Template->errors = 'Missing Mailchimp API key configuration.';
            return;
        }

        $this->Template->campaign = $this->objCampaign;
    }
}