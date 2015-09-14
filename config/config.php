<?php
return new \Phalcon\Config([
    'database' => [
        'adapter' => 'Mysql',
        'host' => 'localhost',
        'username' => '',
        'password' => '',
        'dbname' => 'phasty',
        'charset' => 'utf8',
    ],
    'app' => [
        'cacheDir' => BASE_DIR . '/var/cache/',
        'publicUrl' => 'phasty.tk/',
        'cryptSalt' => 'your_unique_salt', //generate unique for your site!!!!!!!!
        'jwtSecret' => 'Your_unique_secret_key', //generate unique for your site!!!!!!!!
        'jwtAlgorithm' => 'HS256',
        'locale' => 'en_US',
        'currency' => 'EUR',
        'productImagesPath' => DIRECTORY_SEPARATOR . 'img' . DIRECTORY_SEPARATOR . 'products' . DIRECTORY_SEPARATOR,
        'productImagesNumber' => 5,
        'productImagesTypes' => ['image/jpeg', 'image/png', 'image/gif']
    ],
    'mail' => [
        'viewsDir' => BASE_DIR . '/app/Front/views/email/',
        'driver' => 'smtp',
        "host" => "your_mail_provider_url",
        "port" => 2525,
        "username" => "your_mail_service_provider_user_name",
        "password" => "your_mail_service_provider_password",
        //"sendmail" => "/usr/sbin/sendmail -bs",
        'encryption' => 'tls',
        'from' => [
            'email' => 'admin@example.com',
            'name' => 'Phasty e-commerce'
        ]
    ],
    'OAuth' => ['baseUrl' => 'http://' . $_SERVER['SERVER_NAME'] . '/social', "providers" => [
        "OpenId" => ["enabled" => false,
            "Yahoo" => ["enabled" => false],
            "Vkontakte" => ["enabled" => true, "keys" => ["id" => "", "secret" => ""]],
            "Mailru" => ["enabled" => true, "keys" => ["id" => "", "secret" => ""]],
            "Google" => [
                "enabled" => true,
                "keys" => ["id" => "", "secret" => ""],
                "scope" => "email"],
            "Facebook" => [
                "enabled" => true,
                "keys" => ["id" => "", "secret" => ""],
                "scope" => "email,publish_stream",
                "display" => ""],
            "Twitter" => ["enabled" => true, "keys" => ["key" => "", "secret" => ""]],
            "LinkedIn" => ["enabled" => true, "keys" => ["key" => "", "secret" => ""]]
        ]
    ]],

    'models' => ['metadata' => ['adapter' => 'Redis']],
    'session' => ['adapter' => 'Redis']
]);
