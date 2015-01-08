<?php
/**
 * gTemplate Plugins
 *
 * @package    gTemplate
 * @subpackage Plugins
 */

/**
 * gTemplate {capture}{/capture} block plugin
 *
 * Type:     block function<br>
 * Name:     capture<br>
 * Purpose:  removes content and stores it in a variable<br>
 *
 * @version 1.0
 * @author Tamas David (G-Lex) <glex at mittudomain.info>
 * @link https://github.com/glex86/g-template-php G-Template Engine on Github
 *
 * @internal Some source codes are taken from Smarty
 * @internal author Monte Ohrt <monte at ohrt dot com>
 * @internal link http://smarty.net Smarty
 */
function tpl_block_capture($params, $content, &$gTpl) {
    extract($params);

    if (isset($name)) {
        $buffer = $name;
    } else {
        $buffer = "'default'";
    }

    $gTpl->_vars['capture'][$buffer] = $content;
    if (isset($assign)) {
        $gTpl->assign($assign, $content);
    }
    return;
}
