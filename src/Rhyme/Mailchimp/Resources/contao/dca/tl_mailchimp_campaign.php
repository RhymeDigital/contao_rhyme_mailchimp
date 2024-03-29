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
System::loadLanguageFile('tl_mailchimp_campaign');

$GLOBALS['TL_DCA']['tl_mailchimp_campaign'] = array
(

    // Config
    'config' => array
    (
        'dataContainer'               => 'Table',
        'switchToEdit'                => true,
        'enableVersioning'            => false,
        'ctable'                      => array('tl_content'),
        'onload_callback'           => array
        (
            array('Rhyme\Mailchimp\Backend\Mailchimp\Campaign\Callbacks', 'loadDca'),
        ),
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
            'mode'                    => 2,
            'disableGrouping'         => true,
            'fields'                  => array('tstamp DESC', 'id DESC'),
            'headerFields'            => array('name'),
            'panelLayout'             => 'filter;search,limit',
        ),
        'label' => array
        (
            'fields'                  => array('name'),
            'format'                  => '%s',
            'label_callback'          => array('Rhyme\Mailchimp\Backend\Mailchimp\Campaign\Callbacks', 'generateLabel'),
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
                'button_callback'     => array('Rhyme\Mailchimp\Backend\Mailchimp\Campaign\Callbacks', 'toggleIcon'),
            ),
            'preview' => array
            (
                'label'               => &$GLOBALS['TL_LANG']['tl_mailchimp_campaign']['preview'],
                'href'                => Environment::get('base').'mailchimp/campaign/',
                'icon'                => 'bundles/rhymemailchimp/assets/img/search.svg',
                'attributes'          => 'onclick="window.open(this.href); return false;" alt="Icon made by Dimitry Miroliubov from www.flaticon.com"',
                'button_callback'     => array('Rhyme\Mailchimp\Backend\Mailchimp\Campaign\Callbacks', 'previewIcon'),
            ),
            'archive_url' => array
            (
                'label'               => &$GLOBALS['TL_LANG']['tl_mailchimp_campaign']['archive_url'],
                'href'                => Environment::get('base').'mailchimp/campaign/',
                'icon'                => 'bundles/rhymemailchimp/assets/img/worldwide.svg',
                'attributes'          => 'onclick="window.open(this.href); return false;" alt="Icon made by turkkub from www.flaticon.com"',
                'button_callback'     => array('Rhyme\Mailchimp\Backend\Mailchimp\Campaign\Callbacks', 'archiveUrlIcon'),
            ),
            'test' => array
            (
                'label'               => &$GLOBALS['TL_LANG']['tl_mailchimp_campaign']['test'],
                'href'                => Environment::get('request').'&key=test&id=',
                'icon'                => 'bundles/rhymemailchimp/assets/img/flask.svg',
                'attributes'          => 'alt="Icon made by Freepik from www.flaticon.com"',
                'button_callback'     => array('Rhyme\Mailchimp\Backend\Mailchimp\Campaign\Callbacks', 'testIcon'),
            ),
            'schedule' => array
            (
                'label'               => &$GLOBALS['TL_LANG']['tl_mailchimp_campaign']['schedule'],
                'href'                => Environment::get('request').'&key=schedule&id=',
                'icon'                => 'assets/datepicker/images/icon.svg',
                'button_callback'     => array('Rhyme\Mailchimp\Backend\Mailchimp\Campaign\Callbacks', 'scheduleIcon'),
            ),
            'unschedule' => array
            (
                'label'               => &$GLOBALS['TL_LANG']['tl_mailchimp_campaign']['unschedule'],
                'href'                => Environment::get('request').'&key=unschedule&id=',
                'icon'                => 'bundles/rhymemailchimp/assets/img/cancel-calendar.svg',
                'attributes'          => 'alt="Icon made by Pixel perfect from www.flaticon.com"',
                'button_callback'     => array('Rhyme\Mailchimp\Backend\Mailchimp\Campaign\Callbacks', 'unscheduleIcon'),
            ),
        )
    ),

    // Palettes
    'palettes' => array
    (
        '__selector__'                => array(),
        'default'                     => '{general_legend},name;{mailchimp_legend},mc_api_key,mc_list,mc_from_name,mc_replyto_email,mc_subject,mc_preview_text;{template_legend},html_tpl,reset_styles_tpl,styles_tpl;{publishing_legend},published,archived'
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
            'flag'                    => 6,
            'sql'                     => "int(10) unsigned NOT NULL default '0'"
        ),
        'name' => array
        (
            'exclude'                 => true,
            'search'                  => true,
            'inputType'               => 'text',
            'eval'                    => array('mandatory'=>true, 'maxlength'=>255, 'tl_class'=>'w50', 'doNotCopy'=>true, 'decodeEntities'=>true),
            'sql'                     => "varchar(255) NOT NULL default ''"
        ),
        'mc_api_key' => array
        (
            'exclude'                 => true,
            'inputType'               => 'select',
            'foreignKey'              => 'tl_mailchimp_apikeys.name',
            'eval'                    => array('tl_class'=>'w50 clr', 'mandatory'=>true, 'includeBlankOption'=>true, 'submitOnChange'=>true, 'chosen'=>true, 'doNotSaveEmpty'=>true),
            'sql'                     => "int(10) NOT NULL default '0'",
            'relation'                => array('type'=>'hasOne', 'load'=>'eager'),
        ),
        'mc_list' => array
        (
            'exclude'                 => true,
            'inputType'               => 'select',
            'options_callback'        => array('Rhyme\Mailchimp\Backend\Mailchimp\Campaign\Callbacks', 'getMailchimpLists'),
            'eval'                    => array('tl_class'=>'w50', 'mandatory'=>true, 'includeBlankOption'=>true, 'submitOnChange'=>true, 'chosen'=>true, 'doNotSaveEmpty'=>true),
            'sql'                     => "varchar(16) NOT NULL default ''"
        ),
        'mc_from_name' => array
        (
            'exclude'                 => true,
            'inputType'               => 'text',
            'eval'                    => array('mandatory'=>true, 'maxlength'=>255, 'tl_class'=>'w50 clr', 'decodeEntities'=>true),
            'sql'                     => "varchar(255) NOT NULL default ''"
        ),
        'mc_replyto_email' => array
        (
            'exclude'                 => true,
            'inputType'               => 'text',
            'eval'                    => array('mandatory'=>true, 'maxlength'=>255, 'tl_class'=>'w50', 'rgxp'=>'email', 'decodeEntities'=>true),
            'sql'                     => "varchar(255) NOT NULL default ''"
        ),
        'mc_subject' => array
        (
            'exclude'                 => true,
            'search'                  => true,
            'inputType'               => 'text',
            'eval'                    => array('mandatory'=>true, 'maxlength'=>255, 'tl_class'=>'clr long', 'decodeEntities'=>true),
            'sql'                     => "varchar(255) NOT NULL default ''"
        ),
        'mc_preview_text' => array
        (
            'exclude'                 => true,
            'search'                  => true,
            'inputType'               => 'text',
            'eval'                    => array('mandatory'=>true, 'maxlength'=>255, 'tl_class'=>'clr long', 'decodeEntities'=>true),
            'sql'                     => "varchar(255) NOT NULL default ''"
        ),
        'mc_archive_url' => array
        (
            'exclude'                 => true,
            'search'                  => true,
            'inputType'               => 'text',
            'eval'                    => array('maxlength'=>255, 'tl_class'=>'clr long'),
            'sql'                     => "varchar(255) NOT NULL default ''"
        ),
        'mc_long_archive_url' => array
        (
            'exclude'                 => true,
            'search'                  => true,
            'inputType'               => 'text',
            'eval'                    => array('maxlength'=>512, 'tl_class'=>'clr long'),
            'sql'                     => "varchar(512) NOT NULL default ''"
        ),
        'html_tpl' => array
        (
            'default'                 => 'mailchimp_campaign_html_default',
            'exclude'                 => true,
            'inputType'               => 'select',
            'options_callback'        => function (DataContainer $dc){
                return Controller::getTemplateGroup('mailchimp_campaign_html_');
            },
            'eval'                    => array('tl_class'=>'w50 clr', 'mandatory'=>true, 'chosen'=>true),
            'sql'                     => "varchar(128) NOT NULL default ''"
        ),
        'reset_styles_tpl' => array
        (
            'default'                 => 'mailchimp_campaign_reset_styles_default',
            'exclude'                 => true,
            'inputType'               => 'select',
            'options_callback'        => function (DataContainer $dc){
                return Controller::getTemplateGroup('mailchimp_campaign_reset_styles_');
            },
            'eval'                    => array('tl_class'=>'w50', 'mandatory'=>true, 'chosen'=>true),
            'sql'                     => "varchar(128) NOT NULL default ''"
        ),
        'styles_tpl' => array
        (
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
            'exclude'                 => true,
            'filter'                  => true,
            'default'                 => '1',
            'inputType'               => 'checkbox',
            'sql'                     => "char(1) NOT NULL default '1'"
        ),
        'archived' => array
        (
            'exclude'                 => true,
            'filter'                  => true,
            'default'                 => '',
            'inputType'               => 'checkbox',
            'sql'                     => "char(1) NOT NULL default ''"
        ),
        'campaign_id' => array
        (
            'eval'                    => array('doNotCopy'=>true),
            'sql'                     => "varchar(16) NOT NULL default ''"
        ),
    )
);