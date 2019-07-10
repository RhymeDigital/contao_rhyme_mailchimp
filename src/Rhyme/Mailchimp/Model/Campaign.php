<?php

/**
 * Copyright (C) 2019 Rhyme Digital, LLC.
 *
 * @link       https://rhyme.digital
 * @license    http://www.gnu.org/licenses/lgpl-3.0.html LGPL
 */

namespace Rhyme\Mailchimp\Model;

use Contao\Date;
use Contao\Model;
use Contao\System;
use Contao\ModuleModel;
use Contao\FrontendTemplate;
use Contao\Model\Collection;

use MailchimpAPI\Mailchimp;
use MailchimpAPI\Responses\MailchimpResponse;


/**
 * Class Campaign
 * @package Rhyme\Mailchimp\Model
 */
class Campaign extends Model
{

    /**
     * Table name
     * @var string
     */
    protected static $strTable = 'tl_mailchimp_campaign';

    /**
     * Mailchimp object
     * @var Mailchimp
     */
    protected $objMailchimp;

    /**
     * Response cache object
     * @var MailchimpResponse
     */
    protected $objResponse;


    /**
     * Can this campaign be sent as a test?
     * @return boolean
     */
    public function canSendTest()
    {
        return !in_array($this->getStatus(), array('sent', 'sending', 'schedule'));
    }


    /**
     * Can this campaign be scheduled
     * @todo: adjust buffer and timezone
     * @return boolean
     */
    public function canSchedule()
    {
        $strSendTime = $this->getSendTime();
        return !in_array($this->getStatus(), array('sent', 'sending', 'schedule')) && (!$strSendTime || strtotime($strSendTime) < time());
    }


    /**
     * Can this campaign be unscheduled
     * @todo: adjust buffer and timezone
     * @return boolean
     */
    public function canUnschedule()
    {
        $strSendTime = $this->getSendTime();
        return $this->getStatus() == 'schedule' && $strSendTime && strtotime($strSendTime) > time();
    }


    /**
     * Get response data from API
     * @param $blnNoCache boolean
     * @return array
     */
    public function getApiData($blnNoCache=false)
    {
        $arrData = array();

        if (!\class_exists('\MailchimpAPI\Mailchimp'))
        {
            return $arrData;
        }

        try
        {
            if ($this->objResponse === null || $blnNoCache)
            {
                $this->objMailchimp = $this->objMailchimp !== null && !$blnNoCache ?
                    $this->objMailchimp :
                    new Mailchimp($this->getRelated('mc_api_key')->api_key);

                $this->objResponse = $this->objMailchimp
                    ->campaigns($this->campaign_id)
                    ->get();
            }

            if ($this->objResponse->wasSuccess())
            {
                $arrData = json_decode($this->objResponse->getBody(), true);
            }
        }
        catch (\Exception $e)
        {
            System::log('Error while attempting to get campaign data from API: Contao ID = ' . $this->id . '; Mailchimp ID = ' . $this->campaign_id . '; Message = ' . $e->getMessage() . ';', __METHOD__, TL_ERROR);
        }

        return $arrData;
    }


    /**
     * Get status from API
     * @param $blnNoCache boolean
     * @return string
     */
    public function getStatus($blnNoCache=false)
    {
        $strStatus = 'unknown';

        try
        {
            $arrData = $this->getApiData($blnNoCache);
            $strStatus = $arrData['status'] ?: $strStatus;
        }
        catch (\Exception $e)
        {
            System::log('Error while attempting to get campaign status: Contao ID = ' . $this->id . '; Mailchimp ID = ' . $this->campaign_id . '; Message = ' . $e->getMessage() . ';', __METHOD__, TL_ERROR);
        }

        return $strStatus;
    }


    /**
     * Get send_time from API
     * @param $blnNoCache boolean
     * @return string
     */
    public function getSendTime($blnNoCache=false)
    {
        $strSendTime = '';

        try
        {
            $arrData = $this->getApiData($blnNoCache);
            $strSendTime = $arrData['send_time'] ?: $strSendTime;
        }
        catch (\Exception $e)
        {
            System::log('Error while attempting to get campaign send_time: Contao ID = ' . $this->id . '; Mailchimp ID = ' . $this->campaign_id . '; Message = ' . $e->getMessage() . ';', __METHOD__, TL_ERROR);
        }

        return $strSendTime;
    }


    /**
     * Find all published
     * @param array $arrOptions An optional options array
     *
     * @return \Model|Collection|null The model or null if there is none
     */
    public static function findAllPublished(array $arrOptions=array())
    {
        $t = static::$strTable;

        $time = Date::floorToMinute();
        $arrColumns = array("$t.published='1'");

        return static::findBy($arrColumns, array(), $arrOptions);
    }


    /**
     * Find a published campaign by its ID or alias
     *
     * @param mixed $intId      The numeric ID
     * @param array $arrOptions An optional options array
     *
     * @return static|null The model or null if there is no campaign
     */
    public static function findPublishedById($intId, array $arrOptions=array())
    {
        $t = static::$strTable;
        $arrColumns = array("$t.id=?");

        if (!static::isPreviewMode($arrOptions))
        {
            $arrColumns[] = "$t.published='1'";
        }

        return static::findOneBy($arrColumns, $intId, $arrOptions);
    }


    /**
     * Find a published campaign by its Mailchimp campaign ID
     *
     * @param string $strCampaignId The Mailchimp campaign ID
     * @param array $arrOptions An optional options array
     *
     * @return static|null The model or null if there is no campaign
     */
    public static function findPublishedByCampaignId($strCampaignId, array $arrOptions=array())
    {
        $strCampaignId = trim($strCampaignId);

        if (trim($strCampaignId) == '')
        {
            return null;
        }

        $t = static::$strTable;
        $arrColumns = array("$t.campaign_id=?");

        if (!static::isPreviewMode($arrOptions))
        {
            $arrColumns[] = "$t.published='1'";
        }

        return static::findOneBy($arrColumns, $strCampaignId, $arrOptions);
    }


}