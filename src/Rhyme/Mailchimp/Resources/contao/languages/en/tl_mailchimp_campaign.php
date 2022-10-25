<?php

/**
 * Copyright (C) 2019 Rhyme Digital, LLC.
 * 
 * @link		http://rhyme.digital
 * @license		http://www.gnu.org/licenses/lgpl-3.0.html LGPL
 */


/**
 * Fields
 */
$GLOBALS['TL_LANG']['tl_mailchimp_campaign']['name']					= array('Campaign name', 'Please enter the campaign name.');
$GLOBALS['TL_LANG']['tl_mailchimp_campaign']['tstamp']					= array('Date modified', 'This is the last modified date/time.');
$GLOBALS['TL_LANG']['tl_mailchimp_campaign']['mc_api_key']			    = array('API key', 'Please select the API configuration here.');
$GLOBALS['TL_LANG']['tl_mailchimp_campaign']['mc_list']				    = array('Audience', 'Please select the audience/recipient list.');
$GLOBALS['TL_LANG']['tl_mailchimp_campaign']['mc_from_name']			= array('"From" name', 'Please enter the "From" name for the email.');
$GLOBALS['TL_LANG']['tl_mailchimp_campaign']['mc_replyto_email']	    = array('"Reply To" email', 'Please enter the "Reply To" email address for the email.');
$GLOBALS['TL_LANG']['tl_mailchimp_campaign']['mc_subject']			    = array('Subject', 'Please enter the subject for the email.');
$GLOBALS['TL_LANG']['tl_mailchimp_campaign']['mc_preview_text']			= array('Preview text', 'Please enter the preview text for the email.');
$GLOBALS['TL_LANG']['tl_mailchimp_campaign']['mc_title']			    = array('Title', 'Please enter the title for this campaign (how it will be displayed within Mailchimp) .');
$GLOBALS['TL_LANG']['tl_mailchimp_campaign']['html_tpl']			    = array('HTML template', 'Please select the HTML template for "URL" type emails.');
$GLOBALS['TL_LANG']['tl_mailchimp_campaign']['reset_styles_tpl']		= array('"Reset" CSS styles template', 'Please select the reset CSS styles template for "URL" type emails.');
$GLOBALS['TL_LANG']['tl_mailchimp_campaign']['styles_tpl']			    = array('CSS styles template', 'Please select the CSS styles template for "URL" type emails.');
$GLOBALS['TL_LANG']['tl_mailchimp_campaign']['published']			    = array('Published', 'Check this box if the campaign should be published.');
$GLOBALS['TL_LANG']['tl_mailchimp_campaign']['archived']			    = array('Archived', 'Check this box if the campaign should be archived.');


/**
 * Legends
 */
$GLOBALS['TL_LANG']['tl_mailchimp_campaign']['general_legend']     			= 'General Settings';
$GLOBALS['TL_LANG']['tl_mailchimp_campaign']['mailchimp_legend']     	    = 'Mailchimp Settings';
$GLOBALS['TL_LANG']['tl_mailchimp_campaign']['template_legend']     	    = 'Template Settings';
$GLOBALS['TL_LANG']['tl_mailchimp_campaign']['publishing_legend']     	    = 'Publishing Settings';


/**
 * Buttons
 */
$GLOBALS['TL_LANG']['tl_mailchimp_campaign']['new']         = array('New campaign', 'Create a new campaign');
$GLOBALS['TL_LANG']['tl_mailchimp_campaign']['edit']        = array('Edit campaign', 'Edit campaign %s');
$GLOBALS['TL_LANG']['tl_mailchimp_campaign']['copy']        = array('Duplicate campaign', 'Duplicate campaign %s');
$GLOBALS['TL_LANG']['tl_mailchimp_campaign']['delete']      = array('Delete campaign', 'Delete campaign %s');
$GLOBALS['TL_LANG']['tl_mailchimp_campaign']['toggle']      = array('Toggle visibility', 'Toggle visibility of campaign %s');
$GLOBALS['TL_LANG']['tl_mailchimp_campaign']['preview']     = array('Preview campaign', 'Preview campaign %s in a new tab');
$GLOBALS['TL_LANG']['tl_mailchimp_campaign']['test']        = array('Test email', 'Send a test email for campaign %s');
$GLOBALS['TL_LANG']['tl_mailchimp_campaign']['schedule']    = array('Schedule', 'Schedule campaign %s');
$GLOBALS['TL_LANG']['tl_mailchimp_campaign']['unschedule']  = array('Unschedule', 'Unschedule campaign %s');


/**
 * Reference
 */
$GLOBALS['TL_LANG']['tl_mailchimp_campaign']['statuses']['new']             = 'Draft';
$GLOBALS['TL_LANG']['tl_mailchimp_campaign']['statuses']['draft']           = 'Draft';
$GLOBALS['TL_LANG']['tl_mailchimp_campaign']['statuses']['save']            = 'Draft';
$GLOBALS['TL_LANG']['tl_mailchimp_campaign']['statuses']['sent']            = 'Sent';
$GLOBALS['TL_LANG']['tl_mailchimp_campaign']['statuses']['schedule']        = 'Scheduled';
$GLOBALS['TL_LANG']['tl_mailchimp_campaign']['statuses']['scheduled']       = 'Scheduled';
$GLOBALS['TL_LANG']['tl_mailchimp_campaign']['statuses']['sending']         = 'Sending';
$GLOBALS['TL_LANG']['tl_mailchimp_campaign']['statuses']['paused']          = 'Paused';
$GLOBALS['TL_LANG']['tl_mailchimp_campaign']['statuses']['unknown']         = 'Unknown';
