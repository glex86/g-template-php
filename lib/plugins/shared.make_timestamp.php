<?php
/**
 * gTemplate Plugins
 *
 * @package    gTemplate
 * @subpackage Plugins
 */


/**
 * Function: tpl_make_timestamp<br>
 * Purpose:  used by other smarty functions to make a timestamp
 *           from a string.
 * 
 * @version 1.0
 * @author Tamas David (G-Lex) <glex at mittudomain.info>
 * @link https://github.com/glex86/g-template-php G-Template Engine on Github
 *
 * @internal Some source codes are taken from Smarty
 * @internal author Monte Ohrt <monte at ohrt dot com>
 * @internal link http://smarty.net Smarty
 */
function tpl_make_timestamp($string) {
    if (empty($string)) {
        return time();
    }
    
    $time = strtotime($string);
    if (is_numeric($time) && $time != -1) {
        return $time;
    }

    // is mysql timestamp format of YYYYMMDDHHMMSS?
    if (is_numeric($string) && strlen($string) == 14) {
        $time = mktime(substr($string, 8, 2), substr($string, 10, 2), substr($string, 12, 2), substr($string, 4, 2), substr($string, 6, 2), substr($string, 0, 4));
        return $time;
    }

    return time();
}
