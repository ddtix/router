<?php

namespace Ddtix\Router;

use Controllers;

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
            } else {
                $filePath = getenv('BASE_PATH') . '/../src/Controllers/Notfound.php';
            }
        });

        if (isset($_SERVER['REQUEST_URI']) && is_string($_SERVER['REQUEST_URI'])) {

            $url = explode('/', $_SERVER['REQUEST_URI']);

            $filePath = getenv('BASE_PATH') . '/../src/Controllers/' . $url[1] . '.php';

            $controllerName = $url[1] ?? false;

            if ($controllerName && file_exists($filePath)) {
                $connector = 'Controllers\\' . $controllerName;
            } elseif (empty($controllerName)) {
                $connector = 'Controllers\\Index';
            } else {
                $connector = 'Controllers\\NotFound';
            }

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
