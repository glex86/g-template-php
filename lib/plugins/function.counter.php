<?php
/**
 * gTemplate Plugins
 *
 * @package    gTemplate
 * @subpackage Plugins
 */

/**
 * gTemplate {counter} function plugin
 * 
 * Type:     function<br>
 * Name:     counter<br>
 * Purpose:  print out a counter value<br>
 * 
 * @version 1.0
 * @author Tamas David (G-Lex) <glex at mittudomain.info>
 * @link https://github.com/glex86/g-template-php G-Template Engine on Github
 *
 * @internal Some source codes are taken from Smarty
 * @internal author Monte Ohrt <monte at ohrt dot com>
 * @internal link http://smarty.net Smarty
 */
function tpl_function_counter($params, &$gTpl)
{
    static $counters = array();

    $name = (isset($params['name'])) ? $params['name'] : 'default';
    if (!isset($counters[$name])) {
        $counters[$name] = array(
            'start'=>1,
            'skip'=>1,
            'direction'=>'up',
            'count'=>1
            );
    }
    $counter =& $counters[$name];

    if (isset($params['start'])) {
        $counter['start'] = $counter['count'] = (int)$params['start'];
    }

    if (!empty($params['assign'])) {
        $counter['assign'] = $params['assign'];
    }

    if (isset($counter['assign'])) {
        $gTpl->assign($counter['assign'], $counter['count']);
    }
    
    if (isset($params['print'])) {
        $print = (bool)$params['print'];
    } else {
        $print = empty($counter['assign']);
    }

    if ($print) {
        $retval = $counter['count'];
    } else {
        $retval = null;
    }

    if (isset($params['skip'])) {
        $counter['skip'] = $params['skip'];
    }
    
    if (isset($params['direction'])) {
        $counter['direction'] = $params['direction'];
    }

    if ($counter['direction'] == "down")
        $counter['count'] -= $counter['skip'];
    else
        $counter['count'] += $counter['skip'];
    
    return $retval;
    
}

