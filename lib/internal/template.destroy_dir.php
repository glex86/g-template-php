<?php
/**
 * gTemplate Internal Function
 * Remove subdirectories for caching functions
 *
 * @package    gTemplate
 * @subpackage internalFunctions
 */


function template_destroy_dir($file, $id, $dir, &$gTpl) {
    if ($file == null && $id == null && is_dir($dir)) {
        if ($d = opendir($dir)) {
            while (($f = readdir($d)) !== false) {
                if ($f != '.' && $f != '..') {
                    template_rm_dir($dir . $f . DIRECTORY_SEPARATOR);
                }
            }
        }
    } else {
        if ($id == null) {
            $name = $gTpl->getCompiledName($file);
            @unlink($dir . $name);
        } else {
            $_args = "";
            foreach (explode('|', $id) as $value) {
                $_args .= $value . DIRECTORY_SEPARATOR;
            }
            template_rm_dir($dir . DIRECTORY_SEPARATOR . $_args);
        }
    }
}

function template_rm_dir($dir) {
    if (is_file(substr($dir, 0, -1))) {
        @unlink(substr($dir, 0, -1));
        return;
    }
    if ($d = opendir($dir)) {
        while (($f = readdir($d)) !== false) {
            if ($f != '.' && $f != '..') {
                template_rm_dir($dir . $f . DIRECTORY_SEPARATOR, $gTpl);
            }
        }
        @rmdir($dir . $f);
    }
}

