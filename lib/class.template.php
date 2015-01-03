<?php

/*
 * Project:	template_lite, a smarter template engine
 * File:	class.template.php
 * Author:	Paul Lockaby <paul@paullockaby.com>, Mark Dickenson <akapanamajack@sourceforge.net>
 * Copyright:	2003,2004,2005 by Paul Lockaby, 2005,2006 Mark Dickenson
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
 * The latest version of template_lite can be obtained from:
 * http://templatelite.sourceforge.net
 *
 */

if (!defined('G_TEMPLATE_BASE')) {
    define('G_TEMPLATE_BASE', __DIR__ . DIRECTORY_SEPARATOR);
}

class gTemplate {
    const DS                            = DIRECTORY_SEPARATOR;
    const NAME                          = 'G-Template Engine';
    const VERSION                       = '0.8';
    const CREATED                       = '2014-12-22';        

    // public configuration variables
    var $left_delimiter              = '<{';                 // the left delimiter for template tags
    var $right_delimiter             = '}>';                 // the right delimiter for template tags
    var $cache                       = false;                // whether or not to allow caching of files
    var $force_compile               = false;                // force a compile regardless of saved state
    var $plugin_prefix               = array('tpl');         // prefix for plugins/filters, without underscore
    
    var $plugins_dir                 = array('plugins');     // where the plugins are to be found
    var $template_dir                = 'templates';          // where the templates are to be found
    var $compile_dir                 = 'templates_c';        // the directory to store the compiled files in
    var $config_dir                  = 'templates_config';   // where the config files are
    var $cache_dir                   = 'templates_cache';    // where cache files are stored
    var $abs_template_dir           = '';
    var $abs_compile_dir            = '';
    var $abs_config_dir             = '';
    var $abs_cache_dir              = '';


    var $config_overwrite            = false;
    var $config_booleanize           = true;
    var $config_fix_new_lines        = true;
    var $config_read_hidden          = true;
    var $cache_lifetime              = 0;  // how long the file in cache should be considered "fresh"
    var $encode_file_name            = false; // Set this to false if you do not want the name of the compiled/cached file to be md5 encoded.
    var $reserved_template_varname   = 'smarty';
    var $default_modifiers           = array();
    var $debugging                   = false;
    
    // gzip output configuration
    var $send_now                       = 1;
    var $force_compression              = 0;
    var $compression_level              = 9;
    var $enable_gzip                    = 1;
    
    // private internal variables
    var $_vars                          = array(); // stores all internal assigned variables
    var $_confs                         = array(); // stores all internal config variables
    var $_plugins                       = array(
                                                'modifier'      => array(),
                                                'function'      => array(),
                                                'block'         => array(),
                                                'compiler'      => array(),
                                                'prefilter'     => array(),
                                                'postfilter'    => array(),
                                                'outputfilter'  => array()
                                            );
    
    var $_linenum                       = 0;  // the current line number in the file we are processing
    var $_file                          = '';  // the current file we are processing
    var $_config_obj                    = null;
    var $_compile_obj                   = null;
    var $_cache_id                      = null;
    var $_cache_dir                     = '';  // stores where this specific file is going to be cached
    var $_cache_info                    = array('config' => array(), 'template' => array());
    var $_config_module_loaded          = false;
    var $_templatelite_debug_info       = array();
    var $_templatelite_debug_loop       = false;
    var $_templatelite_debug_dir        = '';
    var $_inclusion_depth               = 0;
    var $_null                          = null;
    
    var $_resource_time                 = 0;
    var $_resource_file                 = '';
    var $_resource_valid                = false;
    
    var $_sections                      = array();
    var $_foreach                       = array();

    function __construct() {
        $this->setCompileDir($this->compile_dir);
        $this->setTemplateDir($this->template_dir);
        $this->setConfigDir($this->config_dir);
    }

    
    /***************************************************************************
     * DATA HANDLING FUNCTIONS
     */
    function assign($key, $value = null) {
        if (is_array($key)) {
            foreach ($key as $var => $val)
                if ($var != '') {
                    $this->_vars[$var] = $val;
                }
        } else {
            if ($key != '') {
                $this->_vars[$key] = $value;
            }
        }
    }

