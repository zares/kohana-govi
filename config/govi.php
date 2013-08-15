<?php defined('SYSPATH') OR die('No direct access allowed.');

return array(
    'default' => array(
        'driver'    => 'mysql',
        'host'      => 'localhost',
        'database'  => 'kohana_skeletonapp',
        'username'  => 'root',
        'password'  => '',
        'charset'   => 'utf8',
        'collation' => 'utf8_general_ci',
        'prefix'    => ''
    ),
    'testing' => array(
        'driver'    => 'sqlite',
        'database'  => APPPATH.'data/testing.sqlite',
        'prefix'    => '',
    ),
);
