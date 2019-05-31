<?php

/**
 * Copyright (C) 2019 Rhyme Digital, LLC.
 *
 * @link		http://rhyme.digital
 * @license		http://www.gnu.org/licenses/lgpl-3.0.html LGPL
 */


// Dynamically add the permission check and parent table
if (\Contao\Input::get('do') == 'mailchimp_campaigns')
{
    $GLOBALS['TL_DCA']['tl_content']['config']['ptable'] = 'tl_mailchimp_campaign';
}