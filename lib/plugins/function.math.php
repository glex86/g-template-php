<?php
/**
 * gTemplate Plugins
 *
 * @package    gTemplate
 * @subpackage Plugins
 */

/**
 * gTemplate {math} function plugin
 *
 * Type:     function<br>
 * Name:     math<br>
 * Purpose:  handle math computations in template<br>
 * 
 * @version 1.0
 * @author Tamas David (G-Lex) <glex at mittudomain.info>
 * @link https://github.com/glex86/g-template-php G-Template Engine on Github
 *
 * @internal Some source codes are taken from Smarty
 * @internal author Monte Ohrt <monte at ohrt dot com>
 * @internal link http://smarty.net Smarty
 */
function tpl_function_math($params, &$gTpl) {
    // be sure equation parameter is present
    if (empty($params['equation'])) {
        $gTpl->trigger_error("math: missing equation parameter");
        return;
    }

    // strip out backticks, not necessary for math
    $equation = str_replace('`', '', $params['equation']);

    // make sure parenthesis are balanced
    if (substr_count($equation, "(") != substr_count($equation, ")")) {
        $gTpl->trigger_error("math: unbalanced parenthesis");
        return;
    }

    // match all vars in equation, make sure all are passed
    preg_match_all("!(?:0x[a-fA-F0-9]+)|([a-zA-Z][a-zA-Z0-9_]*)!", $equation, $match);
    $allowed_funcs = array('int', 'abs', 'ceil', 'cos', 'exp', 'floor', 'log', 'log10',
        'max', 'min', 'pi', 'pow', 'rand', 'round', 'sin', 'sqrt', 'srand', 'tan');

    foreach ($match[1] as $curr_var) {
        if ($curr_var && !in_array($curr_var, array_keys($params)) && !in_array($curr_var, $allowed_funcs)) {
            $gTpl->trigger_error("math: function call $curr_var not allowed");
            return;
        }
    }

    foreach ($params as $key => $val) {
        if ($key != "equation" && $key != "format" && $key != "assign") {
            // make sure value is not empty
            if (strlen($val) == 0) {
                $gTpl->trigger_error("math: parameter $key is empty");
                return;
            }
            if (!is_numeric($val)) {
                $gTpl->trigger_error("math: parameter $key: is not numeric");
                return;
            }
            $equation = preg_replace("/\b$key\b/", " \$params['$key'] ", $equation);
        }
    }

    eval("\$gTpl_math_result = " . $equation . ";");

    if (empty($params['format'])) {
        if (empty($params['assign'])) {
            return $gTpl_math_result;
        } else {
            $gTpl->assign($params['assign'], $gTpl_math_result);
        }
    } else {
        if (empty($params['assign'])) {
            printf($params['format'], $gTpl_math_result);
        } else {
            $gTpl->assign($params['assign'], sprintf($params['format'], $gTpl_math_result));
        }
    }
}
