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
use Contao\ModuleModel;
use Contao\FrontendTemplate;
use Contao\Model\Collection;

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