    function assignByRef($key, $value = null) {
        if ($key != '') {
            $this->_vars[$key] = &$value;
        }
    }

    function clearAssign($key = null) {
        if ($key == null) {
            $this->_vars = array();
        } else {
            if (is_array($key)) {
                foreach ($key as $var) {
                    unset($this->_vars[$var]);
                }
            } else {
                unset($this->_vars[$key]);
            }
        }
    }

    function clearConfig($key = null) {
        if ($key == null) {
            $this->_conf = array();
        } else {
            if (is_array($key)) {
                foreach ($key as $var) {
                    unset($this->_conf[$var]);
                }
            } else {
                unset($this->_conf[$key]);
            }
        }
    }

    function &getTemplateVars($key = null) {
        if ($key == null) {
            return $this->_vars;
        } else {
            if (isset($this->_vars[$key])) {
                return $this->_vars[$key];
            } else {
                return $this->_null;
            }
        }
    }

    function &getConfigVars($key = null) {
        if ($key == null) {
            return $this->_confs;
        } else {
            if (isset($this->_confs[$key])) {
                return $this->_confs[$key];
            } else {
                return $this->_null;
            }
        }
    }
    
    
    /***************************************************************************
     * PLUGIN HANDLING FUNCTIONS
     */    
    function register_modifier($modifier, $implementation) {
        $this->_plugins['modifier'][$modifier] = $implementation;
    }

    function unregister_modifier($modifier) {
        unset($this->_plugins['modifier'][$modifier]);
    }

    function register_function($function, $implementation) {
        $this->_plugins['function'][$function] = $implementation;
    }

    function unregister_function($function) {
        unset($this->_plugins['function'][$function]);
    }

    function register_block($function, $implementation) {
        $this->_plugins['block'][$function] = $implementation;
    }

    function unregister_block($function) {
        unset($this->_plugins['block'][$function]);
    }

    function register_compiler($function, $implementation) {
        $this->_plugins['compiler'][$function] = $implementation;
    }

    function unregister_compiler($function) {
        unset($this->_plugins['compiler'][$function]);
    }

    function register_prefilter($function) {
        $this->_plugins['prefilter'][$_name] = $function;
    }

    function unregister_prefilter($function) {
        unset($this->_plugins['prefilter'][$function]);
    }

    function register_postfilter($function) {
        $this->_plugins['postfilter'][$_name] = $function;
    }

    function unregister_postfilter($function) {
        unset($this->_plugins['postfilter'][$function]);
    }

    function register_outputfilter($function) {
        $this->_plugins['outputfilter'][$_name] = $function;
    }

    function unregister_outputfilter($function) {
        unset($this->_plugins['outputfilter'][$function]);
    }    
    
    
    function _get_plugin_filepath($type, $plugin_name) {
        $plugin_name = $type . '.' . $plugin_name . '.php';
        return $this->_get_plugin_dir($plugin_name) . $plugin_name;
    }

    function _get_plugin_dir($plugin_name) {
        static $_path_array = null;

        if (!isset($_path_array)) {
            $_path_array = explode(PATH_SEPARATOR, get_include_path());
        }

        $plugin_dir_path = '';
        $_plugin_dir_list = is_array($this->plugins_dir) ? $this->plugins_dir : (array) $this->plugins_dir;
        foreach ($_plugin_dir_list as $_plugin_dir) {
            $_plugin_dir = $this->_get_dir($_plugin_dir);

            if (!preg_match('/^([\/\\\\]|[a-zA-Z]:[\/\\\\])/', $_plugin_dir)) {
                // path is relative
                if (file_exists(dirname(__FILE__) . DIRECTORY_SEPARATOR . $_plugin_dir . $plugin_name)) {
                    $plugin_dir_path = dirname(__FILE__) . DIRECTORY_SEPARATOR . $_plugin_dir;
                    break;
                }
            } else {
                if (!in_array($_plugin_dir, $_path_array)) {
                    array_push($_path_array, $_plugin_dir);
                }

                foreach ($_path_array as $_include_path) {
                    if (file_exists($_include_path . $plugin_name)) {
                        $plugin_dir_path = $_include_path;
                        break 2;
                    }
                }
            }
        }
        return $plugin_dir_path;
    }
    
