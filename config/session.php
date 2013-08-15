<?php defined('SYSPATH') OR die('No direct script access.');

return array(
    'native'       => array(
        'name'     => 'kohana_session',
        'lifetime' => 43200,  // 12 hours
    ),
    'database' => array(
        'name'      => 'kohana_session',
        'table'     => 'sessions',
        'lifetime'  => 43200,
        'gc'        => 500,
    ),
);
