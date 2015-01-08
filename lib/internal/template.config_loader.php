<?php
/**
 * gTemplate Internal Function
 * Load / Compile Config files
 *
 * @package    gTemplate
 * @subpackage internalFunctions
 */


$name = $this->getCompiledName($file, 'conf');

if ($this->debugging) {
    $debug_start_time = array_sum(explode(' ', microtime()));
}

if ($this->cache) {
    array_push($this->_cache_info['config'], $file);
}

if (!$this->force_compile && $this->_validate_compiled($this->abs_config_dir.$file, $this->abs_compile_dir.$name)) {
    include($this->abs_compile_dir.$name);

} else {
    //Teljes config állomány értelmezése
    $configContent = parse_ini_file($this->abs_config_dir.$file, true);

    $output = '<?php ' . "\n"
            . '/* ' . self::NAME . ' ' . self::VERSION . ' - Compiled Config' . "\n"
            . ' * - Compiled on ' . strftime("%Y-%m-%d %H:%M:%S") . "\n"
            . ' * - From source template: ' . $file . "\n" . ' */ ' . "\n"
            .'$configContent='.var_export($configContent, true).';';


    $this->storeCompiledFile($name, $output, $this->abs_compile_dir);
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

if ($this->debugging) {
    $this->_debug_info[] = array(  'type'      => 'config',
                                                'filename'  => $file.' ['.$section_name.'] '.$var_name,
                                                'depth'     => 0,
                                                'exec_time' => array_sum(explode(' ', microtime())) - $debug_start_time );
}

return true;
