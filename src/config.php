<?php

return [

    /*
    |--------------------------------------------------------------------------
    | 区域配置
    |--------------------------------------------------------------------------
    |
    | 不同区域的域名、密钥可能不同，根据需要增加区域配置
    |
    */

    'area' => [
        'default' => [
            'domain' => env('TCECLOUD_DOMAIN', 'tcecqpoc.fsphere.cn'),
            'secretId' => env('TCECLOUD_SECRET_ID', ''),
            'secretKey' => env('TCECLOUD_SECRET_KEY', ''),
        ],

        /*
        'shenzhen' => [
            'domain' => env('TCECLOUD_DOMAIN', 'tcecqpoc.fsphere.cn'),
            'secretId' => env('TCECLOUD_SECRET_ID', ''),
            'secretKey' => env('TCECLOUD_SECRET_KEY', ''),
        ],
        */

    ],
];
