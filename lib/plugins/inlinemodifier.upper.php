<?php
/**
 * gTemplate Plugins
 *
 * @package    gTemplate
 * @subpackage Plugins
 */

/**
 * gTemplate upper modifier plugin
 *
 * Type:     inlinemodifier<br>
 * Name:     upper<br>
 * Purpose:  convert string to uppercase
 * 
 * @version 1.0
 * @author Tamas David (G-Lex) <glex at mittudomain.info>
 * @link https://github.com/glex86/g-template-php G-Template Engine on Github
 */
function tpl_inlinemodifier_upper($variable, $arguments, &$gTpl) {    
    return "mb_strtoupper({$variable})";
}
