<?php
/**
 * gTemplate Engine
 */
function compile_foreach_start($arguments, &$object) {
    $attrs = $object->_parse_arguments($arguments);
    $arg_list = array();

    /* Required attr: from */
    if (empty($attrs['from'])) {
        return $object->trigger_error("foreach: missing 'from' attribute", E_USER_ERROR, __FILE__, __LINE__);
    }
    $from = $attrs['from'];

    /* Required attr: item */
    if (empty($attrs['item'])) {
        return $object->trigger_error("foreach: missing 'item' attribute", E_USER_ERROR, __FILE__, __LINE__);
    }
    $item = $object->_dequote($attrs['item']);
    if (!preg_match('~^\w+$~', $item)) {
        return $object->trigger_error("foreach: 'item' must be a variable name (literal string)", E_USER_ERROR, __FILE__, __LINE__);
    }

    /* attr: key */
    if (isset($attrs['key'])) {
        $key  = $object->_dequote($attrs['key']);
        if (!preg_match('~^\w+$~', $key)) {
            return $object->trigger_error("foreach: 'key' must to be a variable name (literal string)", E_USER_ERROR, __FILE__, __LINE__);
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

    $object->openTag('foreach', array('from' => $_args['from']));    
    return $output;                
}
