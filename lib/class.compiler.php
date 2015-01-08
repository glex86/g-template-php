<?php
/**
 * Project:     G-Template Engine
 * File:        class.compiler.php
 *
 * This library is free software; you can redistribute it and/or
 * modify it under the terms of the GNU Lesser General Public
 * License as published by the Free Software Foundation; either
 * version 2.1 of the License, or (at your option) any later version.
 *
 * This library is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the GNU
 * Lesser General Public License for more details.
 *
 * You should have received a copy of the GNU Lesser General Public
 * License along with this library; if not, write to the Free Software
 * Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA
 *
 * @version 1.0
 * @author Tamas David (G-Lex) <glex at mittudomain.info>
 * @link https://github.com/glex86/g-template-php G-Template Engine on Github
 *
 * @internal Some source codes are taken from Smarty
 * @internal author Monte Ohrt <monte at ohrt dot com>
 * @internal link http://smarty.net Smarty
 */

class gTemplateCompiler extends gTemplate {

    // public configuration variables
    var $_linenum = 1;  // the current line number in the file we are processing
    var $_currentTag = '';
    var $_file = "";  // the current file we are processing
    var $_literal = array(); // stores all literal blocks
    var $_tag_stack = array();
    var $_require_stack = array(); // stores all files that are "required" inside of the template
    var $_db_qstr_regexp = null;        // regexps are setup in the constructor
    var $_si_qstr_regexp = null;
    var $_qstr_regexp = null;
    var $_func_regexp = null;
    var $_reg_obj_regexp = null;
    var $_var_bracket_regexp = null;
    var $_num_const_regexp = null;
    var $_dvar_guts_regexp = null;
    var $_dvar_regexp = null;
    var $_cvar_regexp = null;
    var $_svar_regexp = null;
    var $_avar_regexp = null;
    var $_mod_regexp = null;
    var $_var_regexp = null;
    var $_parenth_param_regexp = null;
    var $_func_call_regexp = null;
    var $_obj_ext_regexp = null;
    var $_obj_start_regexp = null;
    var $_obj_params_regexp = null;
    var $_obj_call_regexp = null;

