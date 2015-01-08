<?php
/**
 * gTemplate Internal Function
 * Compiles the 'inline' tag
 *
 * @package    gTemplate
 * @subpackage internalFunctions
 */

function compile_inline($arguments, &$gTpl) {
    $_args = $gTpl->_parse_arguments($arguments);

    $arg_list = array();
    if (empty($_args['file'])) {
        $gTpl->trigger_error("[SYNTAX] missing 'file' attribute in 'include' tag", E_USER_ERROR, $gTpl->_file, $gTpl->_linenum);
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

    $file = $gTpl->_get_resource($include_file);
    $output =    "<?php \n/* START of Inline include: {$include_file} */\n"
                ."\$gTpl->_get_resource('{$include_file}');\n"
                . "\$compiled_file = \$gTpl->getCompiledName('{$include_file}');\n"
                .'$subtpl_valid = $gTpl->_validate_compiled($gTpl->_resource_file, $gTpl->abs_compile_dir.$compiled_file);'."\n"
                .'if (!$subtpl_valid) { return "Need to recompile"; } '."\n?>"
                .$gTpl->_fetch_compile($include_file)
                ."<?php /* END of Inline include: {$include_file} */ ?>";
    return $output;
}

