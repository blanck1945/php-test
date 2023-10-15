<?php

namespace Controller\AuthController;

use Core\Controller\ICoreController;
use Core\Database\Database;
use Core\Injectable\Injectable;
use Services\AuthService\AuthService;
use Services\ProductService\ProductService;

class AuthController extends Injectable implements ICoreController
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
                    'ProductService' => [
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
        return [
            '/signup' => [
                'POST' => [
                    'controller' => AuthController::class,
                    'handler' => 'signup'
                ]
            ]
        ];
    }

    public function signup()
    {
        $new_tenant = $this->inject['AuthService']->create_user([
            'tenant_name' => 'test',
            'tenant_email' => 'email@gmail.com'
        ]);

        return [
            "message" => "User created successfully",
            'new_tenant' => $new_tenant
        ];
    }
}