    function __construct() {
        // matches double quoted strings:
        // "foobar"
        // "foo\"bar"
        $this->_db_qstr_regexp = '"[^"\\\\]*(?:\\\\.[^"\\\\]*)*"';

        // matches single quoted strings:
        // 'foobar'
        // 'foo\'bar'
        $this->_si_qstr_regexp = '\'[^\'\\\\]*(?:\\\\.[^\'\\\\]*)*\'';

        // matches single or double quoted strings
        $this->_qstr_regexp = '(?:' . $this->_db_qstr_regexp . '|' . $this->_si_qstr_regexp . ')';

        // matches bracket portion of vars
        // [0]
        // [foo]
        // [$bar]
        $this->_var_bracket_regexp = '\[\$?[\w\.]+\]';

        // matches numerical constants
        // 30
        // -12
        // 13.22
        $this->_num_const_regexp = '(?:\-?\d+(?:\.\d+)?)';

        // matches $ vars (not objects):
        // $foo
        // $foo.bar
        // $foo.bar.foobar
        // $foo[0]
        // $foo[$bar]
        // $foo[5][blah]
        // $foo[5].bar[$foobar][4]
        $this->_dvar_math_regexp = '(?:[\+\*\/\%]|(?:-(?!>)))';
        $this->_dvar_math_var_regexp = '[\$\w\.\+\-\*\/\%\d\>\[\]]';
        $this->_dvar_guts_regexp = '\w+(?:' . $this->_var_bracket_regexp
                . ')*(?:\.\$?\w+(?:' . $this->_var_bracket_regexp . ')*)*(?:' . $this->_dvar_math_regexp . '(?:' . $this->_num_const_regexp . '|' . $this->_dvar_math_var_regexp . ')*)?';
        $this->_dvar_regexp = '\$' . $this->_dvar_guts_regexp;

        // matches config vars:
        // #foo#
        // #foobar123_foo#
        // #foo.bar#
        $this->_cvar_regexp = '\#\w+\#';
        $this->_cvar_regexp = '\#\w+\#|\#\w+\.\w+\#';

        // matches section vars:
        // %foo.bar%
        $this->_svar_regexp = '\%\w+\.\w+\%';

        // matches foreach vars:
        // @foo.bar
        $this->_fvar_regexp = '\{\w+\.\w+\}';

        // matches all valid variables (no quotes, no modifiers)
        $this->_avar_regexp = '(?:' . $this->_dvar_regexp . '|'
                . $this->_cvar_regexp . '|' . $this->_svar_regexp . '|' . $this->_fvar_regexp . ')';

        // matches valid variable syntax:
        // $foo
        // $foo
        // #foo#
        // #foo#
        // "text"
        // "text"
        $this->_var_regexp = '(?:' . $this->_avar_regexp . '|' . $this->_qstr_regexp . ')';

        // matches valid object call (one level of object nesting allowed in parameters):
        // $foo->bar
        // $foo->bar()
        // $foo->bar("text")
        // $foo->bar($foo, $bar, "text")
        // $foo->bar($foo, "foo")
        // $foo->bar->foo()
        // $foo->bar->foo->bar()
        // $foo->bar($foo->bar)
        // $foo->bar($foo->bar())
        // $foo->bar($foo->bar($blah,$foo,44,"foo",$foo[0].bar))
        $this->_obj_ext_regexp = '\->(?:\$?' . $this->_dvar_guts_regexp . ')';
        $this->_obj_restricted_param_regexp = '(?:'
                . '(?:' . $this->_var_regexp . '|' . $this->_num_const_regexp . ')(?:' . $this->_obj_ext_regexp . '(?:\((?:(?:' . $this->_var_regexp . '|' . $this->_num_const_regexp . ')'
                . '(?:\s*,\s*(?:' . $this->_var_regexp . '|' . $this->_num_const_regexp . '))*)?\))?)*)';
        $this->_obj_single_param_regexp = '(?:\w+|' . $this->_obj_restricted_param_regexp . '(?:\s*,\s*(?:(?:\w+|'
                . $this->_var_regexp . $this->_obj_restricted_param_regexp . ')))*)';
        $this->_obj_params_regexp = '\((?:' . $this->_obj_single_param_regexp
                . '(?:\s*,\s*' . $this->_obj_single_param_regexp . ')*)?\)';
        $this->_obj_start_regexp = '(?:' . $this->_dvar_regexp . '(?:' . $this->_obj_ext_regexp . ')+)';
        $this->_obj_call_regexp = '(?:' . $this->_obj_start_regexp . '(?:' . $this->_obj_params_regexp . ')?(?:' . $this->_dvar_math_regexp . '(?:' . $this->_num_const_regexp . '|' . $this->_dvar_math_var_regexp . ')*)?)';

        // matches valid modifier syntax:
        // |foo
        // |@foo
        // |foo:"bar"
        // |foo:$bar
        // |foo:"bar":$foobar
        // |foo|bar
        // |foo:$foo->bar
        $this->_mod_regexp = '(?:\|@?\w+(?::(?:\w+|' . $this->_num_const_regexp . '|'
                . $this->_obj_call_regexp . '|' . $this->_avar_regexp . '|' . $this->_qstr_regexp . '))*)';

        // matches valid function name:
        // foo123
        // _foo_bar
        $this->_func_regexp = '[a-zA-Z_]\w*';

        // matches valid registered object:
        // foo->bar
        $this->_reg_obj_regexp = '[a-zA-Z_]\w*->[a-zA-Z_]\w*';

        // matches valid parameter values:
        // true
        // $foo
        // $foo|bar
        // #foo#
        // #foo#|bar
        // "text"
        // "text"|bar
        // $foo->bar
        $this->_param_regexp = '(?:\s*(?:' . $this->_obj_call_regexp . '|'
                . $this->_var_regexp . '|' . $this->_num_const_regexp . '|\w+)(?>' . $this->_mod_regexp . '*)\s*)';

        // matches valid parenthesised function parameters:
        //
        // "text"
        //    $foo, $bar, "text"
        // $foo|bar, "foo"|bar, $foo->bar($foo)|bar
        $this->_parenth_param_regexp = '(?:\((?:\w+|'
                . $this->_param_regexp . '(?:\s*,\s*(?:(?:\w+|'
                . $this->_param_regexp . ')))*)?\))';

        // matches valid function call:
        // foo()
        // foo_bar($foo)
        // _foo_bar($foo,"bar")
        // foo123($foo,$foo->bar(),"foo")
        $this->_func_call_regexp = '(?:' . $this->_func_regexp . '\s*(?:'
                . $this->_parenth_param_regexp . '))';
    }

