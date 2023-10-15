<?php

namespace Core;

use Core\Database\Database;
use Core\Router\AppRouter;
use Core\Request\Request;

class Phantom
{
    public $request;
    public $router;
    public $middlewares;
    public $metadata;
    public $configuration;
    public $guards;
    public $interceptors;
    public $method;
    public $path;

    public function __construct(private array $config = [])
    {
        $this->configuration = $config['configuration'] ?? [];
        $this->metadata = $config['metadata'] ?? [];
        $this->request = new Request();
        $this->method = $_SERVER['REQUEST_METHOD'];
        $this->path = $_SERVER['REQUEST_URI'];
    }

    public function set_configuration($error_page = [], $port = 3000, $database = null)
    {
        $this->configuration['404'] = $error_page;
        $this->configuration['port'] = $port;


        if (!is_null($database)) {
            $this->configuration['database'] = new Database($database['driver']);
        }
    }

    public function boost(...$classes)
    {
        $routes = [];
        $injectables = [];

        foreach ($classes as $class) {
            // $inject_service = $class::inject();
            $injectables = $class::inject();

            $routes = array_merge($routes, $class::routes());
        }

        $route = $routes[$this->path];

        $controller = $route[$this->method]['controller'];
        $handler = $route[$this->method]['handler'];

        ## Get controller injectables
        $injectables = $controller::inject();

        ## Construct Controller class
        $instance = new $controller(...$injectables);

        if (method_exists($controller, 'config')) {
            $class_config = $controller::config();
        }

        $execute = fn () => $instance->$handler();

        $this->add_routes($execute, $class_config ?? [
            'metadata' => true
        ]);
    }

    public function cors()
    {
        // Allow from any origin
        if (isset($_SERVER['HTTP_ORIGIN'])) {
            // Decide if the origin in $_SERVER['HTTP_ORIGIN'] is one
            // you want to allow, and if so:
            header("Access-Control-Allow-Origin: {$_SERVER['HTTP_ORIGIN']}");
            header('Access-Control-Allow-Credentials: true');
            header('Access-Control-Max-Age: 86400');    // cache for 1 day
            header('Content-Type: application/json; charset=utf-8');
        }

        // Access-Control headers are received during OPTIONS requests
        if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS') {

            if (isset($_SERVER['HTTP_ACCESS_CONTROL_REQUEST_METHOD']))
                // may also be using PUT, PATCH, HEAD etc
                header("Access-Control-Allow-Methods: GET, POST, OPTIONS");

            if (isset($_SERVER['HTTP_ACCESS_CONTROL_REQUEST_HEADERS']))
                header("Access-Control-Allow-Headers: {$_SERVER['HTTP_ACCESS_CONTROL_REQUEST_HEADERS']}");

            exit(0);
        }
    }

    public function set_metadata($css = [], $js = [])
    {
        $this->metadata['css'] = $css;
        $this->metadata['js'] = $js;
    }

    public function dump_property($property)
    {
        var_dump($this->configuration[$property]);
    }

    public function get_database()
    {
        return $this->configuration['database'];
    }

    public function get_metadata()
    {
        return $this->metadata;
    }

    public function add_middlewares($middlewares = [])
    {
        $this->middlewares = $middlewares;
    }

    public function add_guards($guards = [])
    {
        $this->guards = $guards;
    }

    public function add_routes($executable, $class_config = null)
    {
        ## We create APP context
        $context = [
            'configuration' => $this->configuration,
            'metadata' => $this->metadata,
            'request' => $this->request,
            'inject' => $this->configuration['inject'] ?? [],
        ];

        ## Exucete route controller
        $execute = $executable($context);

        ## If route controller returns something, we add it to the router
        if (!empty($execute)) {
            $route_to_add = ['execute' => $execute, 'config' => $class_config];
        }

        ## We create a new router instance passing the route to add - the server request - app context
        $this->router = new AppRouter($route_to_add, $_SERVER, $context);
    }

    public function add_interceptor($interceptors = [])
    {
        $this->interceptors = $interceptors;
    }

    private function merge_metadata($page_data)
    {
        $core_metada = $this->get_metadata();

        $page_data['metadata']['css'] = array_merge($core_metada['css'] ?? [],  $page_data['metadata']['css'] ?? []);
        $page_data['metadata']['js'] = array_merge($core_metada['js'] ?? [],  $page_data['metadata']['js'] ?? []);

        return $page_data;
    }

    public function serve()
    {
        $route_to_execute = $this->router->route_exists();
        $route_exists = $route_to_execute['execute'];
        $route_config = $route_to_execute['config'];


        $has_404 = array_key_exists('404', $this->configuration) ? $this->configuration['404'] : [
            'view' => 'index.php',
        ];

        $page_data = empty($route_exists) ? $has_404 : $route_exists;

        $page_data = $this->merge_metadata($page_data);

        // Server view handler.

        if (isset($page_data['view'])) {
            require_once __DIR__ . "/../views/" . $page_data['view'];
        } else {
            if (isset($route_config['metadata']) && $route_config['metadata'] === false) {
                unset($page_data['metadata']);
            }

            echo json_encode($page_data);
        }
    }
}
