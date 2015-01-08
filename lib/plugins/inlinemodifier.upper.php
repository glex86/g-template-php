<?php
/**
 * gTemplate Engine
 * https://github.com/glex86/g-template-php
 */

function tpl_inlinemodifier_upper($variable, $arguments, &$object) {    
    return "mb_strtoupper({$variable})";
}
