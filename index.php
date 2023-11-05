<?php

require_once __DIR__ . '/vendor/autoload.php';

//fix cross origin blocked
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, OPTIONS");
header("Access-Control-Allow-Headers: Origin, X-Requested-With, Content-Type, Accept");
header('Content-Type: application/json');
$method = $_SERVER['REQUEST_METHOD'];
if ($method == "OPTIONS") {
    header('Access-Control-Allow-Origin: *');
    header("Access-Control-Allow-Headers: X-API-KEY, Origin, X-Requested-With, Content-Type, Accept, Access-Control-Request-Method,Access-Control-Request-Headers, Authorization");
    header("HTTP/1.1 200 OK");
    die();
}

use App\Controllers\Events\EventsController as EventsController;
use App\Controllers\Categories\CategoryController as CategoryController;
use App\Controllers\Users\UsersController as UsersController;
use App\Models\Categories\CategoryModel as CategoryModel;
use App\Models\Events\EventsModel as EventsModel;
use App\Models\Users\UsersModel as UsersModel;
use DI\Container;
use FastRoute\RouteCollector;
use function FastRoute\simpleDispatcher;

// Create a dependency injection container
$container = new Container;

// Define how to create instances of your classes
$container->set(EventsController::class, function () use ($container) {
    return new EventsController($container->get(EventsModel::class));
});
$container->set(CategoryController::class, function () use ($container) {
    return new CategoryController($container->get(CategoryModel::class));
});
$container->set(UsersController::class, function () use ($container) {
    return new UsersController($container->get(UsersModel::class));
});

// routes
$dispatcher = simpleDispatcher(function (RouteCollector $r) {
    $r->get('/', [EventsController::class, 'index']);
    $r->get('/events', [EventsController::class, 'index']);
    $r->get('/event/{event_id:\d+}', [EventsController::class, 'eventById']);
    $r->get('/events/category/{category_id:\d+}', [EventsController::class, 'eventByCategory']);
    $r->get('/category', [CategoryController::class, 'index']);
    $r->post('/register', [UsersController::class, 'signUp']);
    $r->post('/login', [UsersController::class, 'login']);
    $r->post('/logout', [UsersController::class, 'logout']);
});

// Dispatch the current request
$httpMethod = $_SERVER['REQUEST_METHOD'];
$uri = $_SERVER['REQUEST_URI'];
$routeInfo = $dispatcher->dispatch($httpMethod, $uri);

switch ($routeInfo[0]) {
    case FastRoute\Dispatcher::NOT_FOUND:
        // 404 Not Found
        http_response_code(404);
        echo json_encode(['error' => true, 'message' => "Page Not found"]);
        break;
    case FastRoute\Dispatcher::METHOD_NOT_ALLOWED:
        // 405 Method Not Allowed
        $allowedMethods = $routeInfo[1];
        http_response_code(405);
        echo json_encode(['error' => true, 'message' => 'Method not allowed', 'allowedMethods' => $allowedMethods]);
        break;
    case FastRoute\Dispatcher::FOUND:
        // Call the handler with vars
        $handler = $routeInfo[1];
        $vars = $routeInfo[2];

        // Extract the endpoint from the URI
        $endpoint = explode('/', trim($uri, '/'))[0];

        // Get the data from the request body
        if ($endpoint === 'login' || $endpoint === 'register') {
            $data = (array) json_decode(file_get_contents('php://input'), true);
            // Add the data to the vars
            $vars['data'] = $data;
        }
        if ($endpoint === 'logout') {
            // Get the data from the request body
            $data = (array) json_decode(file_get_contents('php://input'), true);
            // Add the API key from the headers to the data
            $data['api_key'] = $_SERVER['HTTP_X_API_KEY'] ?? '';
            // Add the data to the vars
            $vars['data'] = $data;
        }
        // Assuming $handler is an array where the first element is the class name and the second is the method
        $className = $handler[0];
        $method = $handler[1];
        // Get the instance from the container
        $class = $container->get($className);
        // Call the method with vars
        call_user_func_array([$class, $method], array_values($vars));
        break;
}