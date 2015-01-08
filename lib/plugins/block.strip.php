<?php
/**
 * gTemplate Plugins
 *
 * @package    gTemplate
 * @subpackage Plugins
 */

/**
 * gTemplate {strip}{/strip} block plugin
 *
 * Type:     block function<br>
 * Name:     strip<br>
 * Purpose:  strip unwanted white space from text<br>
 * 
 * @version 1.0
 * @author Tamas David (G-Lex) <glex at mittudomain.info>
 * @link https://github.com/glex86/g-template-php G-Template Engine on Github
 *
 * @internal Some source codes are taken from Smarty
 * @internal author Monte Ohrt <monte at ohrt dot com>
 * @internal link http://smarty.net Smarty
 */
function tpl_block_strip($params, $content, &$gTpl)
{
	$_strip_search = array(
		"![\t ]+$|^[\t ]+!m",		// remove leading/trailing space chars
		'%[\r\n]+%m',			// remove CRs and newlines
	);
	$_strip_replace = array(
		'',
		'',
	);
	return preg_replace($_strip_search, $_strip_replace, $content);
}
