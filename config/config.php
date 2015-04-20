<?php
return new \Phalcon\Config(array(
    'database' => array(
        'adapter' => 'Mysql',
        'host' => 'localhost',
        'username' => '',
        'password' => '',
        'dbname' => '',
        'charset' => 'utf8',
    ),
    'app' => array(
        'cacheDir' => __DIR__ . '/../../var/cache/',
        'publicUrl' => '',
        'cryptSalt' => '$9dIko$.F#11',
        'locale' => 'en_US',
        'currency' => 'EUR',
    ),
    'mail' => array(
        'fromName' => 'Shop on PhalconPhp',
        'fromEmail' => 'Phasty@example.com',
        'preferTransport' => 'mail', // Swift_mail transport, for smtp type smtp
        'smtp' => array(
            'server' => '127.0.0.1',
            'port' => 587,
            'security' => 'tls',
            'username' => '',
            'password' => '',
        )),
    'social' => array('baseUrl' => 'http://' . $_SERVER['SERVER_NAME'] . '/social', "providers" => array(
        "OpenId" => array("enabled" => false),
        "Yahoo" => array("enabled" => false),
        "Vkontakte" => array("enabled" => true, "keys" => array("id" => "", "secret" => "")),
        "Mailru" => array("enabled" => true, "keys" => array("id" => "", "secret" =>"")),
        "Google" => array(
            "enabled" => true,
            "keys" => array("id" => "", "secret" => ""),
            "scope" => "email"),
        "Facebook" => array(
            "enabled" => true,
            "keys" => array("id" => "", "secret" => ""),
            "scope" => "email,publish_stream",
            "display" => ""),
        "Twitter" => array("enabled" => true, "keys" => array("key" => "", "secret" =>"")),
        "LinkedIn" => array("enabled" => true, "keys" => array("key" => "", "secret" => "")))),

    'models' => array('metadata' => array('adapter' => 'Memory'))));
