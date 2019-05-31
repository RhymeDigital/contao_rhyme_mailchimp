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
 * Class ApiKey
 * @package Rhyme\Mailchimp\Model
 */
class ApiKey extends Model
{

    /**
     * Table name
     * @var string
     */
    protected static $strTable = 'tl_mailchimp_apikeys';


}