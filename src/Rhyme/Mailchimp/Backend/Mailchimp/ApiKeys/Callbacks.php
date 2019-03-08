<?php

/**
 * Copyright (C) 2019 Rhyme Digital, LLC.
 *
 * @link		http://rhyme.digital
 * @license		http://www.gnu.org/licenses/lgpl-3.0.html LGPL
 */

namespace Rhyme\Mailchimp\Backend\Mailchimp\ApiKeys;

use Contao\Backend;
use Contao\Database;
use Contao\DataContainer;

/**
 * Class Callbacks
 * @package Rhyme\Mailchimp\Backend\Mailchimp\ApiKeys
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
     * Auto-generate the news alias if it has not been set yet
     * @param mixed $varValue
     * @param DataContainer $dc
     * @return string
     * @throws \Exception
     */
    public function generateAlias($varValue, DataContainer $dc)
    {
        $autoAlias = false;

        // Generate alias if there is none
        if (!strlen($varValue))
        {
            $autoAlias = true;
            $varValue = standardize($dc->activeRecord->name);
        }

        $objAlias = Database::getInstance()->prepare("SELECT id FROM tl_mailchimp_apikeys WHERE alias=?")
            ->execute($varValue);

        // Check whether the news alias exists
        if ($objAlias->numRows > 1 && !$autoAlias)
        {
            throw new \Exception(sprintf($GLOBALS['TL_LANG']['ERR']['aliasExists'], $varValue));
        }

        // Add ID to alias
        if ($objAlias->numRows && $autoAlias)
        {
            $varValue .= '-' . $dc->id;
        }

        return $varValue;
    }
}