    function loadFilter($type, $name) {
        switch ($type) {
            case 'output':
                include_once($this->_get_plugin_filepath('outputfilter', $name));
                $this->_plugins['outputfilter'][$name] = 'template_' . $type . 'filter_' . $name;
                break;
            case 'pre':
            case 'post':
                if (!isset($this->_plugins[$type . 'filter'][$name])) {
                    $this->_plugins[$type . 'filter'][$name] = 'template_' . $type . 'filter_' . $name;
                }
                break;
        }
    }


    /***************************************************************************
     * RESOURCE HANDLING FUNCTIONS
     */     
    function clear_compiled_tpl($file = null) {
        $this->_destroy_dir($file, null, $this->abs_compile_dir);
    }

    function clear_cache($file = null, $cache_id = null, $compile_id = null, $exp_time = null) {
        if (!$this->cache) {
            return;
        }
        $this->_destroy_dir($file, $cache_id, $this->abs_cache_dir);
    }

    function clear_all_cache($exp_time = null) {
        $this->clear_cache();
    }

    function is_cached($file, $cache_id = null) {
        if (!$this->force_compile && $this->cache && $this->_is_cached($file, $cache_id)) {
            return true;
        } else {
            return false;
        }
    }




    function template_exists($file) {
    }

    function _get_resource($file) {
        if (!file_exists($this->abs_template_dir.$file)) {     
            $this->trigger_error('file "'.$file.'" does not exist', E_USER_ERROR);
        }
        
        
        $this->_resource_time = 0;
        $this->_resource_file = $this->abs_template_dir.$file;
        $this->_resource_valid = opcache_is_script_cached($this->_resource_file);

        if ($this->_resource_valid) 
            return $file;

        $this->_resource_time = filemtime($this->abs_template_dir.$file);
        return $file;
    }
    
    function _validate_compiled($resource, $compiled) {
        $comp_stored = opcache_is_script_cached($compiled);
        $res_stored = $this->_resource_file == $resource ? $this->_resource_valid : opcache_is_script_cached($resource);
        
        if ($res_stored && $comp_stored) {
            return true;
        }
        
        if (!file_exists($compiled)) {
            return false;
        }        
        
        $result = ($this->_resource_time ? $this->_resource_time : filemtime($resource)) < filemtime($compiled);;
        
        if ($result) {
            opcache_compile_file($resource);
            
            //include the compile file to fill up the opcode cache
            require_once $compiled;
        }
        
        return $result;
    }
    

    
    
    
    
    
    
    
    
    
    
    
    
    function display($file, $cache_id = null) {
        $this->fetch($file, $cache_id, true);
    }

    function fetch($file, $cache_id = null, $display = false) {
        $file = $this->_get_resource($file);        
        
        $this->_cache_id = $cache_id;


        $name = $this->getCompiledName($file);


        if (!$this->force_compile && $this->cache && $this->_is_cached($file, $cache_id)) {
            ob_start();
            include($this->_cache_dir . $name);
            $output = ob_get_contents();
            ob_end_clean();
            $output = substr($output, strpos($output, "\n") + 1);
            
        } else {
            $output = $this->_fetch_compile($file, $cache_id);

            if ($this->cache) {  
                $this->storeCompiledFile($name, serialize($this->_cache_info) . "\n".$output, $this->abs_cache_dir);
            }
        }

        if ($display) {
            echo $output;
        } else {
            return $output;
        }
    }
    
    
    function config_load($file, $section_name = null, $var_name = null, $overwrite = true) {
        require_once(G_TEMPLATE_BASE . 'internal/template.config_loader.php');
    }

