<?php

/**
 * Copyright (C) 2019 Rhyme Digital, LLC.
 * 
 * @link		http://rhyme.digital
 * @license		http://www.gnu.org/licenses/lgpl-3.0.html LGPL
 */

use Contao\Controller;

/**
 * Load tl_content language file
 */
Controller::loadLanguageFile('tl_content');


/**
 * Table tl_mailchimp_apikeys
 */
$GLOBALS['TL_DCA']['tl_mailchimp_apikeys'] = array
(

	// ConfigÅ
	'config' => array
	(
		'dataContainer'               => 'Table',
		'enableVersioning'            => true,
		'sql' => array
		(
			'keys' => array
			(
				'id' => 'primary',
				'alias' => 'index'
			)
		)
	),

	// List
	'list' => array
	(
		'sorting' => array
		(
			'mode'                    => 1,
			'flag'					  => 1,
			'fields'                  => array('name'),
			'panelLayout'             => 'filter;sort,search,limit',
		),
		'label' => array
		(
			'fields'                  => array('name'),
			'format'                  => '%s'
		),
		'global_operations' => array
		(
		),
		'operations' => array
		(
			'edit' => array
			(
				'label'               => &$GLOBALS['TL_LANG']['tl_mailchimp_apikeys']['edit'],
				'href'                => 'act=edit',
				'icon'                => 'edit.gif',
			),
			'copy' => array
			(
				'label'               => &$GLOBALS['TL_LANG']['tl_mailchimp_apikeys']['copy'],
				'href'                => 'act=paste&amp;mode=copy',
				'icon'                => 'copy.gif',
			),
			'cut' => array
			(
				'label'               => &$GLOBALS['TL_LANG']['tl_mailchimp_apikeys']['cut'],
				'href'                => 'act=paste&amp;mode=cut',
				'icon'                => 'cut.gif',
			),
			'delete' => array
			(
				'label'               => &$GLOBALS['TL_LANG']['tl_mailchimp_apikeys']['delete'],
				'href'                => 'act=delete',
				'icon'                => 'delete.gif',
				'attributes'          => 'onclick="if (!confirm(\'' . $GLOBALS['TL_LANG']['MSC']['deleteConfirm'] . '\')) return false; Backend.getScrollOffset();"',
			),
		)
	),

	// Palettes
	'palettes' => array
	(
		'default'                     => '{general_legend},name,alias;{mailchimp_apikey_legend},api_key'
	),

	// Fields
	'fields' => array
	(
		'id' => array
		(
			'sql' 					  => "int(10) unsigned NOT NULL auto_increment"
		),
		'tstamp' => array
		(
			'sql'					  => "int(10) unsigned NOT NULL default '0'"
		),
		'name' => array
		(
			'label'                   => &$GLOBALS['TL_LANG']['tl_mailchimp_apikeys']['name'],
			'exclude'                 => true,
			'search'                  => true,
			'sorting'                 => true,
			'flag'                    => 1,
			'inputType'               => 'text',
			'eval'                    => array('mandatory'=>true, 'maxlength'=>255, 'tl_class'=>'w50'),
			'sql'					  => "varchar(255) NOT NULL default ''"
		),
		'alias' => array
		(
			'label'                   => &$GLOBALS['TL_LANG']['tl_mailchimp_apikeys']['alias'],
			'exclude'                 => true,
			'inputType'               => 'text',
			'eval'                    => array('rgxp'=>'alnum', 'unique'=>true, 'spaceToUnderscore'=>true, 'maxlength'=>128, 'tl_class'=>'w50'),
			'save_callback' => array
			(
				array('Rhyme\Mailchimp\Backend\Mailchimp\ApiKeys\Callbacks', 'generateAlias')
			),
			'sql'					  => "varchar(255) NOT NULL default ''"
		),
		'api_key' => array
		(
			'label'                   => &$GLOBALS['TL_LANG']['tl_mailchimp_apikeys']['api_key'],
			'exclude'                 => true,
			'inputType'               => 'text',
			'eval'                    => array('mandatory'=>true, 'tl_class'=>'w50'),
			'sql'					  => "varchar(255) NOT NULL default ''"
		),
	)
);


