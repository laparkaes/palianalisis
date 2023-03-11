<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/*
| -------------------------------------------------------------------------
| Hooks
| -------------------------------------------------------------------------
| This file lets you define "hooks" to extend CI without hacking the core
| files.  Please see the user guide for info:
|
|	https://codeigniter.com/userguide3/general/hooks.html
|
*/
$hook['pre_controller'] = array(
        'class'    => 'My_hook',
        'function' => 'pre_constructor',
        'filename' => 'My_hook.php',
        'filepath' => 'hooks',
        'params'   => null
);

$hook['post_controller_constructor'] = array(
        'class'    => 'My_hook',
        'function' => 'post_constructor',
        'filename' => 'My_hook.php',
        'filepath' => 'hooks',
        'params'   => null
);