    function _is_cached($file, $cache_id) {
        $file = $this->_get_resource($file);
        $name = $this->getCompiledName($file);


        if (file_exists($this->_cache_dir . $name) && (((time() - filemtime($this->_cache_dir . $name)) < $this->cache_lifetime) || $this->cache_lifetime == -1) && (filemtime($this->_cache_dir . $name) > $this->_resource_time)) {
            $fh = fopen($this->_cache_dir . $name, 'r');
            if (!feof($fh) && ($line = fgets($fh, filesize($this->_cache_dir . $name)))) {
                $includes = unserialize($line);
                if (isset($includes['template'])) {
                    foreach ($includes['template'] as $value) {
                        if (!(file_exists($this->template_dir . $value) && (filemtime($this->_cache_dir . $name) > filemtime($this->template_dir . $value)))) {
                            return false;
                        }
                    }
                }
                if (isset($includes['config'])) {
                    foreach ($includes['config'] as $value) {
                        if (!(file_exists($this->config_dir . $value) && (filemtime($this->_cache_dir . $name) > filemtime($this->config_dir . $value)))) {
                            return false;
                        }
                    }
                }
            }
            fclose($fh);
        } else {
            return false;
        }
        return true;
    }

    function _fetch_compile_include($_templatelite_include_file, $_templatelite_include_vars) {
        if (!function_exists('template_fetch_compile_include')) {
            require_once(G_TEMPLATE_BASE . 'internal/template.fetch_compile_include.php');
        }
        return template_fetch_compile_include($_templatelite_include_file, $_templatelite_include_vars, $this);
    }

    function _fetch_compile($file) {       
        $compiled_file = $this->getCompiledName($file);

        if ($this->cache) {
            array_push($this->_cache_info['template'], $file);
        }
        

        if (!$this->force_compile && $this->_validate_compiled($this->_resource_file, $this->abs_compile_dir.$compiled_file)) {
            $functionHashName = 'gTemplate_TemplateContent_'.sha1($this->abs_template_dir.$file);            
            require_once $this->abs_compile_dir.$compiled_file;
            
            ob_start();
            $result = $functionHashName( $this );
            $output = ob_get_contents();
            ob_end_clean();

            //Do not return if one of the inline subtemplates are invalidated
            if ($result !== 'Need to recompile') {
                return $output;
            }
        }
        
        $file_contents = file_get_contents($this->abs_template_dir.$file);          
        $prev_file_name = $this->_file;
        $this->_file = $file;

        if (!is_object($this->_compile_obj)) {
            require_once G_TEMPLATE_BASE.'class.compiler.php';
            $this->_compile_obj = new gTemplateCompiler();
        }
        $this->_compile_obj->left_delimiter = $this->left_delimiter;
        $this->_compile_obj->right_delimiter = $this->right_delimiter;
        $this->_compile_obj->plugin_prefix = &$this->plugin_prefix;
        $this->_compile_obj->plugins_dir = &$this->plugins_dir;
        $this->_compile_obj->compile_dir = &$this->compile_dir;
        $this->_compile_obj->abs_compile_dir = &$this->abs_compile_dir;
        $this->_compile_obj->template_dir = &$this->template_dir;
        $this->_compile_obj->abs_template_dir = &$this->abs_template_dir;
        $this->_compile_obj->_vars = &$this->_vars;
        $this->_compile_obj->_confs = &$this->_confs;
        $this->_compile_obj->_plugins = &$this->_plugins;
        $this->_compile_obj->_linenum = &$this->_linenum;
        $this->_compile_obj->_file = &$this->_file;
        $this->_compile_obj->reserved_template_varname = &$this->reserved_template_varname;
        $this->_compile_obj->default_modifiers = $this->default_modifiers;

        $output = $this->_compile_obj->_compile_file($this->abs_template_dir.$file, $file_contents);

        $this->storeCompiledFile($compiled_file, $output, $this->abs_compile_dir);
       
        /* Executing compiled template */
        require_once $this->abs_compile_dir.$compiled_file;
        $functionHashName = 'gTemplate_TemplateContent_'.sha1($this->abs_template_dir.$file);

        ob_start();
        $functionHashName( $this );
        $output = ob_get_contents();
        ob_end_clean();
        
        
        $this->_file = $prev_file_name;
        return $output;
    }