    function _compile_file($filename, $file_contents) {
        $functionHashName = 'gTemplate_TemplateContent_' . sha1($filename);

        $ldq = preg_quote($this->left_delimiter);
        $rdq = preg_quote($this->right_delimiter);
        $_match = array();              // a temp variable for the current regex match
        $tags = array();                // all original tags
        $text = array();                // all original text

        $compiled_banner = '<?php ' . "\n"
                . '/* ' . self::NAME . ' ' . self::VERSION . ' - Compiled template' . "\n"
                . ' * - Compiled on ' . strftime("%Y-%m-%d %H:%M:%S") . "\n"
                . ' * - From source template: ' . $filename . "\n" . ' */ ' . "\n"
                . 'function ' . $functionHashName . '( &$gTpl ) { ' . "\n" . '?>';

        $compiled_text = '';       // stores the compiled result
        $compiled_tags = array();  // all tags and stuff
        $this->_linenum = 1;

        $this->_require_stack = array();

        // remove all comments
        $file_contents = preg_replace("!{$ldq}\*.*?\*{$rdq}!se","",$file_contents);

        // replace all php start and end tags
        $file_contents = preg_replace('%(<\?(php|=|$)|\?>)%i', '<?php echo \'\\1\'?>', $file_contents);

        // remove literal blocks
        preg_match_all("!{$ldq}\s*literal\s*{$rdq}(.*?){$ldq}\s*/literal\s*{$rdq}!s", $file_contents, $_match);
        $this->_literal = $_match[1];
        $file_contents = preg_replace("!{$ldq}\s*literal\s*{$rdq}(.*?){$ldq}\s*/literal\s*{$rdq}!s", stripslashes($ldq . "literal" . $rdq), $file_contents);

        // gather all template tags
        preg_match_all("!{$ldq}\s*(.*?)\s*{$rdq}!s", $file_contents, $_match);
        $tags = $_match[1];

        // put all of the non-template tag text blocks into an array, using the template tags as delimiters
        $text = preg_split("!{$ldq}.*?{$rdq}!s", $file_contents);

        // compile template tags
        $count_tags = count($tags);
        for ($i = 0; $i < $count_tags; $i++) {
            $this->_linenum += substr_count($text[$i], "\n");
            $compiled_tags[] = $this->_compile_tag($tags[$i]);
            $this->_linenum += substr_count($tags[$i], "\n");
        }

        //Handle unclosed template tags
        if (count($this->_tag_stack)) {
            $unclosedStack = end($this->_tag_stack);
            $this->trigger_error('[SYNTAX] Unclosed '.$this->left_delimiter . $unclosedStack['tag'] .$this->right_delimiter. ' tag found! Opened in '.$this->_file.' on line ' . $unclosedStack['line'], E_USER_ERROR, $this->_file);
        }

        // build the compiled template by replacing and interleaving text blocks and compiled tags
        $count_compiled_tags = count($compiled_tags);
        for ($i = 0; $i < $count_compiled_tags; $i++) {
            if ($compiled_tags[$i] == '') {
                $text[$i + 1] = preg_replace('~^(\r\n|\r|\n)~', '', $text[$i + 1]); //Remove the first linebreak
            }
            $compiled_text .= $text[$i] . $compiled_tags[$i];
        }
        $compiled_text .= $text[$i];

        //Inserting plugin require stack
        if (count($this->_require_stack)) {
            $compiled_requirestack = "<?php \n/* START of Require Stack */\n?>";

            foreach ($this->_require_stack as $key => $value) {
                $compiled_requirestack .= '<?php require_once(\'' . $this->_get_plugin_dir($key) . $key . '\');' . "\n" . '$gTpl->register' . ucwords($value[0]) . '(\'' . $value[1] . '\', \'' . $value[2] . '\');' . "\n" . '?>';
            }

            $compiled_requirestack .= '<?php /* END of Require Stack */' . "\n" . '?>';

            $compiled_text = $compiled_requirestack.$compiled_text;
        }

        //Insert compiled banner and close the content function
        $compiled_text = $compiled_banner . $compiled_text . "\n" . '<?php } ?>';

        // remove unnecessary close/open tags
        $compiled_text = preg_replace('!\?>\n?<\?php !', '', $compiled_text);

        return $compiled_text;
    }

    function _compile_tag($tag) {
        //Skip Smarty style comments
        if (substr($tag, 0, 1) == '*' && substr($tag, -1) == '*') {
            return '';
        }


        $_match = array();  // stores the tags
        $_result = "";      // the compiled tag
        $_variable = "";    // the compiled variable
        // extract the tag command, modifier and arguments
        $regexp = '/(?:(' . $this->_obj_call_regexp . '|' .  $this->_var_regexp . '|' . $this->_svar_regexp . '|' . $this->_fvar_regexp . '|\/?' . $this->_func_regexp . ')(' . $this->_mod_regexp . '*)(?:\s*[,\.]\s*)?)(?:\s+(.*))?/xs';

        preg_match_all($regexp, $tag, $_match);
        if ($_match[1][0]{0} == '$' || ($_match[1][0]{0} == '#' && $_match[1][0]{strlen($_match[1][0]) - 1} == '#') || $_match[1][0]{0} == "'" || $_match[1][0]{0} == '"' || $_match[1][0]{0} == '%'
         || ($_match[1][0]{0} == '{' && $_match[1][0]{strlen($_match[1][0]) - 1} == '}')) {
            $_result = $this->_parse_variables($_match[1], $_match[2]);
            return "<?php echo $_result; ?>\n";
        }

        // process a function
        $tag_command = $_match[1][0];
        $tag_modifiers = !empty($_match[2][0]) ? $_match[2][0] : null;
        $tag_arguments = !empty($_match[3][0]) ? $_match[3][0] : null;
        $_result = $this->_parse_function($tag_command, $tag_modifiers, $tag_arguments);
        return $_result;
    }

