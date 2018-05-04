<?php
/**
 * @author  Chris
 */
return [

    'DEBUG' => false,
    'LOG_DIR' => '/data/log/project',
    'LOG_DIR_PREFIX' => 'swoole_log',

    'redis_servers' => '127.0.0.1:6379',
    /*
        |--------------------------------------------------------------------------
        | Redis Connections Infos
        |--------------------------------------------------------------------------
        |
        */
//
//    'redis' => [
//
//        'client' => 'predis',
//
//        'default' =>
//            [
//                'host' => config('env.REDIS_HOST', '127.0.0.1'),
//                'password' => config('env.REDIS_PASSWORD', null),
//                'port' => config('env.REDIS_PORT', 6379),
//                'database' => 0,
//            ],
//
//    ],

    /*
    |--------------------------------------------------------------------------
    | Database Connections
    |--------------------------------------------------------------------------
    |
    */
    'db_servers' => [
                'master' => [
                        'host' => '127.0.0.1',
                        'port' => '3306',
                        'username' => 'root',
                        'password' => 'buguniao.com',
                        'dbname' => 'facebook'
                ],
                'slave' => [
                        [
                                'host' => '127.0.0.1',
                                'port' => '3306',
                                'username' => 'root',
                                'password' => 'buguniao.com',
                                'dbname' => 'facebook'
                        ]
                ]
        ],
        
        'google_analytics_db_servers' => [
                'master' => [
                        'host' => '127.0.0.1',
                        'port' => '3306',
                        'username' => 'root',
                        'password' => 'buguniao.com',
                        'dbname' => 'google_analytics'
                ],
                'slave' => [
                        [
                                'host' => '127.0.0.1',
                                'port' => '3306',
                                'username' => 'root',
                                'password' => 'buguniao.com',
                                'dbname' => 'google_analytics'
                        ]
                ]
        ],
        
        'dw_servers' => [
                'host' => '192.168.105.106',
                'port' => '1521',
                'service_name' => 'dwtest',
                'username' => 'bi',
                'password' => 'cuckoo'
        ]
];