    function _run_modifier() {
        $arguments = func_get_args();
        list($variable, $modifier, $php_function, $_map_array) = array_splice($arguments, 0, 4);
        array_unshift($arguments, $variable);
        if ($_map_array && is_array($variable)) {
            foreach ($variable as $key => $value) {
                if ($php_function == 'PHP') {
                    $variable[$key] = call_user_func_array($modifier, $arguments);
                } else {
                    $variable[$key] = call_user_func_array($this->_plugins['modifier'][$modifier], $arguments);
                }
            }
        } else {
            if ($php_function == 'PHP') {
                $variable = call_user_func_array($modifier, $arguments);
            } else {
                $variable = call_user_func_array($this->_plugins['modifier'][$modifier], $arguments);
            }
        }
        return $variable;
    }

    function _get_dir($dir, $id = null) {
        if (empty($dir)) {
            $dir = '.';
        }
        if (substr($dir, -1) != DIRECTORY_SEPARATOR) {
            $dir .= DIRECTORY_SEPARATOR;
        }
        if (!empty($id)) {
            $_args = explode('|', $id);
            if (count($_args) == 1 && empty($_args[0])) {
                return $dir;
            }
            foreach ($_args as $value) {
                $dir .= $value . DIRECTORY_SEPARATOR;
            }
        }
        return $dir;
    }





    function trigger_error($error_msg, $error_type = E_USER_ERROR, $file = null, $line = null) {
        if (isset($file) && isset($line)) {
            $info = ' (' . basename($file) . ', line '.$line.')';
        } else {
            $info = null;
        }
        trigger_error('gTPL: [in ' . $this->_file . ' line ' . $this->_linenum . ']: syntax error: '.$error_msg.$info, $error_type);
    }

    
    /***************************************************************************
     * TEMPLATE DIRECTORY AND FILE HANDLING FUNCTIONS
     */    
    function setCompileDir($compile_dir) 
    {
        $this->compile_dir = rtrim($compile_dir, '/\\').self::DS;
        $this->abs_compile_dir = realpath($this->compile_dir).self::DS;
        
        return $this;
    }
    function getCompileDir($absolute = true) {
        return $absolute ? $this->abs_compile_dir : $this->compile_dir;
    }
    
    
    function setTemplateDir($template_dir)
    {
        $this->template_dir = rtrim($template_dir, '/\\').self::DS;
        $this->abs_template_dir = realpath($this->template_dir).self::DS;
        
        return $this;
    }    
    function getTemplateDir($absolute = true) {
        return $absolute ? $this->abs_template_dir : $this->template_dir;
    }
    
    
    function setConfigDir($config_dir)
    {
        $this->config_dir = rtrim($config_dir, '/\\').self::DS;
        $this->abs_config_dir = realpath($this->config_dir).self::DS;
        
        return $this;
    }    
    function getConfigDir($absolute = true) {
        return $absolute ? $this->abs_config_dir : $this->config_dir;
    }

    function getCompiledName($file, $type = '') 
    {
        $name = '';
        switch ($type) {
            case 'conf':
                $name = 'conf.'.md5($this->config_dir).'.';
                break;
            
            case 'temp':
                $name = 'temp.'.uniqid().'.'.microtime(true).'.';
                break;
            
            default:
                $name = 'tpl.'.md5($this->template_dir).'.';
                break;
        }        
        
        $name .= str_replace(array(':', '/', '\\'), '-', $file).'.php';
        
        return $name;
    }

    function getCompiledPath($file) {
        $name = $this->getCompiledName($file);
        return $this->abs_compile_dir.$name;
    }
    
    private function storeCompiledFile($file, $output, $dir) {
        $tempFile = $dir.$this->getCompiledName($file, 'temp');
        $f = fopen($tempFile, 'w');
        fwrite($f, $output);
        fclose($f);
        rename($tempFile, $dir.$file);   
        
        //Invalidate the cached file
        opcache_invalidate($dir.$file, true);
    }    

    function buildDir($dir, $id) {
        if (!function_exists('template_build_dir')) {
            require_once(G_TEMPLATE_BASE . 'internal/template.build_dir.php');
        }
        return template_build_dir($dir, $id, $this);
    }
    
    function destroyDir($file, $id, $dir) {
        if (!function_exists('template_destroy_dir')) {
            require_once(G_TEMPLATE_BASE . 'internal/template.destroy_dir.php');
        }
        return template_destroy_dir($file, $id, $dir, $this);
    }    
    
}
