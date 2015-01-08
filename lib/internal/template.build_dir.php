<?php
/**
 * gTemplate Internal Function
 * Build subdirectories for caching functions
 *
 * @package    gTemplate
 * @subpackage internalFunctions
 */



function template_build_dir($dir, $id, &$gTpl) {
    $_args = explode('|', $id);
    if (count($_args) == 1 && empty($_args[0])) {
        return $gTpl->_get_dir($dir);
    }
    $_result = $gTpl->_get_dir($dir);
    foreach ($_args as $value) {
        $_result .= $value . DIRECTORY_SEPARATOR;
        if (!is_dir($_result)) {
            @mkdir($_result, 0777);
        }
    }
    return $_result;
}
