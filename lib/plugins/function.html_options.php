<?php
/**
 * gTemplate Plugins
 *
 * @package    gTemplate
 * @subpackage Plugins
 */

/**
 * gTemplate {html_options} function plugin
 * Type:     function<br>
 * Name:     html_options<br>
 * Purpose:  Prints the list of <option> tags generated from
 *           the passed parameters<br>
 * Params:
 * <pre>
 * - name       (optional) - string default "select"
 * - values     (required) - if no options supplied) - array
 * - options    (required) - if no values supplied) - associative array
 * - selected   (optional) - string default not set
 * - output     (required) - if not options supplied) - array
 * - id         (optional) - string default not set
 * - class      (optional) - string default not set
 * </pre>
 *
 * @version 1.0
 * @author Tamas David (G-Lex) <glex at mittudomain.info>
 * @link https://github.com/glex86/g-template-php G-Template Engine on Github
 *
 * @internal Some source codes are taken from Smarty
 * @internal author   Monte Ohrt <monte at ohrt dot com>
 * @internal author   Ralf Strehle (minor optimization) <ralf dot strehle at yahoo dot de>
 * @internal link http://smarty.net Smarty
 */
function tpl_function_html_options($params, &$gTpl) {
    require_once('shared.escape_chars.php');

    $name = null;
    $values = null;
    $options = null;
    $selected = null;
    $output = null;
    $id = null;
    $class = null;

    $extra = '';

    foreach ($params as $_key => $_val) {
        switch ($_key) {
            case 'name':
            case 'class':
            case 'id':
                $$_key = (string) $_val;
                break;

            case 'options':
                $options = (array) $_val;
                break;

            case 'values':
            case 'output':
                $$_key = array_values((array) $_val);
                break;

            case 'selected':
                if (is_array($_val)) {
                    $selected = array();
                    foreach ($_val as $_sel) {
                        $_sel = tpl_escape_chars((string) $_sel);
                        $selected[$_sel] = true;
                    }
                } else {
                    $selected = tpl_escape_chars((string) $_val);
                }
                break;

            case 'disabled':
            case 'readonly':
                if (!is_scalar($_val)) {
                    $gTpl->trigger_error("[SYNTAX] $_key attribute must be a scalar, only boolean true or string '$_key' will actually add the attribute in 'html_options' tag", E_USER_NOTICE, $gTpl->_file, $gTpl->_linenum);
                }

                if ($_val === true || $_val === $_key) {
                    $extra .= ' ' . $_key . '="' . tpl_escape_chars($_key) . '"';
                }

                break;

            default:
                if (!is_array($_val)) {
                    $extra .= ' ' . $_key . '="' . tpl_escape_chars($_val) . '"';
                } else {
                    $gTpl->trigger_error("[SYNTAX] extra attribute '$_key' cannot be an array in 'html_options' tag", E_USER_NOTICE, $gTpl->_file, $gTpl->_linenum);
                }
                break;
        }
    }

    if (!isset($options) && !isset($values)) {
        /* raise error here? */

        return '';
    }

    $_html_result = '';
    $_idx = 0;

    if (isset($options)) {
        foreach ($options as $_key => $_val) {
            $_html_result .= tpl_function_html_options_optoutput($_key, $_val, $selected, $id, $class, $_idx);
        }
    } else {
        foreach ($values as $_i => $_key) {
            $_val = isset($output[$_i]) ? $output[$_i] : '';
            $_html_result .= tpl_function_html_options_optoutput($_key, $_val, $selected, $id, $class, $_idx);
        }
    }

    if (!empty($name)) {
        $_html_class = !empty($class) ? ' class="' . $class . '"' : '';
        $_html_id = !empty($id) ? ' id="' . $id . '"' : '';
        $_html_result = '<select name="' . $name . '"' . $_html_class . $_html_id . $extra . '>' . "\n" . $_html_result . '</select>' . "\n";
    }

    return $_html_result;
}

function tpl_function_html_options_optoutput($key, $value, $selected, $id, $class, &$idx) {
    if (!is_array($value)) {
        $_key = tpl_escape_chars($key);
        $_html_result = '<option value="' . $_key . '"';
        if (is_array($selected)) {
            if (isset($selected[$_key])) {
                $_html_result .= ' selected="selected"';
            }
        } elseif ($_key === $selected) {
            $_html_result .= ' selected="selected"';
        }
        $_html_class = !empty($class) ? ' class="' . $class . ' option"' : '';
        $_html_id = !empty($id) ? ' id="' . $id . '-' . $idx . '"' : '';
        $value = tpl_escape_chars((string) $value);
        $_html_result .= $_html_class . $_html_id . '>' . $value . '</option>' . "\n";
        $idx ++;
    } else {
        $_idx = 0;
        $_html_result = tpl_function_html_options_optgroup($key, $value, $selected, !empty($id) ? ($id . '-' . $idx) : null, $class, $_idx);
        $idx ++;
    }

    return $_html_result;
}

function tpl_function_html_options_optgroup($key, $values, $selected, $id, $class, &$idx) {
    $optgroup_html = '<optgroup label="' . tpl_escape_chars($key) . '">' . "\n";
    foreach ($values as $key => $value) {
        $optgroup_html .= tpl_function_html_options_optoutput($key, $value, $selected, $id, $class, $idx);
    }
    $optgroup_html .= "</optgroup>\n";

    return $optgroup_html;
}
