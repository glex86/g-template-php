<?php
/**
 * gTemplate Engine
 * https://github.com/glex86/g-template-php
 */

function tpl_modifier_truncate($string, $length = 80, $etc = '...', $break_words = false)
{
    if ($length == 0)
    {
        return '';
    }

    if (strlen($string) > $length)
    {
        $length -= strlen($etc);
        if (!$break_words)
        {
            $string = preg_replace('/\s+?(\S+)?$/u', '', substr($string, 0, $length+1));
        }
        return substr($string, 0, $length).$etc;
    }
    else
    {
        return $string;
    }
}
