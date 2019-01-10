<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Default Filesystem Disk
    |--------------------------------------------------------------------------
    |
    | Here you may specify the default filesystem disk that should be used
    | by the framework. The "local" disk, as well as a variety of cloud
    | based disks are available to your application. Just store away!
    |
    */

    'default' => env('FILESYSTEM_DRIVER', 'local'),

    /*
    |--------------------------------------------------------------------------
    | Default Cloud Filesystem Disk
    |--------------------------------------------------------------------------
    |
    | Many applications store files both locally and in the cloud. For this
    | reason, you may specify a default "cloud" driver here. This driver
    | will be bound as the Cloud disk implementation in the container.
    |
    */

    'cloud' => env('FILESYSTEM_CLOUD', 's3'),

    /*
    |--------------------------------------------------------------------------
    | Filesystem Disks
    |--------------------------------------------------------------------------
    |
    | Here you may configure as many filesystem "disks" as you wish, and you
    | may even configure multiple disks of the same driver. Defaults have
    | been setup for each driver as an example of the required options.
    |
    | Supported Drivers: "local", "ftp", "s3", "rackspace"
    |
    */

    'disks' => [

        'local' => [
            'driver' => 'local',
            'root' => storage_path('app'),
        ],
        'qiniu' => [
            'driver'  => 'qiniu',
            'domains' => [
                'default'   => 'qiniu.geekyiqi.com', //你的七牛域名
                'https'     => 'qiniu.geekyiqi.com',         //你的HTTPS域名
                'custom'    => 'qiniu.geekyiqi.com',     //你的自定义域名
             ],
            'access_key'=> '1FsD0UN5u5zofw3SXErL7sWA3yEJnaZUAuOqMnKE',  //AccessKey
            'secret_key'=> 'ePNwrAuaH9wKzAPrr8MoF41Epoj4Z26e4iSBP8c6',  //SecretKey
            'bucket'    => 'geekyiqi',  //Bucket名字
            'notify_url'=> '',  //持久化处理回调地址
        ],
        'public' => [
            'driver' => 'local',
            'root' => storage_path('app/public'),
            'url' => env('APP_URL').'/storage',
            'visibility' => 'public',
        ],
        //新建一个本地端的uploads空间(目录)  用于储存上传的文件
        'uploads' => [
            'driver' => 'local',
            //文件将上传到public/uploads目录 如果需要浏览器直接访问 请设置成这个
            'root' => public_path('uploads'),
        ],
        's3' => [
            'driver' => 's3',
            'key' => env('AWS_KEY'),
            'secret' => env('AWS_SECRET'),
            'region' => env('AWS_REGION'),
            'bucket' => env('AWS_BUCKET'),
        ],

    ],

];
