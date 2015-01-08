<?php
/**
 * gTemplate Internal Function
 * Display an error message
 *
 * @package    gTemplate
 * @subpackage internalFunctions
 */


function debug_beauty_errors($gTpl, $error_msg, $error_type, $file, $line) {
    ?>
    <style type="text/css">
        .gTpl { margin: 10px 20px 0 20px; }
        .gTpl h1, .gTpl h2, .gTpl h3, .gTpl h4 { margin-bottom: 0; }
        .gTpl h1 { color: #cc0000; border-bottom: 2px solid #ee0000; }
        .gTpl p { margin-top: 5px; margin-bottom: 30px; }
        .errormsg { font-size: 1.1em; }
        .filedef { font-size: 0.9em;color: #444; font-style: italic; }
        .errorbox p { margin-bottom: 0; }
        .errorbox h3 { margin-top: 0; }
        .errorbox { background-color: #ffefef; padding: 15px; }
        .subtitle { font-size: 0.5em; font-style: italic; color: #555; padding-left: 20px; }
        .backtrace { font-family: monospace; background-color: #f8f8f8; }
    </style>

    <?php

    preg_match('!^\[([A-Z]*)\] (.*?)$!is', $error_msg, $error);
    $nature = $error[1];
    $error_msg = $error[2] ? $error[2] : $error_msg;
    $error_msg = htmlspecialchars($error_msg);
    $error_msg = preg_replace("/ in '([^']*)' tag/is", ' in <b>'.htmlspecialchars($gTpl->left_delimiter).'$1'.htmlspecialchars($gTpl->right_delimiter).'</b> tag', $error_msg);

    echo '<div class="gTpl">';
    echo '<h1>Unexpected catastrophic error occured <span class="subtitle">I\'m sorry, but a few bits staying in the wrong place</span></h1>';
    echo '<div class="errorbox">';
    if ($error[1]) {
        echo '<h3>' . $nature . ' ERROR</h3>';
    }
    echo '<p class="errormsg">'. $error_msg;

    if ($file) {
        echo '<br><br><span class="filedef">The error occured in <b>'.$file.'</b>';

        if ($line) {
            echo ' on line <b>'.$line.'</b>';
        }

        echo '</span>';
    }
    echo '</p></div>';


    $directories = array(
        'Templates directory' => $gTpl->getTemplateDir(true),
        'Compile directory' => $gTpl->getCompileDir(true),
        'Config directory' => $gTpl->getConfigDir(true),
        'Cache directory' => $gTpl->getCacheDir(true),
    );

    $c = 0;
    foreach ($gTpl->plugins_dir as $dir) {
        $directories['Plugin directory[' . ( ++$c) . ']'] = substr($dir, 0, 1) == '/' ? $dir : G_TEMPLATE_BASE . $dir;
    }

    echo '<h3>Current searching paths</h3>';
    echo '<p>';
    foreach ($directories as $dirTitle => $dirPath) {
        echo '<b>' . $dirTitle . ':</b> ' . $dirPath;


        if (file_exists($dirPath)) {
            echo ' (<i>real path: ' . realpath($dirPath) . '</i>)';
        } else {
            echo '<span style="color: red;"> Not exists</span>';
        }

        echo '<br>';
    }
    echo '</p>';


    echo '<h3>Debug backtrace</h3>';
    $output = '<p class="backtrace">';
    $backtrace = debug_backtrace();
    foreach ($backtrace as $bt) {
        $args = '';
        foreach ($bt['args'] as $a) {
            if (!empty($args)) {
                $args .= ', ';
            }
            switch (gettype($a)) {
                case 'integer':
                case 'double':
                    $args .= '<span style="color: magenta;">' . $a . '</span>';
                    break;
                case 'string':
                    $a = htmlspecialchars(substr($a, 0, 64)) . ((strlen($a) > 64) ? '...' : '');
                    $args .= "<span style=\"color: green;\">\"$a\"</span>";
                    break;
                case 'array':
                    $args .= '<span style="color: blue;">Array</span>(' . count($a) . ')';
                    break;
                case 'object':
                    $args .= '<span style="color: blue;">Object</span>(' . get_class($a) . ')';
                    break;
                case 'resource':
                    $args .= '<span style="color: blue;">Resource</span>(' . strstr($a, '#') . ')';
                    break;
                case 'boolean':
                    $args .= $a ? '<span style="color: blue;">True</span>' : '<span style="color: blue;">False</span>';
                    break;
                case 'NULL':
                    $args .= '<span style="color: blue;">Null</span>';
                    break;
                default:
                    $args .= 'Unknown';
            }
        }

        $output .= "<span style=\"font-size: 1.1em;\"><b>Call</b> {$bt['class']}{$bt['type']}<b>{$bt['function']}</b>($args);</span><br />\n";
        $output .= "<i><b>from </b> {$bt['file']} at line {$bt['line']}</i><br><br />\n";
    }
    $output .= "</p>\n";

    echo $output;

    echo '<h3>Original error message</h3>';
}
