<?php

namespace Ddtix\Router;

class Route
{
    public static function load(): void
    {
        spl_autoload_register(function ($className) {
            $parts = explode('\\', $className);
            $fileName = array_pop($parts);
            $folders = implode('/', $parts);
            $filePath = getenv('BASE_PATH') . '/../src/' . $folders . '/' . $fileName . '.php';

            if (file_exists($filePath)) {
                require_once $filePath;
            }
        });

        if (isset($_SERVER['REQUEST_URI']) && is_string($_SERVER['REQUEST_URI'])) {

            $url = explode('/', $_SERVER['REQUEST_URI']);
            $connector = 'Controllers\\' . ucfirst($url[1]);
            $result = $connector::index();

            echo match (true) {
                is_string($result), is_numeric($result) => $result,
                is_array($result), is_object($result) => json_encode($result, JSON_UNESCAPED_UNICODE),
                is_bool($result) => $result ? 'true' : 'false',
                default => '',
            };
        }
    }
}
