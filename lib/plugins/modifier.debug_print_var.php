<?php
/**
 * gTemplate Plugins
 *
 * @package    gTemplate
 * @subpackage Plugins
 */

/**
 * gTemplate debug_print_var modifier plugin
 *
 * Type:     modifier<br>
 * Name:     debug_print_var<br>
 * Purpose:  formats variable contents for display in the console
 * 
 * @version 1.0
 * @author Tamas David (G-Lex) <glex at mittudomain.info>
 * @link https://github.com/glex86/g-template-php G-Template Engine on Github
 *
 * @internal Some source codes are taken from Smarty
 * @internal author Monte Ohrt <monte at ohrt dot com>
 * @internal link http://smarty.net Smarty
 */
function tpl_modifier_debug_print_var($var, $depth = 0, $length = 40)
{
    $_replace = array(
        "\n" => '<i>\n</i>',
        "\r" => '<i>\r</i>',
        "\t" => '<i>\t</i>'
    );

    switch (gettype($var)) {
        case 'array' :
            $results = '<b>Array (' . count($var) . ')</b>';
            foreach ($var as $curr_key => $curr_val) {
                $results .= '<br>' . str_repeat('&nbsp;', $depth * 2)
                    . '<b>' . strtr($curr_key, $_replace) . '</b> =&gt; '
                    . tpl_modifier_debug_print_var($curr_val, ++$depth, $length);
                    $depth--;
            }
            break;
        case 'object' :
            $object_vars = get_object_vars($var);
            $results = '<b>' . get_class($var) . ' Object (' . count($object_vars) . ')</b>';
            foreach ($object_vars as $curr_key => $curr_val) {
                $results .= '<br>' . str_repeat('&nbsp;', $depth * 2)
                    . '<b> -&gt;' . strtr($curr_key, $_replace) . '</b> = '
                    . tpl_modifier_debug_print_var($curr_val, ++$depth, $length);
                    $depth--;
            }
            break;
        case 'boolean' :
        case 'NULL' :
        case 'resource' :
            if (true === $var) {
                $results = 'true';
            } elseif (false === $var) {
                $results = 'false';
            } elseif (null === $var) {
                $results = 'null';
            } else {
                $results = htmlspecialchars((string) $var);
            }
            $results = '<i>' . $results . '</i>';
            break;
        case 'integer' :
        case 'float' :
            $results = htmlspecialchars((string) $var);
            break;
        case 'string' :
            $results = strtr($var, $_replace);
            if (strlen($var) > $length ) {
                $results = substr($var, 0, $length - 3) . '...';
            }
            $results = htmlspecialchars('"' . $results . '"');
            break;
        case 'unknown type' :
        default :
            $results = strtr((string) $var, $_replace);
            if (strlen($results) > $length ) {
                $results = substr($results, 0, $length - 3) . '...';
            }
            $results = htmlspecialchars($results);
    }

    return $results;
}
