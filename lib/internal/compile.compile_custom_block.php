<?php
/**
 * gTemplate Internal Function
 * Compiles the custom block functions
 *
 * @package    gTemplate
 * @subpackage internalFunctions
 */

function compile_compile_custom_block($function, $modifiers, $arguments, &$_result, &$gTpl) {
    if ($function{0} == '/') {
        $start_tag = false;
        $function = substr($function, 1);
    } else {
        $start_tag = true;
    }
    $oFunction = $function;

    if ($function = $gTpl->_plugin_exists($function, "block")) {
        if ($start_tag) {
            $_args = $gTpl->_parse_arguments($arguments);
            foreach ($_args as $key => $value) {
                if (is_bool($value)) {
                    $value = $value ? 'true' : 'false';
                }
                if (is_null($value)) {
                    $value = 'null';
                }
                $_args[$key] = "'$key' => $value";
            }
            $gTpl->openTag($oFunction);
            $_result = "<?php /* START of '$oFunction' block function */\n"
                      ."\$gTpl->_tag_stack[] = array('$function', array(" . implode(',', (array) $_args) . ")); \n"
                      .$function . '(array(' . implode(',', (array) $_args) . '), null, $gTpl); '."\n"
                      .'ob_start(); ?>';
        } else {
            $gTpl->closeTag($oFunction, $oFunction);
            $_result .= '<?php $gTpl->_block_content = ob_get_contents();'."\n"
                       .'ob_end_clean(); '."\n"
                       .'$gTpl->_block_content = ' . $function . '($gTpl->_tag_stack[count($gTpl->_tag_stack) - 1][1], $gTpl->_block_content, $gTpl); '."\n";

            if (!empty($modifiers)) {
                $_result .= '$gTpl->_block_content = ' . $gTpl->_parse_modifier('$gTpl->_block_content', $modifiers) . '; '."\n";
            }

            $_result .= 'echo $gTpl->_block_content;'."\n"
                       .'array_pop($gTpl->_tag_stack);'."\n"
                       ."/* END of '$oFunction' block function */ ?>";
        }
        return true;
    } else {
        return false;
    }
}

