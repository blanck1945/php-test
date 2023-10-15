<?php

namespace Core\Router;

class AppRouter
{
    public const  INIT_POINT = '/';

    static $ctx = null;
    public function __construct(private array $routes, private array $request, private array $con)
    {
        $this->routes = $routes;
        $this->request = $request;
        self::$ctx = $con;
    }

    static public function guard_route(string $route_method, $callback)
    {
        $method = $_SERVER['REQUEST_METHOD'];

        if ($method === $route_method) {
            return $callback();
        }
    }

    static public function VIEW()
    {
    }

    static public function GET($path, $class, $controller)
    {
        $contructed_class = new $class();

        if (str_contains($path, ':')) {
            $url = $_SERVER['REQUEST_URI'];

            $split_path = explode(':', $path);

            # Remove last character from split_path
            $split_path[0] = substr($split_path[0], 0, -1);

            if (str_contains($url, $split_path[0]) && $url !== $split_path[0]) {
                $query_param = explode('/', $url)[2];
                return self::execute_route($path, 'GET', fn () => $contructed_class::$controller($query_param));
            }
        } else {
            return fn ($ctx) => self::execute_route($path, 'GET', fn () => $contructed_class->$controller($ctx));
        }
    }

    static public function POST($path,  $class, $controller)
    {
        return fn ($ctx) => self::execute_route($path, 'POST', fn () => $class::$controller($ctx));
    }

    static function execute_route_with_params(string $path, string $route_method, $callback)
    {
        $url = $_SERVER['REQUEST_URI'];
        $query_param = explode('/', $url)[2];

        $route_to_execute = $callback($query_param);

        return $route_to_execute;
    }


    static public function execute_route(string $path, string $route_method, $callback)
    {
        $url = $_SERVER['REQUEST_URI'];
        $method = $_SERVER['REQUEST_METHOD'];
        $route_to_execute = [];

        if (str_contains($path, ':')) {
            $split_path = explode(':', $path);

            # Remove last character from split_path
            $split_path[0] = substr($split_path[0], 0, -1);

            if (str_contains($url, $split_path[0]) && $url !== $split_path[0]) {
                $query_param = explode('/', $url)[2];
                $route_to_execute = $callback($query_param);
            }
        } else {

            if ($url === $path && $method === $route_method) {
                $route_to_execute = $callback();
            }
        }

        return [
            'route' => $route_to_execute,
            'metadata' => $route_to_execute['metadata'] ?? [],
            'variables' => $route_to_execute['variables'] ?? null,
            'view' => $route_to_execute['view'] ?? null,
            'path' => $path,
            'method' => $route_method
        ];
    }

    public function show_routes()
    {
        var_dump($this->routes);
    }

    public function get_routes()
    {
        return $this->routes;
    }

    public function format_url(): string
    {
        $base = $this->request['REQUEST_URI'];

        if (str_contains($base, '?')) {
            $base = explode("?", $base)[0];
        }

        return $base;
    }

    public function route_exists()
    {
        return $this->get_routes() ?? [];
    }

    public function get_page_data($route, $configuration)
    {
        if (empty($route)) {
            return $configuration['404'];
        } else {
            return $route;
        }
    }

    public function map_routes()
    {
        $base = $this->format_url();
        $method = $this->request['REQUEST_METHOD'];

        $route = $this->get_routes()[$base][$method] ?? [];

        if (empty($route)) {
            $route = [
                'view' => 'index.php',
                'variables' => [
                    'first' => ['Tokyo', 'Kyoto', 'Nagoya'],
                    'second' => ['東京都', '京都市', '名古屋市']
                ],
                'metadata' => [
                    'css' => ['home.css']
                ]
            ];
        }


        /**
         * If route has an error, return redirect route.
         */
        // if (isset($route[$base]['variables']['has_error'])) {
        //     return header($route[$base]['variables']['redirect']);
        // }

        /**
         * If route is not found, return default route (/).
         */
        if (isset($route['default']))
            $base = SELF::INIT_POINT;

        $page_date = [
            'metadata' => $route['metadata'] ?? [],
            'variables' => $route['variables'] ?? null,
            'view' =>  $route['view']
        ];

        return $page_date;
    }
}
