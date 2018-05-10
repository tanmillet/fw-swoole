<?php
/**
 * @author  Chris
 */
return [
    'route_map' => [
        'user' => 'User',
        'home' => 'Home',
        'live' => 'Live',
        'chart' => 'Chart',
        'im' => 'Im',
    ],
    'no_need_check_session' => [
        'User' => [
            'login',
            'logout'
        ],
        'Home' => [
            'index',
        ],
    ],
];