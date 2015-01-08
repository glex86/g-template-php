<?php
/**
 * gTemplate Engine
 * https://github.com/glex86/g-template-php
 */

function tpl_inlinemodifier_strip($variable, $arguments, &$object) {
    $replace = $arguments[0] ? $arguments[0] : "' '";
    return "preg_replace('!\s+!', {$replace}, {$variable})";
}