    function _parse_function($function, $modifiers, $arguments) {
        $this->_currentTag = $function;
        switch ($function) {
            case 'include':
                if (!function_exists('compile_include')) {
                    require_once(G_TEMPLATE_BASE . "internal/compile.include.php");
                }
                return compile_include($arguments, $this);
                break;

            case 'ldelim':
                return $this->left_delimiter;
                break;

            case 'rdelim':
                return $this->right_delimiter;
                break;

            case 'literal':
                list (, $literal) = each($this->_literal);
                $this->_linenum += substr_count($literal, "\n");
                return "<?php echo '" . str_replace("'", "\'", str_replace("\\", "\\\\", $literal)) . "'; ?>\n";
                break;

            case 'foreach':
                if (!function_exists('compile_foreach_start')) {
                    require_once(G_TEMPLATE_BASE . "internal/compile.foreach_start.php");
                }
                return compile_foreach_start($arguments, $this);
                break;

            case 'foreachelse':
                $closedTag = $this->closeTag($function, 'foreach');
                $this->openTag('foreachelse');
                $_result =   "<?php \n/* END of LOOP section */\n"
                            ."endforeach; else: ?>";
                return $_result;
                break;

            case '/foreach':
                $closedTag = $this->closeTag($function, array('foreach', 'foreachelse'));
                if ($closedTag['tag'] == 'foreachelse') {
                    return   "<?php endif;\n"
                            ."/* END of Foreach */\n?>";
                } else {
                    return   "<?php \n/* END of LOOP section */\n"
                            ."endforeach; endif;\n"
                            ."/* END of Foreach */\n?>";
                }
                break;

            case 'section':
                if (!function_exists('compile_section_start')) {
                    require_once(G_TEMPLATE_BASE . "internal/compile.section_start.php");
                }
                $attrs = $this->_parse_arguments($arguments);
                $this->openTag('section', $attrs);
                return compile_section_start($attrs, $this);
                break;

            case 'sectionelse':
                $this->closeTag($function, 'section');
                $this->openTag('sectionelse');
                return "<?php endfor; else: ?>";
                break;

            case '/section':
                $closedTag = $this->closeTag($function, array('section', 'sectionelse'));

                if ($closedTag['tag'] == 'sectionelse') {
                    return "<?php endif; ?>";
                } else {
                    return "<?php endfor; endif; ?>";
                }
                break;

            case 'if':
                $this->openTag('if');
                return $this->_compile_if($arguments);
                break;

            case 'else':
                $this->closeTag($function, array('if', 'elseif'));
                $this->openTag('else');
                return "<?php else: ?>";
                break;

            case 'elseif':
                $this->closeTag($function, array('if', 'elseif'));
                $this->openTag('elseif');
                return $this->_compile_if($arguments, true);
                break;

            case '/if':
                $this->closeTag($function, array('if', 'elseif', 'else'));
                return "<?php endif; ?>";
                break;

            case 'assign':
                $_args = $this->_parse_arguments($arguments);
                if (!isset($_args['var'])) {
                    $this->trigger_error("[SYNTAX] missing 'var' attribute in 'assign'", E_USER_ERROR, $this->_file, $this->_linenum);
                }
                if (!isset($_args['value'])) {
                    $this->trigger_error("[SYNTAX] missing 'value' attribute in 'assign'", E_USER_ERROR, $this->_file, $this->_linenum);
                }
                if (is_bool($_args['value']))
                    $_args['value'] = $_args['value'] ? 'true' : 'false';
                return '<?php $gTpl->assign(\'' . $this->_dequote($_args['var']) . '\', ' . $_args['value'] . '); ?>';
                break;

            case 'config_load':
                $_args = $this->_parse_arguments($arguments);
                if (empty($_args['file'])) {
                    $this->trigger_error("[SYNTAX] missing 'file' attribute in 'config_load' tag", E_USER_ERROR, $this->_file, $this->_linenum);
                }
                isset($_args['section']) ? null : $_args['section'] = 'null';
                isset($_args['var']) ? null : $_args['var'] = 'null';
                return "<?php \n"
                        . "/* START of Config loader */\n"
                        . '$gTpl->config_load(' . $_args['file'] . ', ' . $_args['section'] . ', ' . $_args['var'] . ');' . "\n"
                        . "/* END of Config loader */\n?>";
                break;

            default:
                $_result = "";
                if ($this->_compile_compiler_function($function, $arguments, $_result)) {
                    return $_result;
                } else if ($this->_compile_custom_block($function, $modifiers, $arguments, $_result)) {
                    return $_result;
                } elseif ($this->_compile_custom_function($function, $modifiers, $arguments, $_result)) {
                    return $_result;
                } else {
                    $this->trigger_error('[COMPILER] function \''.$function .'\' does not exist', E_USER_ERROR, $this->_file, $this->_linenum);
                }
                break;
        }
    }

