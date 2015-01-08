<?php
/**
 * gTemplate Engine
 * https://github.com/glex86/g-template-php
 */

function tpl_modifier_replace($string, $search, $replace)
{
    return str_replace($search, $replace, $string);
}
