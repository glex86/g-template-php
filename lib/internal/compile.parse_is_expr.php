<?php
/**
 * gTemplate Internal Function
 * Parses 'is' expressions in 'if' tag
 *
 * @package    gTemplate
 * @subpackage internalFunctions
 */


function compile_parse_is_expr($is_arg, $_args, &$gTpl)
{
    $expr_end = 0;
    $negate_expr = false;

    if (($first_arg = array_shift($_args)) == 'not') {
        $negate_expr = true;
        $expr_type = array_shift($_args);
    }
    else
    {
        $expr_type = $first_arg;
    }

    switch ($expr_type) {
        case 'even':
            if (isset($_args[$expr_end]) && $_args[$expr_end] == 'by')
            {
                $expr_end++;
                $expr_arg = $_args[$expr_end++];
                $expr = "!(1 & ($is_arg / " . $gTpl->_parse_variable($expr_arg) . "))";
            }
            else
            {
                $expr = "!(1 & $is_arg)";
            }
            break;

        case 'odd':
            if (isset($_args[$expr_end]) && $_args[$expr_end] == 'by')
            {
                $expr_end++;
                $expr_arg = $_args[$expr_end++];
                $expr = "(1 & ($is_arg / " . $gTpl->_parse_variable($expr_arg) . "))";
                }
                else
                {
                    $expr = "(1 & $is_arg)";
                }
                break;

            case 'div':
                if (@$_args[$expr_end] == 'by')
                {
                    $expr_end++;
                    $expr_arg = $_args[$expr_end++];
                    $expr = "!($is_arg % " . $gTpl->_parse_variable($expr_arg) . ")";
                }
                else
                {
                    $gTpl->trigger_error("[SYNTAX] expecting 'by' after 'div'", E_USER_ERROR, $gTpl->_file, $gTpl->_linenum);
                }
            break;

            default:
                $gTpl->trigger_error("[SYNTAX] unknown 'is' expression - '$expr_type'", E_USER_ERROR, $gTpl->_file, $gTpl->_linenum);
                break;
        }

    if ($negate_expr) {
        $expr = "!($expr)";
    }

    array_splice($_args, 0, $expr_end, $expr);

    return $_args;
}

