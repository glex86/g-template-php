<?php
/**
 * Template Lite config_load template internal module
 *
 * Type:	 template
 * Name:	 config_load
 */

$name = $this->getCompiledName($file, 'conf');



if ($this->debugging)
{
	$debug_start_time = array_sum(explode(' ', microtime()));
}

if ($this->cache)
{
	array_push($this->_cache_info['config'], $file);
}

if (!$this->force_compile && $this->_validate_compiled($this->abs_config_dir.$file, $this->abs_compile_dir.$name))
{
    include($this->abs_compile_dir.$name);
	
} else {
    //Teljes config állomány értelmezése
    $configContent = parse_ini_file($this->abs_config_dir.$file, true);
    $this->storeCompiledFile($name, '<?php $configContent='.var_export($configContent, true).';', $this->abs_compile_dir);    
}

//Kiszűrni a kért szekciót
if (!empty($section_name)) {
    if (!isset($configContent[$section_name])) {
        $configContent[$section_name] = array();
    }
    
    $configContent = array($section_name=>$configContent[$section_name]);
}

//Kiszűrni a kért változót
if (!empty($var_name)) {
    if (!isset($configContent[$var_name])) {
        $configContent[$var_name] = array();
    }
    
    $configContent = array($var_name=>$configContent[$var_name]);
}

$this->_confs = array_merge($this->_confs, $configContent);

if ($this->debugging)
{
	$this->_templatelite_debug_info[] = array('type'	  => 'config',
										'filename'  => $file.' ['.$section_name.'] '.$var_name,
										'depth'	 => 0,
										'exec_time' => array_sum(explode(' ', microtime())) - $debug_start_time );
}

return true;
