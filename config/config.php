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
	'mailchimp' => array(
		'mailchimp_apikeys' => array
		(
			'tables' => array('tl_mailchimp_apikeys'),
			'icon'   => 'system/modules/rhyme_mailchimp/assets/img/mailchimp-icon.png'
		)
	)
));

/**
 * Hooks
 */
$GLOBALS['TL_HOOKS']['processFormData'][]				= array('Rhyme\Mailchimp\Hooks\ProcessFormData\SendDataToMailchimp', 'run');
