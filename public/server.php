<?php

declare(strict_types=1);

use Controller\AuthController\AuthController;
use Controller\CsvController\CsvController;
use Controller\ViewController\ViewController;
use Core\Phantom;

function server()
{
    $app = new Phantom();

    $app->cors();

    $app->set_configuration();

    $app->set_metadata(
        css: [
            'styles.css'
        ],
    );

    $app->add_middlewares();

    $app->add_guards();

    $app->add_interceptor();

    $app->boost(
        ViewController::class,
        AuthController::class,
        CsvController::class
    );

    $app->serve();
}
