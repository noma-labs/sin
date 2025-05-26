<?php

declare(strict_types=1);

return [

    /*
    |--------------------------------------------------------------------------
    | Default Filesystem Disk
    |--------------------------------------------------------------------------
    |
    | Here you may specify the default filesystem disk that should be used
    | by the framework. The "local" disk, as well as a variety of cloud
    | based disks are available to your application for file storage.
    |
    */

    'default' => env('FILESYSTEM_DRIVER', 'local'),

    /*
    |--------------------------------------------------------------------------
    | Default Cloud Filesystem Disk
    |--------------------------------------------------------------------------
    |
    | Below you may configure as many filesystem disks as necessary, and you
    | may even configure multiple disks for the same driver. Examples for
    | most supported storage drivers are configured here for reference.
    |
    | Supported drivers: "local", "ftp", "sftp", "s3"
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
            'serve' => false,
            'throw' => true,
            'report' => false,
        ],
        'public' => [
            'driver' => 'local',
            'root' => storage_path('app/public'),
            'url' => env('APP_URL').'/storage',
            'visibility' => 'public',
            'report' => false,
        ],
        'ftp' => [
            'driver' => 'ftp',
            'host' => '',
            'username' => '',
            'password' => '',

            // Optional FTP Settings...
            'port' => 21,
        ],
        's3' => [
            'driver' => 's3',
            'key' => env('AWS_KEY'),
            'secret' => env('AWS_SECRET'),
            'region' => env('AWS_REGION'),
            'bucket' => env('AWS_BUCKET'),
            'report' => false,
        ],
        'scuola' => [
            'driver' => 'local',
            'root' => storage_path('app/elaborati'),
        ],
        'photos' => [
            'driver' => 'local',
            'root' => storage_path('app/foto'),
        ],
        'media_originals' => [
            'driver' => 'local',
            'root' => storage_path('app/media/originals'),
            'visibility' => 'private',
        ],

        'media_previews' => [
            'driver' => 'local',
            'root' => storage_path('app/media/previews'),
            'visibility' => 'private',
        ],
    ],

    'links' => [
        public_path('storage') => storage_path('app/public'),
    ],

];