    function _compile_compiler_function($function, $arguments, &$_result) {
        if ($function = $this->_plugin_exists($function, "compiler")) {
            $_args = $this->_parse_arguments($arguments);
            $_result = '<?php ' . $function($_args, $this) . ' ?>';
            return true;
        } else {
            return false;
        }
    }

    function _compile_custom_function($function, $modifiers, $arguments, &$_result) {
        if (!function_exists('compile_compile_custom_function')) {
            require_once(G_TEMPLATE_BASE . "internal/compile.compile_custom_function.php");
        }
        return compile_compile_custom_function($function, $modifiers, $arguments, $_result, $this);
    }

    function _compile_custom_block($function, $modifiers, $arguments, &$_result) {
        if (!function_exists('compile_compile_custom_block')) {
            require_once(G_TEMPLATE_BASE . "internal/compile.compile_custom_block.php");
        }
        return compile_compile_custom_block($function, $modifiers, $arguments, $_result, $this);
    }

    function _compile_if($arguments, $elseif = false, $while = false) {
        if (!function_exists('compile_compile_if')) {
            require_once(G_TEMPLATE_BASE . "internal/compile.compile_if.php");
        }
        return compile_compile_if($arguments, $elseif, $while, $this);
    }

    function _parse_is_expr($is_arg, $_arg) {
        if (!function_exists('compile_parse_is_expr')) {
            require_once(G_TEMPLATE_BASE . "internal/compile.parse_is_expr.php");
        }
        return compile_parse_is_expr($is_arg, $_arg, $this);
    }

    function _compile_config($variable) {
        if (!function_exists('compile_compile_config')) {
            require_once(G_TEMPLATE_BASE . "internal/compile.compile_config.php");
        }
        return compile_compile_config($variable, $this);
    }

    function _dequote($string) {
        if (($string{0} == "'" || $string{0} == '"') && $string{strlen($string) - 1} == $string{0}) {
            return substr($string, 1, -1);
        } else {
            return $string;
        }
    }

    
    function _parse_arguments_error($state, $attribute, $value='') {
        switch ($state) {
            case 0:
                $this->trigger_error("[SYNTAX] invalid attribute name: '{$attribute}' in '{$this->_currentTag}' tag", E_USER_ERROR, $this->_file, $this->_linenum);
                break;

            case 1:
                $this->trigger_error("[SYNTAX] expecting '=' after '{$attribute}' because '{$attribute}' is not a switch flag in '{$this->_currentTag}' tag", E_USER_ERROR, $this->_file, $this->_linenum);
                break;

            case 2:
                $this->trigger_error("[SYNTAX] '{$value}' cannot be an attribute value in '{$this->_currentTag}' tag", E_USER_ERROR, $this->_file, $this->_linenum);
                break;
        }
    }
    
