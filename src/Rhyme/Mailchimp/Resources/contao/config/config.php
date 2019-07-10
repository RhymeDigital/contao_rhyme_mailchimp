<?php

/**
 * Copyright (C) 2019 Rhyme Digital, LLC.
 * 
 * @link		http://rhyme.digital
 * @license		http://www.gnu.org/licenses/lgpl-3.0.html LGPL
 */

/**
 * Backend scripts
 */
if (TL_MODE === 'BE'){

    $GLOBALS['TL_CSS'] = is_array($GLOBALS['TL_CSS']) ? $GLOBALS['TL_CSS'] : array();
    array_insert($GLOBALS['TL_CSS'], 9999, array(
        'bundles/rhymemailchimp/assets/css/be_styles.css',
    ));
}

/**
 * Back end modules
 */
array_insert($GLOBALS['BE_MOD'], 1, array(
	'mailchimp' => array
    (
        'mailchimp_campaigns' => array
        (
            'tables'        => array('tl_mailchimp_campaign', 'tl_content'),
            'test'          => array('Rhyme\Mailchimp\BackendModule\Campaign\SendTest', 'generate'),
            'schedule'      => array('Rhyme\Mailchimp\BackendModule\Campaign\ScheduleCampaign', 'generate'),
            'unschedule'    => array('Rhyme\Mailchimp\BackendModule\Campaign\UnscheduleCampaign', 'generate'),
        ),
        'mailchimp_apikeys' => array
        (
            'tables'    => array('tl_mailchimp_apikeys'),
        ),
	)
));


/**
 * Hooks
 */
$GLOBALS['TL_HOOKS']['processFormData'][]				= array('Rhyme\Mailchimp\Hooks\ProcessFormData\SendDataToMailchimp', 'run');


/**
 * Models
 */
$GLOBALS['TL_MODELS']['tl_mailchimp_apikeys']           = 'Rhyme\Mailchimp\Model\ApiKey';
$GLOBALS['TL_MODELS']['tl_mailchimp_campaign']			= 'Rhyme\Mailchimp\Model\Campaign';
