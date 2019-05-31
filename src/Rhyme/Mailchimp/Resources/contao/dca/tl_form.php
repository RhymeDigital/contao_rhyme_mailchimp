<?php

/**
 * Copyright (C) 2019 Rhyme Digital, LLC.
 * 
 * @link		http://rhyme.digital
 * @license		http://www.gnu.org/licenses/lgpl-3.0.html LGPL
 */


/**
 * Palettes
 */
$GLOBALS['TL_DCA']['tl_form']['palettes']['__selector__'][]		= 'useMailChimp';
$GLOBALS['TL_DCA']['tl_form']['palettes']['default'] = str_replace('{expert_legend', '{mailchimp_legend:hide},useMailChimp;{expert_legend', $GLOBALS['TL_DCA']['tl_form']['palettes']['default']);


/**
 * Subpalettes
 */
$GLOBALS['TL_DCA']['tl_form']['subpalettes']['useMailChimp'] = 'mailChimpAPIKey,mailChimpLists,mailchimp_email,mailchimp_firstname,mailchimp_lastname'; 


/**
 * Fields
 */
$GLOBALS['TL_DCA']['tl_form']['fields']['useMailChimp'] = array
(
	'label'                   => &$GLOBALS['TL_LANG']['tl_form']['useMailChimp'],
	'exclude'                 => true,
	'inputType'               => 'checkbox',
	'eval'                    => array('tl_class'=>'clr', 'submitOnChange'=>true),
	'sql'					  => "char(1) NOT NULL default ''"
);
$GLOBALS['TL_DCA']['tl_form']['fields']['mailChimpAPIKey'] = array
(
	'label'                   => &$GLOBALS['TL_LANG']['tl_form']['mailChimpAPIKey'],
	'exclude'                 => true,
	'inputType'               => 'select',
	'eval'                    => array('tl_class'=>'clr w50', 'submitOnChange'=>true, 'includeBlankOption'=>true),
	'foreignKey'		      => 'tl_mailchimp_apikeys.name',
	'sql'					  => "int(10) NOT NULL default '0'"
);
$GLOBALS['TL_DCA']['tl_form']['fields']['mailChimpLists'] = array
(
	'label'                   => &$GLOBALS['TL_LANG']['tl_form']['mailChimpLists'],
	'exclude'                 => true,
	'inputType'               => 'checkbox',
	'eval'                    => array('tl_class'=>'clr', 'multiple'=>true),
	'options_callback'		  => array('Rhyme\Mailchimp\Backend\Form\Callbacks', 'getMailChimpLists'),
	'sql'					  => "blob NULL"
);
$GLOBALS['TL_DCA']['tl_form']['fields']['mailchimp_email'] = array
(
	'label'                   => &$GLOBALS['TL_LANG']['tl_form']['mailchimp_email'],
	'exclude'                 => true,
	'filter'                  => false,
	'inputType'               => 'select',
	'options_callback'        => array('Rhyme\Mailchimp\Backend\Form\Callbacks', 'getUsableFormFields'),
	'eval'                    => array('mandatory'=>true, 'tl_class'=>'w50 clr'),
	'mailChimpFieldName'	  => 'EMAIL',
	'sql'					  => "varchar(255) NOT NULL default ''"
);
$GLOBALS['TL_DCA']['tl_form']['fields']['mailchimp_firstname'] = array
(
	'label'                   => &$GLOBALS['TL_LANG']['tl_form']['mailchimp_firstname'],
	'exclude'                 => true,
	'filter'                  => false,
	'inputType'               => 'select',
	'options_callback'        => array('Rhyme\Mailchimp\Backend\Form\Callbacks', 'getUsableFormFields'),
	'eval'                    => array('mandatory'=>false, 'tl_class'=>'w50'),
	'mailChimpFieldName'	  => 'FNAME',
	'sql'					  => "varchar(255) NOT NULL default ''"
);
$GLOBALS['TL_DCA']['tl_form']['fields']['mailchimp_lastname'] = array
(
	'label'                   => &$GLOBALS['TL_LANG']['tl_form']['mailchimp_lastname'],
	'exclude'                 => true,
	'filter'                  => false,
	'inputType'               => 'select',
	'options_callback'        => array('Rhyme\Mailchimp\Backend\Form\Callbacks', 'getUsableFormFields'),
	'eval'                    => array('mandatory'=>false, 'tl_class'=>'w50'),
	'mailChimpFieldName'	  => 'LNAME',
	'sql'					  => "varchar(255) NOT NULL default ''"
);




