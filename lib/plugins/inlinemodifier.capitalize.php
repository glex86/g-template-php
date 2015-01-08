<?php
/**
 * gTemplate Engine
 * https://github.com/glex86/g-template-php
 */

/**
 * capitalize modifier plugin
 *
 * Type:     modifier
 * Name:     capitalize
 * Purpose:  Wrapper for the PHP 'ucwords' function
 */
function tpl_inlinemodifier_capitalize($variable, $arguments, &$object)
{
    return "mb_convert_case({$variable}, MB_CASE_TITLE)";
}
