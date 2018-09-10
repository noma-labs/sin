<?php

return array(


    'pdf' => array(
        'enabled' => true,
        // 'binary'  => '/usr/local/bin/wkhtmltopdf',
        // 'binary' => base_path("vendor\h4cc\wkhtmltopdf-amd64\bin\wkhtmltopdf-amd64"),
        // https://github.com/barryvdh/laravel-snappy/issues/60
        // https://answers.microsoft.com/en-us/windows/forum/windows_10-performance-winpc/the-program-cant-start-because-msvcp120dll-is/5f4016ca-2c7c-49de-bf64-40a06ab004d6
        'binary' => base_path('"vendor\wemersonjanuario\wkhtmltopdf-windows\bin\64bit\wkhtmltopdf"'),
        'timeout' => false,
        'options' => array(),
        'env'     => array(),
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
