<?php
/**
 * gTemplate Plugins
 *
 * @package    gTemplate
 * @subpackage Plugins
 */

/**
 * gTemplate {debug} function plugin
 * 
 * Type:     function<br>
 * Name:     debug<br>
 * Purpose:  display debug outputs
 * 
 * @version 1.0
 * @author Tamas David (G-Lex) <glex at mittudomain.info>
 * @link https://github.com/glex86/g-template-php G-Template Engine on Github
 *
 * @internal Some source codes are taken from Smarty
 * @internal author Monte Ohrt <monte at ohrt dot com>
 * @internal link http://smarty.net Smarty
 */
function tpl_compiler_debug($params, &$gTpl) {
    if ($params['output']) {
        $debug_output = '$gTpl->assign("_debug_output", ' . $params['output'] . ');';
    } else {
        $debug_output = "";
    }

    if (!function_exists("generate_compiler_debug_output")) {
        require_once(G_TEMPLATE_BASE . "internal/compile.generate_compiler_debug_output.php");
    }
    $debug_output .= generate_compiler_debug_output($tpl);
    return $debug_output;
}
