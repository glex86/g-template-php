<?php
/**
 * gTemplate Engine
 * https://github.com/glex86/g-template-php
 */

function tpl_inlinemodifier_cat($variable, $arguments, &$object)
{
    if (!$arguments[0]) {
        $object->trigger_error("[SYNTAX] 'cat' modifier requires one parameter", E_USER_NOTICE, $object->_file, $object->_linenum);
    }
    return $variable .'.'.$arguments[0];
}
