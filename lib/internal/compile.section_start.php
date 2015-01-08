<?php
/**
 * gTemplate Internal Function
 * Compiles the 'section' tag
 *
 * @package    gTemplate
 * @subpackage internalFunctions
 */


function compile_section_start($attrs, &$gTpl) {
        $arg_list = array();

        $output =   "<?php \n/* START of Section */\n";

        $section_name = $attrs['name'];
        /* Required attr: name */
        if (empty($section_name)) {
            $gTpl->trigger_error("[SYNTAX] missing 'name' attribute in 'section' tag", E_USER_ERROR, $gTpl->_file, $gTpl->_linenum);
        }

        /* Required attr: loop */
        if (empty($attrs['loop'])) {
            $gTpl->trigger_error("[SYNTAX] missing 'loop' attribute in 'section' tag", E_USER_ERROR, $gTpl->_file, $gTpl->_linenum);
        }

        $output .= "\$gTpl->_sections[$section_name] = array();\n";
        $section_props = "\$gTpl->_sections['$section_name']";

        $output .= '/* Setting predefined attributes */'."\n";
        foreach ($attrs as $attr_name => $attr_value) {
            switch ($attr_name) {
                case 'name': break;

                case 'loop':
                    $output .= "{$section_props}['loop'] = is_array(\$_loop=$attr_value) ? count(\$_loop) : max(0, (int)\$_loop); unset(\$_loop);\n";
                    break;

                case 'show':
                    if (is_bool($attr_value))
                        $show_attr_value = $attr_value ? 'true' : 'false';
                    else
                        $show_attr_value = "(bool)$attr_value";
                    $output .= "{$section_props}['show'] = $show_attr_value;\n";
                    break;

                case 'max':
                case 'start':
                    $output .= "{$section_props}['$attr_name'] = (int)$attr_value;\n";
                    break;

                case 'step':
                    $output .= "{$section_props}['$attr_name'] = ((int)$attr_value) == 0 ? 1 : (int)$attr_value;\n";
                    break;

                default:
                    $gTpl->trigger_error("[SYNTAX] unknown attribute '$attr_name' in 'section' tag", E_USER_ERROR, $gTpl->_file, $gTpl->_linenum);
                    break;
            }
        }

        $output .= '/* END of Setting predefined attributes */'."\n";
        $output .= "\n".'/* Setting default values for attributes */'."\n";

        if (!isset($attrs['show']))
            $output .= "{$section_props}['show'] = true;\n";

        if (!isset($attrs['loop']))
            $output .= "{$section_props}['loop'] = 1;\n";

        if (!isset($attrs['max']))
            $output .= "{$section_props}['max'] = {$section_props}['loop'];\n";
        else
            $output .= "if ({$section_props}['max'] < 0)\n" .
                       "    {$section_props}['max'] = {$section_props}['loop'];\n";

        if (!isset($attrs['step']))
            $output .= "{$section_props}['step'] = 1;\n";

        if (!isset($attrs['start']))
            $output .= "{$section_props}['start'] = {$section_props}['step'] > 0 ? 0 : {$section_props}['loop']-1;\n";
        else {
            $output .= "if ({$section_props}['start'] < 0)\n" .
                       "    {$section_props}['start'] = max({$section_props}['step'] > 0 ? 0 : -1, {$section_props}['loop'] + {$section_props}['start']);\n" .
                       "else\n" .
                       "    {$section_props}['start'] = min({$section_props}['start'], {$section_props}['step'] > 0 ? {$section_props}['loop'] : {$section_props}['loop']-1);\n";
        }

        $output .= "/* Determine Total loops */\n"
                  ."if ({$section_props}['show']) {\n";
        if (!isset($attrs['start']) && !isset($attrs['step']) && !isset($attrs['max'])) {
            $output .= "    {$section_props}['total'] = {$section_props}['loop'];\n";
        } else {
            $output .= "    {$section_props}['total'] = min(ceil(({$section_props}['step'] > 0 ? {$section_props}['loop'] - {$section_props}['start'] : {$section_props}['start']+1)/abs({$section_props}['step'])), {$section_props}['max']);\n";
        }
        $output .= "    if ({$section_props}['total'] == 0)\n" .
                   "        {$section_props}['show'] = false;\n" .
                   "} else\n" .
                   "    {$section_props}['total'] = 0;\n";

    $output .= "/* END of Determine Total loops */\n";
        $output .= '/* END of Setting default values for attributes */'."\n";

        $output .= "if ({$section_props}['show']):";

    $output .= "
        for ({$section_props}['index'] = {$section_props}['start'], {$section_props}['iteration'] = 1;
             {$section_props}['iteration'] <= {$section_props}['total'];
             {$section_props}['index'] += {$section_props}['step'], {$section_props}['iteration']++):\n";

        $output .= "?>";

        return $output;


        $output .= "
            for ({$section_props}['index'] = {$section_props}['start'], {$section_props}['iteration'] = 1;
                 {$section_props}['iteration'] <= {$section_props}['total'];
                 {$section_props}['index'] += {$section_props}['step'], {$section_props}['iteration']++):\n";
        $output .= "{$section_props}['rownum'] = {$section_props}['iteration'];\n";
        $output .= "{$section_props}['index_prev'] = {$section_props}['index'] - {$section_props}['step'];\n";
        $output .= "{$section_props}['index_next'] = {$section_props}['index'] + {$section_props}['step'];\n";
        $output .= "{$section_props}['first']      = ({$section_props}['iteration'] == 1);\n";
        $output .= "{$section_props}['last']       = ({$section_props}['iteration'] == {$section_props}['total']);\n";

        $output .= "?>";

        return $output;


}
