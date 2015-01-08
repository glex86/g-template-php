<?php
/**
 * gTemplate Engine
 * https://github.com/glex86/g-template-php
 */
function compile_include($arguments, &$object) {
    $_args = $object->_parse_arguments($arguments);

    $arg_list = array();
    if (empty($_args['file'])) {
        $object->trigger_error("[SYNTAX] missing 'file' attribute in 'include' tag", E_USER_ERROR, $object->_file, $object->_linenum);
    }

    foreach ($_args as $arg_name => $arg_value) {
        if ($arg_name == 'file') {
            $include_file = $arg_value;
            continue;

        } else if ($arg_name == 'assign') {
            $assign_var = $arg_value;
            continue;
        }

        if (is_bool($arg_value)) {
            $arg_value = $arg_value ? 'true' : 'false';
        }
        $arg_list[] = "'$arg_name' => $arg_value";
    }

    if (isset($assign_var)) {
        $output = "<?php \n"
                . "/* START of Subtemplate include: {$include_file} */\n"
                . '$gTpl->assign(' . $assign_var . ', $gTpl->_fetch_compile_include(' . $include_file . ', array(' . implode(',', (array) $arg_list) . ')));' . "\n"
                . "/* END of Subtemplate include: {$include_file} */\n"
                . ' ?>';
    } else {
        $output = "<?php \n"
                . "/* START of Subtemplate include: {$include_file} */\n"
                . 'echo $gTpl->_fetch_compile_include(' . $include_file . ', array(' . implode(',', (array) $arg_list) . '));' . "\n"
                . "/* END of Subtemplate include: {$include_file} */\n"
                . ' ?>';
    }
    return $output;
}
