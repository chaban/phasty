<?php
$actions = ['index', 'show', 'store', 'update', 'destroy'];
return new \Phalcon\Config([
    'defaultAction' => \Phalcon\Acl::DENY,
    'resource' => [
        'index' => ['description' => 'Dashboard', 'actions' => ['index', 'dashboard']],
        'attributes' => ['description' => 'attributes controller', 'actions' => $actions],
        'brands' => ['description' => 'brands controller', 'actions' => $actions],
        'categories' => ['description' => 'categories controller', 'actions' => $actions],
        'currencies' => ['description' => 'currencies controller', 'actions' => $actions],
        'deliveries' => ['description' => 'deliveries controller', 'actions' => $actions],
        'discounts' => ['description' => 'discounts controller', 'actions' => $actions],
        'orders' => ['description' => 'orders controller', 'actions' => $actions],
        'orderStatuses' => ['description' => 'orderStatuses controller', 'actions' => $actions],
        'pages' => ['description' => 'pages controller', 'actions' => $actions],
        'payments' => ['description' => 'payments controller', 'actions' => $actions],
        'productAttributes' => ['description' => 'productAttributes controller', 'actions' => $actions],
        'productImages' => ['description' => 'productImages controller', 'actions' => $actions],
        'products' => ['description' => 'products controller', 'actions' => $actions],
        'reviews' => ['description' => 'products reviews', 'actions' => $actions],
        'users' => ['description' => 'users controller', 'actions' => $actions],
    ],
    'role' => [
        'guest' => [
            'description' => 'not authorized user', 'allow' => [],
        ],
        'customer' => [
            'description' => 'logged in user, favorite shop visitor',
            'inherit' => 'guest',
            'allow' => [],
        ],
        'moderator' => [
            'description' => 'user that have access to moderate content',
            'inherit' => 'customer',
            'allow' => [
                'index' => ['actions' => ['index', 'dashboard']],
                'reviews' => ['actions' => $actions],
                'pages' => ['actions' => $actions],
                'brands' => ['actions' => $actions],
            ],
        ],
        'editor' => [
            'description' => 'user that have access to create and edit content',
            'inherit' => 'moderator',
            'allow' => [
                'attributes' => ['actions' => $actions],
                'categories' => ['actions' => $actions],
                'currencies' => ['actions' => $actions],
                'deliveries' => ['actions' => $actions],
                'discounts' => ['actions' => $actions],
                'orders' => ['actions' => $actions],
                'orderStatuses' => ['actions' => $actions],
                'payments' => ['actions' => $actions],
                'productAttributes' => ['actions' => $actions],
                'productImages' => ['actions' => $actions],
                'products' => ['actions' => $actions],
            ],
        ],
        'admin' => [
            'description' => 'administrator have full access',
            'inherit' => 'editor',
            'allow' => [
                'users' => ['actions' => $actions]
            ],
        ],
    ],
]);