<?php
/**
 * gTemplate Internal Function
 * Compiles the custom functions
 *
 * @package    gTemplate
 * @subpackage internalFunctions
 */

function compile_compile_custom_function($function, $modifiers, $arguments, &$_result, &$gTpl) {
    if ($function = $gTpl->_plugin_exists($function, "function")) {
        $_args = $gTpl->_parse_arguments($arguments);
        foreach ($_args as $key => $value) {
            if (is_bool($value)) {
                $value = $value ? 'true' : 'false';
            }
            if (is_null($value)) {
                $value = 'null';
            }
            $_args[$key] = "'$key' => $value";
        }
        $_result = '<?php echo ';
        if (!empty($modifiers)) {
            $_result .= $gTpl->_parse_modifier($function . '(array(' . implode(',', (array) $_args) . '), $gTpl)', $modifiers) . '; ';
        } else {
            $_result .= $function . '(array(' . implode(',', (array) $_args) . '), $gTpl);';
        }
        $_result .= '?>';
        return true;
    } else {
        return false;
    }
}
