<?php

/**
 * Copyright (C) 2019 Rhyme Digital, LLC.
 * 
 * @link		http://rhyme.digital
 * @license		http://www.gnu.org/licenses/lgpl-3.0.html LGPL
 */


/**
 * Back end modules
 */
array_insert($GLOBALS['BE_MOD'], 1, array(
	'mailchimp' => array
    (
		'mailchimp_apikeys' => array
		(
			'tables' => array('tl_mailchimp_apikeys'),
		),
        'mailchimp_campaigns' => array
        (
            'tables' => array('tl_mailchimp_campaign', 'tl_content'),
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
