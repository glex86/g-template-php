<?php
/**
 * gTemplate Internal Function
 * Include a subtemplate
 *
 * @package    gTemplate
 * @subpackage internalFunctions
 */

function template_fetch_compile_include($include_file, $include_vars, &$object) {
    if ($object->debug) {
        $object->_debug_info[] = array('type' => 'template',
            'filename' => $include_file,
            'depth' => ++$object->_inclusion_depth,
            'exec_time' => array_sum(explode(' ', microtime())));
        $included_tpls_idx = count($object->_debug_info) - 1;
    }

    $object->_vars = array_merge($object->_vars, $include_vars);
    $include_file = $object->_get_resource($include_file);
    if (isset($object->_confs[0])) {
        array_unshift($object->_confs, $object->_confs[0]);
        $_compiled_output = $object->_fetch_compile($include_file);
        array_shift($object->_confs);
    } else {
        $_compiled_output = $object->_fetch_compile($include_file);
    }

    $object->_inclusion_depth--;

    if ($object->debug) {
        $object->_debug_info[$included_tpls_idx]['exec_time'] = array_sum(explode(' ', microtime())) - $object->_debug_info[$included_tpls_idx]['exec_time'];
    }
    return $_compiled_output;
}
