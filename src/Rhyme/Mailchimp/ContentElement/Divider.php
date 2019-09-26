<?php

/**
 * Copyright (C) 2019 Rhyme Digital, LLC.
 *
 * @link       https://rhyme.digital
 * @license    http://www.gnu.org/licenses/lgpl-3.0.html LGPL
 */

namespace Rhyme\Mailchimp\ContentElement;

use Contao\System;
use Contao\StringUtil;
use Contao\ContentElement;
use Contao\BackendTemplate;

/**
 * Class Divider
 * @package Rhyme\Mailchimp\ContentElement
 */
class Divider extends ContentElement
{

    /**
     * Template
     * @var string
     */
    protected $strTemplate = 'ce_mailchimp_divider';

    /**
     * Generate the content element
     */
    protected function compile()
    {
        // Border style
        $this->Template->borderstyle = '';

        if ($this->rhymemailchimp_borderstyle != '')
        {
            $this->Template->borderstyle = 'border-top-style:' . $this->rhymemailchimp_borderstyle . ';';
        }

        // Width
        $this->Template->borderwidth = '';
        $this->rhymemailchimp_borderwidth = StringUtil::deserialize($this->rhymemailchimp_borderwidth);

        if (isset($this->rhymemailchimp_borderwidth['value']) && $this->rhymemailchimp_borderwidth['value'] != '')
        {
            $this->Template->borderwidth = 'border-top-width:' . $this->rhymemailchimp_borderwidth['value'] . (($this->rhymemailchimp_borderwidth['value'] == 'auto') ? '' : $this->rhymemailchimp_borderwidth['unit']) . ';';
        }

        // Border color
        $this->Template->bordercolor = '';

        if ($this->rhymemailchimp_bordercolor != '')
        {
            $this->Template->bordercolor = 'border-top-color:#' . $this->rhymemailchimp_bordercolor . ';';
        }

        // Background color
        $this->Template->bgcolor = '';

        if ($this->rhymemailchimp_bgcolor != '')
        {
            $this->Template->bgcolor = 'background-color:#' . $this->rhymemailchimp_bgcolor . ';';
        }

        // Padding
        $this->Template->padding = '';

        if ($this->rhymemailchimp_padding != '')
        {
            $this->rhymemailchimp_padding = StringUtil::deserialize($this->rhymemailchimp_padding);

            if (\is_array($this->rhymemailchimp_padding))
            {
                $top = $this->rhymemailchimp_padding['top'];
                $right = $this->rhymemailchimp_padding['right'];
                $bottom = $this->rhymemailchimp_padding['bottom'];
                $left = $this->rhymemailchimp_padding['left'];

                // Try to shorten the definition
                if ($top != '' && $right != '' && $bottom != '' && $left != '')
                {
                    if ($top == $right && $top == $bottom && $top == $left)
                    {
                        $this->Template->padding = 'padding:' . $top . (($top === '0') ? '' : $this->rhymemailchimp_padding['unit']) . ';';
                    }
                    elseif ($top == $bottom && $right == $left)
                    {
                        $this->Template->padding = 'padding:' . $top . (($top === '0') ? '' : $this->rhymemailchimp_padding['unit']) . ' ' . $right . (($right === '0') ? '' : $this->rhymemailchimp_padding['unit']) . ';';
                    }
                    elseif ($top != $bottom && $right == $left)
                    {
                        $this->Template->padding = 'padding:' . $top . (($top === '0') ? '' : $this->rhymemailchimp_padding['unit']) . ' ' . $right . (($right === '0') ? '' : $this->rhymemailchimp_padding['unit']) . ' ' . $bottom . (($bottom === '0') ? '' : $this->rhymemailchimp_padding['unit']) . ';';
                    }
                    else
                    {
                        $this->Template->padding = 'padding:' . $top . (($top === '0') ? '' : $this->rhymemailchimp_padding['unit']) . ' ' . $right . (($right === '0') ? '' : $this->rhymemailchimp_padding['unit']) . ' ' . $bottom . (($bottom === '0') ? '' : $this->rhymemailchimp_padding['unit']) . ' ' . $left . (($left === '0') ? '' : $this->rhymemailchimp_padding['unit']) . ';';
                    }
                }
                else
                {
                    $arrDir = compact('top', 'right', 'bottom', 'left');

                    foreach ($arrDir as $k=>$v)
                    {
                        if ($v != '')
                        {
                            $this->Template->padding .= 'padding-' . $k . ':' . $v . (($v === '0') ? '' : $this->rhymemailchimp_padding['unit']) . ';';
                        }
                    }
                }
            }
        }
    }
}