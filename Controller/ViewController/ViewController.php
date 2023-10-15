<?php

namespace Controller\ViewController;

use Core\Controller\ICoreController;
use Core\Database\Database;
use Core\Injectable\Injectable;
use Core\Router\AppRouter;
use Services\AuthService\AuthService;

class Controller
{
    static public $inject = [];
    public function __construct(...$classes)
    {
        // foreach ($classes as $class) {
        //     $replace_injectable = str_replace('\\', '/', $class['class']);
        //     require_once __DIR__ . "/../../" . $replace_injectable . ".php";
        //     $split = explode('\\', $class['class']);
        //     $end = end($split);
        //     self::$inject =
        //         array_merge(
        //             self::$inject,
        //             [
        //                 $end => new $class['class'](...$class['arguments'] ?? [])
        //             ]
        //         );
        // }
    }
}

class ViewController extends Injectable implements ICoreController
{

    static public function config()
    {
        return [
            'metadata' => false,
        ];
    }

    static public function inject()
    {
        return [
            'AuthService' => [
                'class' => AuthService::class,
                'arguments' => [
                    'Database' => [
                        'class' => Database::class,
                        'arguments' => [
                            'db_type' => 'postgres'
                        ]
                    ]
                ]
            ],
        ];
    }

    static public function routes()
    {
        return  [
            '/' => ['GET' => [
                'controller' => ViewController::class,
                'handler' => 'get_home'
            ]],
            '/about' => [
                'GET' => [
                    'controller' => ViewController::class,
                    'handler' => 'about'
                ]
            ]
        ];
    }

    public function get_home()
    {
        // var_dump(self::$inject['AuthService']->create_user([
        //     'email' => 'email@gmail.com',
        //     'password' => 'Pastrana1'
        // ]));

        return [
            'view' => 'index.php',
            'variables' => [
                'hello' => 'Hello World',
                'framework' => 'Phantom'
            ]
        ];
    }

    public function about()
    {
        return [
            'view' => 'product.php',
            'variables' => [
                'hello' => 'Hello World',
                'framework' => 'Phantom',
                'id' => 1,
                'name' => 'Product 1',
            ]
        ];
    }
}
