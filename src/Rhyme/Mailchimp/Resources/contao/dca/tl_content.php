<?php

/**
 * Copyright (C) 2019 Rhyme Digital, LLC.
 *
 * @link		http://rhyme.digital
 * @license		http://www.gnu.org/licenses/lgpl-3.0.html LGPL
 */


// Dynamically add the permission check and parent table
if (\Contao\Input::get('do') == 'rhymemailchimp_campaigns')
{
    $GLOBALS['TL_DCA']['tl_content']['config']['ptable'] = 'tl_mailchimp_campaign';
}


/**
 * Palettes
 */
$GLOBALS['TL_DCA']['tl_content']['palettes']['rhymemailchimp_divider'] = '{type_legend},type;{config_legend},rhymemailchimp_borderstyle,rhymemailchimp_borderwidth,rhymemailchimp_bordercolor,rhymemailchimp_bgcolor,rhymemailchimp_padding;{template_legend:hide},customTpl;{protected_legend:hide},protected;{expert_legend:hide},guests;{invisible_legend:hide},invisible,start,stop';


/**
 * Fields
 */
$GLOBALS['TL_DCA']['tl_content']['fields']['rhymemailchimp_borderstyle'] = array
(
    'label'                   => &$GLOBALS['TL_LANG']['tl_content']['rhymemailchimp_borderstyle'],
    'inputType'               => 'select',
    'default'                 => 'solid',
    'options'                 => array('solid', 'dotted', 'dashed', 'double', 'groove', 'ridge', 'inset', 'outset', 'hidden'),
    'eval'                    => array('includeBlankOption'=>false, 'tl_class'=>'w50'),
    'sql'                     => "varchar(32) NOT NULL default ''"
);
$GLOBALS['TL_DCA']['tl_content']['fields']['rhymemailchimp_borderwidth'] = array
(
    'label'                   => &$GLOBALS['TL_LANG']['tl_content']['rhymemailchimp_borderwidth'],
    'inputType'               => 'inputUnit',
    'options'                 => array('px'),
    'eval'                    => array('mandatory'=>true, 'includeBlankOption'=>false, 'rgxp'=>'digit_inherit', 'maxlength' => 20, 'tl_class'=>'w50'),
    'sql'                     => "varchar(64) NOT NULL default ''"
);
$GLOBALS['TL_DCA']['tl_content']['fields']['rhymemailchimp_bordercolor'] = array
(
    'label'                   => &$GLOBALS['TL_LANG']['tl_content']['rhymemailchimp_bordercolor'],
    'inputType'               => 'text',
    'eval'                    => array('mandatory'=>true, 'maxlength'=>6, 'colorpicker'=>true, 'isHexColor'=>true, 'decodeEntities'=>true, 'tl_class'=>'w50 wizard'),
    'sql'                     => "varchar(64) NOT NULL default ''"
);
$GLOBALS['TL_DCA']['tl_content']['fields']['rhymemailchimp_bgcolor'] = array
(
    'label'                   => &$GLOBALS['TL_LANG']['tl_content']['rhymemailchimp_bgcolor'],
    'inputType'               => 'text',
    'eval'                    => array('maxlength'=>6, 'colorpicker'=>true, 'isHexColor'=>true, 'decodeEntities'=>true, 'tl_class'=>'w50 wizard'),
    'sql'                     => "varchar(64) NOT NULL default ''"
);
$GLOBALS['TL_DCA']['tl_content']['fields']['rhymemailchimp_padding'] = array
(
    'label'                   => &$GLOBALS['TL_LANG']['tl_content']['rhymemailchimp_padding'],
    'inputType'               => 'trbl',
    'options'                 => array('px'),
    'eval'                    => array('includeBlankOption'=>false, 'rgxp'=>'digit_inherit', 'tl_class'=>'w50'),
    'sql'                     => "varchar(128) NOT NULL default ''"
);