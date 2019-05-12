<?php
    return [
        'default' => 'mysql',
        'migrations' =>'migrations',

         'connections'=>[

          'mysql' => [
              'driver' =>'mysql',
              'host' => env('DB_HOST' ,'localhost'),
              'port' => env('DB_PORT' ,3306),
              'database' => env('DB_NAME' ,'fundbuz'),
              'username' =>env('DB_USERNAME', 'root'),
              'password' => env('DB_PASSWORD' ,''),
              'charset'   => 'utf8',
              'collation' => 'utf8_unicode_ci',
              'strict' =>false,
              'option' =>[]
          ],

          'mongodb' => [
              'driver'   => 'mongodb',
              'host'     => '127.0.0.1',
              'port'     =>  27017,
              'database' => 'Hamnamad',
              'username' => '',
              'password' => ''
          ]

      ]



    ];

