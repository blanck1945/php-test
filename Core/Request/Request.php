<?php

namespace Core\Request;

class Request
{
    public $server;
    public $request;
    public $method;
    public $path;
    public $body;
    public $params;


    public function __construct()
    {
        $this->server = $_SERVER;
        $this->request = $_REQUEST;
        $this->method = $_SERVER['REQUEST_METHOD'];
        $this->path = $_SERVER['REQUEST_URI'];
        $this->body = json_decode(file_get_contents('php://input'));
        $this->params = $_GET;
    }
}
