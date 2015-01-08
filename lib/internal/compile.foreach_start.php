<?php
/**
 * gTemplate Internal Function
 * Compiles the 'foreach' function
 *
 * @package    gTemplate
 * @subpackage internalFunctions
 */

function compile_foreach_start($arguments, &$gTpl) {

    //Emulate Smarty3 functionality: foreach $items as $item
    $regexp1 = '/((?:('.$gTpl->_obj_call_regexp.'|' . $gTpl->_var_regexp . '|' . $gTpl->_svar_regexp . ')(' . $gTpl->_mod_regexp . '*))(?:\s+(.*))?) as \$('.$gTpl->_dvar_guts_regexp.')=>\$('.$gTpl->_dvar_guts_regexp.')/is';
    $regexp2 = '/((?:('.$gTpl->_obj_call_regexp.'|' . $gTpl->_var_regexp . '|' . $gTpl->_svar_regexp . ')(' . $gTpl->_mod_regexp . '*))(?:\s+(.*))?) as \$('.$gTpl->_dvar_guts_regexp.')/is';
    if (preg_match($regexp1, $arguments)) {
        $arguments = preg_replace($regexp1, 'from=$1 key=$5 item=$6', $arguments);
    } else {
        $arguments = preg_replace($regexp2, 'from=$1 item=$5', $arguments);
    }

    $attrs = $gTpl->_parse_arguments($arguments);
    $arg_list = array();

    /* Required attr: from */
    if (empty($attrs['from'])) {
        return $gTpl->trigger_error("[SYNTAX] missing 'from' attribute in 'foreach' tag", E_USER_ERROR, $gTpl->_file, $gTpl->_linenum);
    }
    $from = $attrs['from'];

    /* Required attr: item */
    if (empty($attrs['item'])) {
        return $gTpl->trigger_error("[SYNTAX]  missing 'item' attribute in 'foreach' tag", E_USER_ERROR, $gTpl->_file, $gTpl->_linenum);
    }
    $item = $gTpl->_dequote($attrs['item']);
    if (!preg_match('~^\w+$~', $item)) {
        return $gTpl->trigger_error("[SYNTAX] 'item' must be a variable name (literal string) in 'foreach' tag", E_USER_ERROR, $gTpl->_file, $gTpl->_linenum);
    }

    /* attr: key */
    if (isset($attrs['key'])) {
        $key  = $gTpl->_dequote($attrs['key']);
        if (!preg_match('~^\w+$~', $key)) {
            return $gTpl->trigger_error("[SYNTAX] 'key' must to be a variable name (literal string) in 'foreach' tag", E_USER_ERROR, $gTpl->_file, $gTpl->_linenum);
        }
        $key_part = "\$gTpl->_vars['$key'] => ";
    } else {
        $key = null;
        $key_part = '';
    }

    /* attr: name */
    if (isset($attrs['name'])) {
        $name = $attrs['name'];
    } else {
        $name = null;
    }


    /* Generate output */
    $output =    "<?php \n/* START of Foreach on {$from} */\n"
                ."\$_from = $from;\n"
                ."if (!is_array(\$_from) && !is_object(\$_from)) { settype(\$_from, 'array'); }\n";
    if (isset($name)) {
        $foreach_props = "\$gTpl->_foreach['$name']";
        $output .=   "{$foreach_props} = array('total' => count(\$_from), 'iteration' => 0);\n"
                    ."if ({$foreach_props}['total'] > 0):\n"
                    ."    foreach (\$_from as $key_part\$gTpl->_vars['$item']):\n"
                    ."        {$foreach_props}['iteration']++;\n"
                    ."        /* START of LOOP section */\n";

    } else {
        $output .=   "if (count(\$_from)):\n"
                    ."    foreach (\$_from as $key_part\$gTpl->_vars['$item']):\n"
                    ."    /* START of LOOP section */\n";
    }
    $output .= '?>';
    $gTpl->openTag('foreach', array('from' => $attrs['from']));
    return $output;
}