    function _parse_arguments($arguments) {        
        $_match = array();
        $_result = array();
        $_variables = array();       
        preg_match_all('~(?:' . $this->_obj_call_regexp . '|' . $this->_qstr_regexp . ' | (?>[^"\'=\s]+)
                         )+ |
                         [=]
                        ~x', $arguments, $_match);
        
        /*
          Parse state:
          0 - expecting attribute name
          1 - expecting '='
          2 - expecting attribute value (not '=')
         */
        $state = 0;
        $attribute = '';
        foreach ($_match[0] as $value) {
            switch ($state) {
                case 0:
                    // valid attribute name
                    if (is_string($value)) {
                        $attribute = $value;
                        $state = 1;
                    } else {
                        $this->_parse_arguments_error($state, $value);                        
                    }
                    break;
                case 1:
                    if ($value == '=') {
                        $state = 2;
                    } else {
                        $this->_parse_arguments_error($state, $attribute);                        
                    }
                    break;
                case 2:
                    if ($value != '=') {
                        if ($value == 'yes' || $value == 'on' || $value == 'true') {
                            $value = true;
                        } elseif ($value == 'no' || $value == 'off' || $value == 'false') {
                            $value = false;
                        } elseif ($value == 'null') {
                            $value = null;
                        }

                        if (!preg_match_all('/(?:('.$this->_obj_call_regexp.'|' . $this->_var_regexp . '|' . $this->_svar_regexp . ')(' . $this->_mod_regexp . '*))(?:\s+(.*))?/xs', $value, $_variables)) {
                            $_result[$attribute] = $value;
                        } else {
                            $_result[$attribute] = $this->_parse_variables($_variables[1], $_variables[2]);
                        }
                        $state = 0;
                    } else {                        
                        $this->_parse_arguments_error($state, $attribute, $value);
                    }
                    break;
            }
            $last_value = $value;
        }
        if ($state != 0) {
            $this->_parse_arguments_error($state, $attribute, $value);
        }
        return $_result;
    }

    function _parse_variables($variables, $modifiers) {
        $_result = "";
        foreach ($variables as $key => $value) {
            $tag_variable = trim($variables[$key]);
            if (!empty($this->default_modifiers) && !preg_match('!(^|\|)gtpl:nodefaults($|\|)!', $modifiers[$key])) {
                $_default_mod_string = implode('|', (array) $this->default_modifiers);
                $modifiers[$key] = empty($modifiers[$key]) ? $_default_mod_string : $_default_mod_string . '|' . $modifiers[$key];
            }
            if (empty($modifiers[$key])) {
                $_result .= $this->_parse_variable($tag_variable) . '.';
            } else {
                $_result .= $this->_parse_modifier($this->_parse_variable($tag_variable), $modifiers[$key]) . '.';
            }
        }
        return substr($_result, 0, -1);
    }

    function _parse_variable($variable) {
        // replace variable with value
        if ($variable{0} == '$') {
            // replace the variable
            return $this->_compile_variable($variable);
        } elseif ($variable{0} == '#') {
            // replace the config variable
            return $this->_compile_config($variable);
        } elseif ($variable{0} == '"') {
            // expand the quotes to pull any variables out of it
            // fortunately variables inside of a quote aren't fancy, no modifiers, no quotes
            //   just get everything from the $ to the ending space and parse it
            // if the $ is escaped, then we won't expand it
            $_result = "";
            preg_match_all('/(?:' . $this->_dvar_regexp . ')/', substr($variable, 1, -1), $_expand);  // old match
//          preg_match_all('/(?:[^\\\]' . $this->_dvar_regexp . '[^\\\])/', $variable, $_expand);

            $_expand = array_unique($_expand[0]);
            foreach ($_expand as $key => $value) {
                $_expand[$key] = trim($value);
                if (strpos($_expand[$key], '$') > 0) {
                    $_expand[$key] = substr($_expand[$key], strpos($_expand[$key], '$'));
                }
            }
            $_result = $variable;
            foreach ($_expand as $value) {
                $value = trim($value);
                $_result = str_replace($value, '" . ' . $this->_parse_variable($value) . ' . "', $_result);
            }
            $_result = str_replace("`", "", $_result);
            return $_result;
        } elseif ($variable{0} == "'") {
            // return the value just as it is
            return $variable;
        } elseif ($variable{0} == "%") {            
            return $this->_parse_section_prop($variable);
        } elseif ($variable{0} == "{") {
            return $this->_parse_foreach_prop(substr($variable,1,-1));
        } else {
            // return it as is; i believe that there was a reason before that i did not just return it as is,
            // but i forgot what that reason is ...
            // the reason i return the variable 'as is' right now is so that unquoted literals are allowed
            return $variable;
        }
    }


    /*
     * SECTION PROPERTIES
     */
    function _parse_section_prop($section_prop_expr) {
        $parts = explode('|', $section_prop_expr, 2);
        $var_ref = $parts[0];
        $modifiers = isset($parts[1]) ? $parts[1] : '';

        preg_match('!%(\w+)\.(\w+)%!', $var_ref, $match);
        $section_name = $match[1];
        $prop_name = $match[2];

        $output = $this->_compile_section_prop($section_name, $prop_name);

        $this->_parse_modifier($output, $modifiers);

        return $output;
    }

    function _compile_section_prop($_var, $prop) {
        $compiled_ref = '';

        switch ($prop) {
            case 'first':
                $compiled_ref = "(\$gTpl->_sections['$_var']['iteration'] == 1)";
                break;

            case 'last':
                $compiled_ref = "(\$gTpl->_sections['$_var']['iteration'] == \$gTpl->_sections['$_var']['total'])";
                break;

            default:
                $compiled_ref = "(\$gTpl->_sections['$_var']['$prop'])";
                break;
        }

        return $compiled_ref;
    }


