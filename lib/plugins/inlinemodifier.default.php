<?php
/**
 * gTemplate Plugins
 *
 * @package    gTemplate
 * @subpackage Plugins
 */

/**
 * gTemplate cat modifier plugin
 *
 * Type:     inlinemodifier<br>
 * Name:     default<br>
 * Purpose:  designate default value for empty variables<br>
 * Example:  {$var|default:"foo"}
 * 
 * @version 1.0
 * @author Tamas David (G-Lex) <glex at mittudomain.info>
 * @link https://github.com/glex86/g-template-php G-Template Engine on Github
 */
function tpl_inlinemodifier_default($variable, $arguments, &$gTpl)
{
    if (!$arguments[0]) {
        $gTpl->trigger_error("[SYNTAX] 'cat' modifier requires one parameter", E_USER_NOTICE, $object->_file, $object->_linenum);
    }

    return "(string)$variable===''?{$arguments[0]}:$variable";
}
