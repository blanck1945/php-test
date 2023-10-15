<?php

namespace Core\Injectable;

class Injectable
{
    public $inject = [];

    public function __construct(...$classes)
    {
        foreach ($classes as $class) {
            $replace_injectable = str_replace('\\', '/', $class['class']);
            $path = __DIR__ . "/../../" . $replace_injectable . ".php";
            require_once $path;
            $split = explode('\\', $class['class']);
            $end = end($split);
            $this->inject =
                array_merge(
                    [
                        $end => new $class['class'](...$class['arguments'] ?? [])
                    ]
                );
        }
    }
}