    /*
     * FOREACH PROPERTIES
     */
    function _parse_foreach_prop($prop) {
        $parts = explode('.', $prop);

        return $this->_compile_foreach_prop($parts[0], $parts[1]);
    }

    function _compile_foreach_prop($_var, $prop) {
        $compiled_ref = '';
        switch ($prop) {
            case 'index':
                $compiled_ref = "(\$gTpl->_foreach[$_var]['iteration']-1)";
                break;

            case 'first':
                $compiled_ref = "(\$gTpl->_foreach[$_var]['iteration'] <= 1)";
                break;

            case 'last':
                $compiled_ref = "(\$gTpl->_foreach[$_var]['iteration'] == \$this->_foreach[$_var]['total'])";
                break;

            case 'show':
                $compiled_ref = "(\$gTpl->_foreach[$_var]['total'] > 0)";
                break;

            default:
                $compiled_ref = "\$gTpl->_foreach[$_var][$prop]";
        }

        return $compiled_ref;
    }

    function _compile_variable($variable) {
        $_result = "";

        // remove the $
        $variable = substr($variable, 1);

        // get [foo] and .foo and (...) pieces
        preg_match_all('!(?:^\w+)|(?:' . $this->_var_bracket_regexp . ')|\.\$?\w+|\S+!', $variable, $_match);
        $variable = $_match[0];
        $var_name = array_shift($variable);

        if ($var_name == $this->reserved_template_varname) {
            if ($variable[0]{0} == '[' || $variable[0]{0} == '.') {
                $find = array("[", "]", ".");
                switch (strtoupper(str_replace($find, "", $variable[0]))) {
                    case 'GET':
                        $_result = "\$_GET";
                        break;
                    case 'POST':
                        $_result = "\$_POST";
                        break;
                    case 'COOKIE':
                        $_result = "\$_COOKIE";
                        break;
                    case 'ENV':
                        $_result = "\$_ENV";
                        break;
                    case 'SERVER':
                        $_result = "\$_SERVER";
                        break;
                    case 'SESSION':
                        $_result = "\$_SESSION";
                        break;
                    case 'NOW':
                        $_result = "time()";
                        break;
                    case 'FOREACH':
                        $_result = "\$gTpl->_foreach";
                        return $this->_compile_foreach_prop(substr($variable[1], 1), substr($variable[2], 1));
                        break;
                    case 'SECTION':
                        $_result = "\$gTpl->_sections";
                        break;
                    case 'LDELIM':
                        $_result = "\$gTpl->left_delimiter";
                        break;
                    case 'RDELIM':
                        $_result = "\$gTpl->right_delimiter";
                        break;
                    case 'VERSION':
                        $_result = "self::NAME.' '.self::VERSION";
                        break;
                    case 'CONFIG':
                        $_result = "\$gTpl->_confs";
                        break;
                    case 'TEMPLATE':
                        $_result = "\$gTpl->_file";
                        break;
                    case 'CONST':
                        $constant = str_replace($find, "", $_match[0][2]);
                        $_result = "constant('$constant')";
                        $variable = array();
                        break;
                    default:

                        $_var_name = str_replace($find, "", $variable[0]);
                        $this->trigger_error('[COMPILER] $'.$this->reserved_template_varname.implode('', $variable) . ' is an invalid $'.$this->reserved_template_varname.' reference', E_USER_ERROR, $this->_file, $this->_linenum);
                        $_result = "\$gTpl->_vars['$_var_name']";
                        break;
                }
                array_shift($variable);
            } else {
                $this->trigger_error('[COMPILER] $' . $var_name . implode('', $variable) . ' is an invalid reference', E_USER_ERROR, $this->_file, $this->_linenum);
            }
        } else {
            $_result = "\$gTpl->_vars['$var_name']";
        }

        foreach ($variable as $var) {
            if ($var{0} == '[') {
                $var = substr($var, 1, -1);
                if (is_numeric($var)) {
                    $_result .= "[$var]";
                } elseif ($var{0} == '$') {
                    $_result .= "[" . $this->_compile_variable($var) . "]";
                } elseif ($var{0} == '#') {
                    $_result .= "[" . $this->_compile_config($var) . "]";
                } else {
                    $inSection = false;
                    foreach ($this->_tag_stack as $stack) {
                        $parts = explode('.', $var);
                        $section = $parts[0];
                        $section_prop = isset($parts[1]) ? $parts[1] : 'index';
                        if ($stack['tag'] == 'section' && $stack['params']['name'] == $section) {
                            $inSection = true;
                        }
                    }
                            

                    if ($inSection) {
                        $_result .= "[\$gTpl->_sections['$section']['$section_prop']]";
                    } else {
                        $_result .= "['$var']";
                    }
                }
            } else if ($var{0} == '.') {
                if ($var{1} == '$') {
                    $_result .= "[\$gTpl->_vars['" . substr($var, 2) . "']]";
                } else {
                    $_result .= "['" . substr($var, 1) . "']";
                }
            } else if (substr($var, 0, 2) == '->') {
                if (substr($var, 2, 2) == '__') {
                    $this->trigger_error('[COMPILER] call to internal object members is not allowed', E_USER_ERROR, $this->_file, $this->_linenum);
                } else if (substr($var, 2, 1) == '$') {
                    $_result .= '->{(($var=$gTpl->_vars[\'' . substr($var, 3) . '\']) && substr($var,0,2)!=\'__\') ? $_var : $gTpl->trigger_error("cannot access property \\"$var\\"")}';
                } else {
                    $_result .= $var;
                }

            } else {
                //$this->trigger_error('$' . $var_name.implode('', $variable) . ' is an invalid reference', E_USER_ERROR, __FILE__, __LINE__);
                $_result .= ' . \'' . implode('', $variable) . '\'';
            }
        }
        return $_result;
    }

