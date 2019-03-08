<?php

/**
 * Copyright (C) 2019 Rhyme Digital, LLC.
 *
 * @link		http://rhyme.digital
 * @license		http://www.gnu.org/licenses/lgpl-3.0.html LGPL
 */

namespace Rhyme\Mailchimp\Backend\Form;

use Contao\Input;
use Contao\System;
use Contao\Backend;
use Contao\Database;
use MailchimpAPI\Mailchimp;
use MailchimpAPI\Responses\MailchimpResponse;
use MailchimpAPI\MailchimpException;

/**
 * Class Callbacks
 * @package Rhyme\Mailchimp\Backend\Form
 */
class Callbacks extends Backend
{

    /**
     * Get MC API Keys
     * @return array
     */
    public function getMailChimpAPIKeys()
    {
        $arrKeys = array('-- Please select --');

        $objResult = Database::getInstance()->prepare("SELECT * FROM tl_mailchimp_apikeys")->execute();

        if ($objResult->numRows)
        {
            while ($objResult->next())
            {
                $arrKeys[$objResult->id] = $objResult->name;
            }
        }

        return $arrKeys;
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
            return $arrLists;
        }

        $objForm = Database::getInstance()->prepare("SELECT * FROM tl_form WHERE id=?")->execute(Input::get('id'));

        if ($objForm->numRows)
        {
            // Get API Key data
            $objResult = Database::getInstance()->prepare("SELECT * FROM tl_mailchimp_apikeys WHERE id=?")->execute($objForm->mailChimpAPIKey);
            if ($objResult->numRows == 0)
            {
                return array();
            }

            try
            {
                $objMailchimp = new Mailchimp($objResult->api_key);
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
     * Return all usable fields as array
     * @return array
     */
    public function getUsableFormFields()
    {
        $fields = array();

        // Get all form fields which can be used for MailChimp values
        $objFields = Database::getInstance()->prepare("SELECT id,name,label FROM tl_form_field WHERE pid=? AND (type=? OR type=? OR type=? OR type=? OR type=?) ORDER BY name ASC")
            ->execute(Input::get('id'), 'text', 'hidden', 'select', 'radio', 'checkbox');

        $fields[] = '-';
        while ($objFields->next())
        {
            $k = $objFields->name;
            $v = $objFields->label;
            $v = strlen($v) ? $v.' ['.$k.']' : $k;
            $fields[$k] =$v;
        }

        return $fields;
    }
}


