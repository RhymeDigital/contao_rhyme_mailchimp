<?php

/**
 * Copyright (C) 2019 Rhyme Digital, LLC.
 *
 * @link		http://rhyme.digital
 * @license		http://www.gnu.org/licenses/lgpl-3.0.html LGPL
 */

namespace Rhyme\Mailchimp\Hooks\ProcessFormData;

use Contao\Input;
use Contao\System;
use Contao\Database;
use Contao\Frontend;
use Contao\StringUtil;
use MailchimpAPI\Mailchimp;
use MailchimpAPI\Responses\MailchimpResponse;
use MailchimpAPI\MailchimpException;

/**
 * Class SendDataToMailchimp
 * @package Rhyme\Mailchimp\Hooks
 */
class SendDataToMailchimp extends Frontend
{

    /**
     * This will send the data to MailChimp
     *
     * @param	array $arrFormData
     * @param	array $arrFormInfo
     * @param	array $arrFiles
     * @param	array $arrLabels
     */
    public function run(&$arrFormData, &$arrFormInfo, &$arrFiles, &$arrLabels)
    {
        if ($arrFormInfo['useMailChimp'] && $arrFormInfo['mailChimpAPIKey'])
        {
            $strMethod = $arrFormInfo['method'] == 'POST' ? 'post' : 'get';

            // Get API Key data
            $objResult = Database::getInstance()->prepare("SELECT * FROM tl_mailchimp_apikeys WHERE id=?")->execute($arrFormInfo['mailChimpAPIKey']);
            if ($objResult->numRows == 0)
            {
                return;
            }

            try
            {
                $objMailchimp = new Mailchimp($objResult->api_key);

                if (\class_exists('\Contao\StringUtil'))
                {
                    $arrLists = StringUtil::deserialize($arrFormInfo['mailChimpLists'], true);
                }
                else
                {
                    $arrLists = \deserialize($arrFormInfo['mailChimpLists'], true);
                }

                if (count($arrLists))
                {
                    foreach ($arrLists as $list)
                    {
                        $arrMergeVars = array();

                        if (Input::$strMethod($arrFormInfo['mailchimp_firstname']))
                        {
                            $arrMergeVars['FNAME'] = Input::$strMethod($arrFormInfo['mailchimp_firstname']);
                        }
                        if (Input::$strMethod($arrFormInfo['mailchimp_lastname']))
                        {
                            $arrMergeVars['LNAME'] = Input::$strMethod($arrFormInfo['mailchimp_lastname']);
                        }

                        $email_type = 'html';
                        $strEmail = Input::$strMethod($arrFormInfo['mailchimp_email']);

                        // Allow handling of other fields
                        if (isset($GLOBALS['TL_HOOKS']['mailchimp_fields']) && is_array($GLOBALS['TL_HOOKS']['mailchimp_fields']))
                        {
                            foreach ($GLOBALS['TL_HOOKS']['mailchimp_fields'] as $callback)
                            {
                                $this->import($callback[0]);
                                list($list, $strEmail, $merge_vars) = $this->{$callback[0]}->{$callback[1]}($list, $strEmail, $arrMergeVars, $arrFormData, $arrFormInfo, $arrFiles, $arrLabels, $email_type);
                            }
                        }

                        $arrPOST = array
                        (
                            'email_address'         => $strEmail,
                            'email_type'            => $email_type,
                            'status'                => 'pending',
                        );

                        if (!empty($arrMergeVars))
                        {
                            $arrPOST['merge_fields'] = $arrMergeVars;
                        }

                        $objResponse = $objMailchimp->lists($list)->members()->post($arrPOST);

                        if (!$objResponse->wasSuccess())
                        {
                            System::log('MailChimp error: ' . $objResponse->getBody(), __METHOD__, TL_ERROR);
                        }
                        else
                        {
                            System::log("Subscribed - look for the confirmation email!", __METHOD__, TL_GENERAL);
                        }
                    }
                }
            }
            catch (\Exception $e)
            {
                System::log('MailChimp error: ' . $e->getMessage(), __METHOD__, TL_ERROR);
            }
        }
    }
}