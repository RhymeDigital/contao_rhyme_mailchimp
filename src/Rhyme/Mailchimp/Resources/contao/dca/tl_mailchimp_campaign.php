<?php

/**
 * Copyright (C) 2019 Rhyme Digital, LLC.
 *
 * @link       https://rhyme.digital
 * @license    http://www.gnu.org/licenses/lgpl-3.0.html LGPL
 */

use Contao\System;
use Contao\Controller;
use Contao\Environment;
use Contao\DataContainer;

System::loadLanguageFile('tl_content');

$GLOBALS['TL_DCA']['tl_mailchimp_campaign'] = array
(

    // Config
    'config' => array
    (
        'dataContainer'               => 'Table',
        'switchToEdit'                => true,
        'enableVersioning'            => false,
        'ctable'                      => array('tl_content'),
        'onsubmit_callback'           => array
        (
            array('Rhyme\Mailchimp\Backend\Mailchimp\Campaign\Callbacks', 'campaignSave'),
        ),
        'ondelete_callback'           => array
        (
            array('Rhyme\Mailchimp\Backend\Mailchimp\Campaign\Callbacks', 'campaignDelete'),
        ),
        'sql' => array
        (
            'keys' => array
            (
                'id' => 'primary',
            )
        )
    ),

    // List
    'list' => array
    (
        'sorting' => array
        (
            'mode'                    => 1,
            'fields'                  => array('name'),
            'headerFields'            => array('name'),
            'panelLayout'             => 'filter;search,limit',
        ),
        'label' => array
        (
            'fields'                  => array('name'),
            'format'                  => '%s'
        ),
        'global_operations' => array
        (
            'all' => array
            (
                'label'               => &$GLOBALS['TL_LANG']['MSC']['all'],
                'href'                => 'act=select',
                'class'               => 'header_edit_all',
                'attributes'          => 'onclick="Backend.getScrollOffset()" accesskey="e"'
            )
        ),
        'operations' => array
        (
            'edit' => array
            (
                'label'               => &$GLOBALS['TL_LANG']['tl_mailchimp_campaign']['edit'],
                'href'                => 'table=tl_content',
                'icon'                => 'edit.svg'
            ),
            'editheader' => array
            (
                'label'               => &$GLOBALS['TL_LANG']['tl_mailchimp_campaign']['editmeta'],
                'href'                => 'act=edit',
                'icon'                => 'header.svg'
            ),
            'preview' => array
            (
                'label'               => &$GLOBALS['TL_LANG']['tl_mailchimp_campaign']['preview'],
                'href'                => Environment::get('base').'mailchimp/campaign/%s',
                'icon'                => 'layout.svg',
                'attributes'          => 'onclick="window.open(\''.Environment::get('base').'mailchimp/campaign/%s\'); return false;"',
            ),
            'copy' => array
            (
                'label'               => &$GLOBALS['TL_LANG']['tl_mailchimp_campaign']['copy'],
                'href'                => 'act=copy',
                'icon'                => 'copy.svg',
            ),
            'delete' => array
            (
                'label'               => &$GLOBALS['TL_LANG']['tl_mailchimp_campaign']['delete'],
                'href'                => 'act=delete',
                'icon'                => 'delete.svg',
                'attributes'          => 'onclick="if(!confirm(\'' . $GLOBALS['TL_LANG']['MSC']['deleteConfirm'] . '\'))return false;Backend.getScrollOffset()"',
            ),
            'toggle' => array
            (
                'label'               => &$GLOBALS['TL_LANG']['tl_mailchimp_campaign']['toggle'],
                'icon'                => 'visible.svg',
                'attributes'          => 'onclick="Backend.getScrollOffset();return AjaxRequest.toggleVisibility(this,%s)"',
                'button_callback'     => array('Rhyme\Mailchimp\Backend\Mailchimp\Campaign\Callbacks', 'toggleIcon')
            ),
            'show' => array
            (
                'label'               => &$GLOBALS['TL_LANG']['tl_mailchimp_campaign']['show'],
                'href'                => 'act=show',
                'icon'                => 'show.svg'
            ),
        )
    ),

    // Palettes
    'palettes' => array
    (
        '__selector__'                => array(),
        'default'                     => '{general_legend},name;{mailchimp_legend},mc_api_key,mc_list,mc_from_name,mc_replyto_email,mc_subject,mc_preview_text;{template_legend},html_tpl,styles_tpl;{publishing_legend},published,start,stop;'
    ),

    // Subpalettes
    'subpalettes' => array
    (
    ),

    // Fields
    'fields' => array
    (
        'id' => array
        (
            'sql'                     => "int(10) unsigned NOT NULL auto_increment"
        ),
        'tstamp' => array
        (
            'sql'                     => "int(10) unsigned NOT NULL default '0'"
        ),
        'name' => array
        (
            'label'                   => &$GLOBALS['TL_LANG']['tl_mailchimp_campaign']['name'],
            'exclude'                 => true,
            'search'                  => true,
            'inputType'               => 'text',
            'eval'                    => array('mandatory'=>true, 'maxlength'=>255, 'tl_class'=>'w50', 'doNotCopy'=>true, 'decodeEntities'=>true),
            'sql'                     => "varchar(255) NOT NULL default ''"
        ),
        'mc_api_key' => array
        (
            'label'                   => &$GLOBALS['TL_LANG']['tl_mailchimp_campaign']['mc_api_key'],
            'exclude'                 => true,
            'inputType'               => 'select',
            'foreignKey'              => 'tl_mailchimp_apikeys.name',
            'eval'                    => array('tl_class'=>'w50 clr', 'mandatory'=>true, 'includeBlankOption'=>true, 'submitOnChange'=>true, 'chosen'=>true, 'doNotSaveEmpty'=>true),
            'sql'                     => "int(10) NOT NULL default '0'"
        ),
        'mc_list' => array
        (
            'label'                   => &$GLOBALS['TL_LANG']['tl_mailchimp_campaign']['mc_list'],
            'exclude'                 => true,
            'filter'                  => true,
            'inputType'               => 'select',
            'options_callback'        => array('Rhyme\Mailchimp\Backend\Mailchimp\Campaign\Callbacks', 'getMailchimpLists'),
            'eval'                    => array('tl_class'=>'w50', 'mandatory'=>true, 'includeBlankOption'=>true, 'submitOnChange'=>true, 'chosen'=>true, 'doNotSaveEmpty'=>true),
            'sql'                     => "varchar(16) NOT NULL default ''"
        ),
        'mc_from_name' => array
        (
            'label'                   => &$GLOBALS['TL_LANG']['tl_mailchimp_campaign']['mc_from_name'],
            'exclude'                 => true,
            'inputType'               => 'text',
            'eval'                    => array('mandatory'=>true, 'maxlength'=>255, 'tl_class'=>'w50 clr', 'decodeEntities'=>true),
            'sql'                     => "varchar(255) NOT NULL default ''"
        ),
        'mc_replyto_email' => array
        (
            'label'                   => &$GLOBALS['TL_LANG']['tl_mailchimp_campaign']['mc_replyto_email'],
            'exclude'                 => true,
            'inputType'               => 'text',
            'eval'                    => array('mandatory'=>true, 'maxlength'=>255, 'tl_class'=>'w50', 'rgxp'=>'email', 'decodeEntities'=>true),
            'sql'                     => "varchar(255) NOT NULL default ''"
        ),
        'mc_subject' => array
        (
            'label'                   => &$GLOBALS['TL_LANG']['tl_mailchimp_campaign']['mc_subject'],
            'exclude'                 => true,
            'search'                  => true,
            'inputType'               => 'text',
            'eval'                    => array('mandatory'=>true, 'maxlength'=>255, 'tl_class'=>'clr long', 'decodeEntities'=>true),
            'sql'                     => "varchar(255) NOT NULL default ''"
        ),
        'mc_preview_text' => array
        (
            'label'                   => &$GLOBALS['TL_LANG']['tl_mailchimp_campaign']['mc_preview_text'],
            'exclude'                 => true,
            'search'                  => true,
            'inputType'               => 'text',
            'eval'                    => array('mandatory'=>true, 'maxlength'=>255, 'tl_class'=>'clr long', 'decodeEntities'=>true),
            'sql'                     => "varchar(255) NOT NULL default ''"
        ),
        'html_tpl' => array
        (
            'label'                   => &$GLOBALS['TL_LANG']['tl_mailchimp_campaign']['html_tpl'],
            'default'                 => 'mailchimp_campaign_html_default',
            'exclude'                 => true,
            'inputType'               => 'select',
            'options_callback'        => function (DataContainer $dc){
                return Controller::getTemplateGroup('mailchimp_campaign_html_');
            },
            'eval'                    => array('tl_class'=>'w50 clr', 'mandatory'=>true, 'chosen'=>true),
            'sql'                     => "varchar(128) NOT NULL default ''"
        ),
        'styles_tpl' => array
        (
            'label'                   => &$GLOBALS['TL_LANG']['tl_mailchimp_campaign']['styles_tpl'],
            'default'                 => 'mailchimp_campaign_styles_default',
            'exclude'                 => true,
            'inputType'               => 'select',
            'options_callback'        => function (DataContainer $dc){
                return Controller::getTemplateGroup('mailchimp_campaign_styles_');
            },
            'eval'                    => array('tl_class'=>'w50', 'mandatory'=>true, 'chosen'=>true),
            'sql'                     => "varchar(128) NOT NULL default ''"
        ),
        'published' => array
        (
            'label'                   => &$GLOBALS['TL_LANG']['tl_mailchimp_campaign']['published'],
            'exclude'                 => true,
            'filter'                  => true,
            'inputType'               => 'checkbox',
            'sql'                     => "char(1) NOT NULL default ''"
        ),
        'campaign_id' => array
        (
            'eval'                    => array('doNotCopy'=>true),
            'sql'                     => "varchar(16) NOT NULL default ''"
        ),
        'status' => array
        (
            'inputType'               => 'select',
            'default'                 => 'draft',
            'options'                 => array('draft', 'scheduled', 'paused', 'sent'), // More options?
            'eval'                    => array('doNotCopy'=>true),
            'sql'                     => "varchar(32) NOT NULL default 'draft'"
        ),
    )
);