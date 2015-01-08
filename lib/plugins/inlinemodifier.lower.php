<?php
/**
 * gTemplate Engine
 * https://github.com/glex86/g-template-php
 */

function tpl_modifier_lower($variable, $arguments, &$object) {
    return "mb_strtolower({$variable})";
}
