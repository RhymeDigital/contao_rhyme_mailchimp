<?php

/**
 * Copyright (C) 2019 Rhyme Digital, LLC.
 * 
 * @link		http://rhyme.digital
 * @license		http://www.gnu.org/licenses/lgpl-3.0.html LGPL
 */


/**
 * Register the templates
 */
TemplateLoader::addFiles(array
(
    // Backend
    'be_mod_mailchimp_test'                                     => 'vendor/rhymedigital/contao_rhyme_mailchimp/src/Rhyme/Mailchimp/Resources/templates/backend',
    'be_mod_mailchimp_schedule'                                 => 'vendor/rhymedigital/contao_rhyme_mailchimp/src/Rhyme/Mailchimp/Resources/templates/backend',
    'be_mod_mailchimp_unschedule'                               => 'vendor/rhymedigital/contao_rhyme_mailchimp/src/Rhyme/Mailchimp/Resources/templates/backend',

    // Mailchimp
    'mailchimp_campaign_html_default'                           => 'vendor/rhymedigital/contao_rhyme_mailchimp/src/Rhyme/Mailchimp/Resources/templates/mailchimp/campaign',
    'mailchimp_campaign_styles_default'                         => 'vendor/rhymedigital/contao_rhyme_mailchimp/src/Rhyme/Mailchimp/Resources/templates/mailchimp/campaign',
    'mailchimp_campaign_text_default'                           => 'vendor/rhymedigital/contao_rhyme_mailchimp/src/Rhyme/Mailchimp/Resources/templates/mailchimp/campaign',
));