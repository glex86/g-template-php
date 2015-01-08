<?php

/*
 * Template Lite plugin converted from Smarty
 * -------------------------------------------------------------
 * Type:     function
 * Name:     debug
 * Version:  1.0
 * Date:     July 1, 2002
 * Author:	 Monte Ohrt <monte@ispi.net>
 * Purpose:  popup debug window
 * -------------------------------------------------------------
 */
function tpl_compiler_debug($params, &$gTpl)
{
	if($params['output'])
	{
	    $debug_output = '$gTpl->assign("_debug_output", ' . $params['output'] . ');';
	}
	else
	{
		$debug_output = "";
	}

	if(!function_exists("generate_compiler_debug_output"))
	{
		require_once(G_TEMPLATE_BASE . "internal/compile.generate_compiler_debug_output.php");
	}
	$debug_output .= generate_compiler_debug_output($tpl);
	return $debug_output;
}

