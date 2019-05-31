<?php

return array(


    'pdf' => array(
        'enabled' => true,
        // 'binary'  => '/usr/local/bin/wkhtmltopdf',
        // 'binary' => base_path("vendor\h4cc\wkhtmltopdf-amd64\bin\wkhtmltopdf-amd64"),
        // https://github.com/barryvdh/laravel-snappy/issues/60
        'binary' => base_path('"vendor\wemersonjanuario\wkhtmltopdf-windows\bin\64bit\wkhtmltopdf"'),
        'timeout' => false,
        'options' => array(),
        'env'     => array(),
        // 'print-media-type' => true,
    ),
    'image' => array(
        'enabled' => true,
        // 'binary'  => '/usr/local/bin/wkhtmltoimage',
        'binary' => base_path("vendor\h4cc\wkhtmltoimage-amd64\bin\wkhtmltoimage-amd64"),
        'timeout' => false,
        'options' => array(),
        'env'     => array(),
    ),


);
