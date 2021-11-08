<?php

function autoloadAllFiles($class)
{
    if (preg_match('/Controller$/', $class)) {
        $type = 'C';
    }

    if (preg_match('/Model$/', $class)) {
        $type = 'M';
    }

    switch ($type){
        case 'C':  require_once ('../' . DIRECTORY_CONTROLLERS . $class . ".php"); break;
        case 'M':  require_once ('../' . DIRECTORY_MODELS . $class . ".php"); break;

        default: throw new Exception('Class not found');
    }
}

spl_autoload_register('autoloadAllFiles');

