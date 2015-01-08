<?php
/**
 * gTemplate Internal Function
 * generate variables for the debug output
 *
 * @package    gTemplate
 * @subpackage internalFunctions
 */

function generate_compiler_debug_output(&$object)
{
    $debug_output = "\$assigned_vars = \$gTpl->_vars;\n";
    $debug_output .= "ksort(\$assigned_vars);\n";
    $debug_output .= "if (@is_array(\$gTpl->_confs)) {\n";
    $debug_output .= "    \$config_vars = \$gTpl->_confs;\n";
    $debug_output .= "    ksort(\$config_vars);\n";
    $debug_output .= "    \$gTpl->assign('_debug_config_keys', array_keys(\$config_vars));\n";
    $debug_output .= "    \$gTpl->assign('_debug_config_vals', array_values(\$config_vars));\n";
    $debug_output .= "}   \n";

    $debug_output .= "\$included_templates = \$gTpl->_debug_info;\n";

    $debug_output .= "\$gTpl->assign('_debug_keys', array_keys(\$assigned_vars));\n";
    $debug_output .= "\$gTpl->assign('_debug_vals', array_values(\$assigned_vars));\n";
    $debug_output .= "\$gTpl->assign('_debug_tpls', \$included_templates);\n";

    $debug_output .= "\$gTpl->left_delimiter = '{';\n";
    $debug_output .= "\$gTpl->right_delimiter = '}';\n";
    $debug_output .= "\$gTpl->_debug_loop = true;\n";
    $debug_output .= "\$gTpl->_debug_dir = \$gTpl->template_dir;\n";
    $debug_output .= "\$gTpl->setTemplateDir(G_TEMPLATE_BASE . 'internal/');\n";
    $debug_output .= "echo \$gTpl->_fetch_compile('debug.tpl');\n";
    $debug_output .= "\$gTpl->setTemplateDir(\$gTpl->_debug_dir);\n";
    $debug_output .= "\$gTpl->_debug_loop = false; \n";
    return $debug_output;
}
