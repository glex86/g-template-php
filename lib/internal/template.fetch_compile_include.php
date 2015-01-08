<?php
/**
 * gTemplate Internal Function
 * Include a subtemplate
 *
 * @package    gTemplate
 * @subpackage internalFunctions
 */

function template_fetch_compile_include($include_file, $include_vars, &$gTpl) {
    if ($gTpl->debug) {
        $gTpl->_debug_info[] = array('type' => 'template',
            'filename' => $include_file,
            'depth' => ++$gTpl->_inclusion_depth,
            'exec_time' => array_sum(explode(' ', microtime())));
        $included_tpls_idx = count($gTpl->_debug_info) - 1;
    }

    $gTpl->_vars = array_merge($gTpl->_vars, $include_vars);
    $include_file = $gTpl->_get_resource($include_file);
    if (isset($gTpl->_confs[0])) {
        array_unshift($gTpl->_confs, $gTpl->_confs[0]);
        $_compiled_output = $gTpl->_fetch_compile($include_file);
        array_shift($gTpl->_confs);
    } else {
        $_compiled_output = $gTpl->_fetch_compile($include_file);
    }

    $gTpl->_inclusion_depth--;

    if ($gTpl->debug) {
        $gTpl->_debug_info[$included_tpls_idx]['exec_time'] = array_sum(explode(' ', microtime())) - $gTpl->_debug_info[$included_tpls_idx]['exec_time'];
    }
    return $_compiled_output;
}
