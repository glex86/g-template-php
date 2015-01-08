<?php
/**
 * gTemplate Engine
 * https://github.com/glex86/g-template-php
 */

function compile_inline($arguments, &$object) {
    $_args = $object->_parse_arguments($arguments);

    $arg_list = array();
    if (empty($_args['file'])) {
        $object->trigger_error("[SYNTAX] missing 'file' attribute in 'include' tag", E_USER_ERROR, $object->_file, $object->_linenum);
    }

    foreach ($_args as $arg_name => $arg_value) {
        if ($arg_name == 'file') {
            $include_file = str_replace(array('"', "'"), '', $arg_value);
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

    $file = $object->_get_resource($include_file);
    $output =    "<?php \n/* START of Inline include: {$include_file} */\n"
                ."\$gTpl->_get_resource('{$include_file}');\n"
                . "\$compiled_file = \$gTpl->getCompiledName('{$include_file}');\n"
                .'$subtpl_valid = $gTpl->_validate_compiled($gTpl->_resource_file, $gTpl->abs_compile_dir.$compiled_file);'."\n"
                .'if (!$subtpl_valid) { return "Need to recompile"; } '."\n?>"
                .$object->_fetch_compile($include_file)
                ."<?php /* END of Inline include: {$include_file} */ ?>";
    return $output;
}

?>
