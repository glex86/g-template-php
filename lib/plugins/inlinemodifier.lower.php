<?php
/**
 * gTemplate Plugins
 *
 * @package    gTemplate
 * @subpackage Plugins
 */

/**
 * gTemplate lower modifier plugin
 *
 * Type:     inlinemodifier<br>
 * Name:     lower<br>
 * Purpose:  convert string to lowercase
 * 
 * @version 1.0
 * @author Tamas David (G-Lex) <glex at mittudomain.info>
 * @link https://github.com/glex86/g-template-php G-Template Engine on Github
 */
function tpl_modifier_lower($variable, $arguments, &$gTpl) {
    return "mb_strtolower({$variable})";
}