    function _parse_modifier($variable, $modifiers) {
        $_match = array();
        $_mods = array();  // stores all modifiers
        $_args = array();  // modifier arguments

        preg_match_all('!\|(@?\w+)((?>:(?:' . $this->_qstr_regexp . '|[^|]+))*)!', '|' . $modifiers, $_match);
        list(, $_mods, $_args) = $_match;

        $count_mods = count($_mods);
        for ($i = 0, $for_max = $count_mods; $i < $for_max; $i++) {
            preg_match_all('!:(' . $this->_qstr_regexp . '|[^:]+)!', $_args[$i], $_match);
            $_arg = $_match[1];

            foreach ($_arg as $key => $value) {
                $_arg[$key] = $this->_parse_variable($value);
            }

            if (count($_arg) > 0) {
                $_argString = ', ' . implode(', ', $_arg);
            } else {
                $_argString = '';
            }            
            
            if ($pluginName = $this->_plugin_exists($_mods[$i], "inlinemodifier", true)) {
                $variable = $pluginName($variable, $_arg, $this);

            } elseif ($this->_plugin_exists($_mods[$i], "modifier")) {
                $variable = "{$this->plugin_prefix}_modifier_{$_mods[$i]}($variable$_argString)";
            } elseif (function_exists($_mods[$i])) {
                $variable = "{$_mods[$i]}($variable$_argString)";
            } else {
                $this->trigger_error("[COMPILER] '" . $_mods[$i] . "' modifier does not exist", E_USER_NOTICE, $this->_file, $this->_linenum);
            }
        }
        return $variable;
    }

    function _plugin_exists($function, $type, $inline = false) {
        // check for standard functions
        if (isset($this->_plugins[$type][$function]) && function_exists($this->_plugins[$type][$function])) {
            return $this->_plugins[$type][$function];
        }

        // check for a plugin in the plugin directory
        $plugin_filepath = $this->_get_plugin_filepath($type, $function);
        if (file_exists($plugin_filepath)) {
            require_once($plugin_filepath);
            if (function_exists($this->plugin_prefix . '_' . $type . '_' . $function)) {
                if (!$inline) {
                    $this->_require_stack[$type . '.' . $function . '.php'] = array($type, $function, $this->plugin_prefix . '_' . $type . '_' . $function);
                }
                
                $this->_plugins[$type][$function] = $this->plugin_prefix . '_' . $type . '_' . $function;
                return ($this->plugin_prefix . '_' . $type . '_' . $function);
            }
        }
        return false;
    }

    function openTag($tag, $params = array()) {
        $this->_tag_stack[] = array('tag' => $tag, 'line' => $this->_linenum, 'params' => $params);
    }

    function closeTag($function, $tag) {
        $lastTag = end($this->_tag_stack);

        if (!is_array($tag)) {
            $tag = array($tag);
        }
        if (!in_array($lastTag['tag'], $tag)) {
            $this->trigger_error('[SYNTAX] Unexpected closing tag: '.$this->left_delimiter.$function.$this->right_delimiter.' found! Missing '.$this->left_delimiter.implode($this->right_delimiter.' or '.$this->left_delimiter, $tag).$this->right_delimiter. ' opening tag', E_USER_ERROR, $this->_file);
        }

        array_pop($this->_tag_stack);
        return $lastTag;
    }
    
}
