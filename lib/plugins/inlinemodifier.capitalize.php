<?php
/**
 * gTemplate Plugins
 *
 * @package    gTemplate
 * @subpackage Plugins
 */

/**
 * gTemplate capitalize modifier plugin
 *
 * Type:     inlinemodifier<br>
 * Name:     capitalize<br>
 * Purpose:  Capitalizes the first chars of every word
 * 
 * @version 1.0
 * @author Tamas David (G-Lex) <glex at mittudomain.info>
 * @link https://github.com/glex86/g-template-php G-Template Engine on Github
 */
function tpl_inlinemodifier_capitalize($variable, $arguments, &$gTpl)
{
    return "mb_convert_case({$variable}, MB_CASE_TITLE)";
}
