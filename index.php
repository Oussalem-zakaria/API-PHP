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

$eventsModel = new EventsModel;
$eventsController = new EventsController($eventsModel);


$categoryModel = new CategoryModel;
$CategoryController = new CategoryController($categoryModel);


$usersModel = new UsersModel;
$usersController = new UsersController($usersModel);

$segments = explode("/", parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH));

if ($_SERVER['REQUEST_METHOD'] === "GET" && empty($segments[1])) {
    $eventsController->index();
    exit;
    // show all events
} else if ($_SERVER['REQUEST_METHOD'] === "GET" && $segments[1] === "events" && empty($segments[2])) {
    $eventsController->index();
    exit;
    // show event by id
} else if ($_SERVER['REQUEST_METHOD'] === "GET" && $segments[1] === "event" && is_numeric($segments[2])) {
    $event = $eventsController->eventById($segments[2]);
    exit;
}
// show event category id
else if ($_SERVER['REQUEST_METHOD'] === "GET" && $segments[1] === "events" && $segments[2] === "category" && is_numeric($segments[3])) {
    $eventsController->eventByCategory($segments[3]);
    exit;
} else if ($_SERVER['REQUEST_METHOD'] === "GET" && $segments[1] === "category" && empty($segments[2])) {
    $CategoryController->index();
    exit;
} else if ($_SERVER['REQUEST_METHOD'] === "POST" && $segments[1] === "register" && empty($segments[2])) {
    $data = (array) json_decode(file_get_contents('php://input'), true);
    $usersController->signUp($data);
    exit;
} else if ($_SERVER['REQUEST_METHOD'] === "POST" && $segments[1] === "login" && empty($segments[2])) {
    $data = (array) json_decode(file_get_contents('php://input'), true);
    $usersController->login($data);
    exit;
} else if ($_SERVER['REQUEST_METHOD'] === "POST" && $segments[1] === "logout" && empty($segments[2])) {
    $data = (array) json_decode(file_get_contents('php://input'), true);
    $data['api_key'] = $_SERVER['HTTP_X_API_KEY'] ?? '';
    $usersController->logout($data);
    exit;
} else {
    http_response_code(404);
    echo json_encode(['error' => true, 'message' => "Page Not found"]);
}


// <?php

// require_once __DIR__ . '/vendor/autoload.php';

// //fix cross origin blocked
// header("Access-Control-Allow-Origin: *");
// header("Access-Control-Allow-Methods: GET, POST, OPTIONS");
// header("Access-Control-Allow-Headers: Origin, X-Requested-With, Content-Type, Accept");
// header('Content-Type: application/json');
// $method = $_SERVER['REQUEST_METHOD'];
// if ($method == "OPTIONS") {
//     header('Access-Control-Allow-Origin: *');
//     header("Access-Control-Allow-Headers: X-API-KEY, Origin, X-Requested-With, Content-Type, Accept, Access-Control-Request-Method,Access-Control-Request-Headers, Authorization");
//     header("HTTP/1.1 200 OK");
//     die();
// }

// use App\Controllers\Events\EventsController as EventsController;
// use App\Controllers\Categories\CategoryController as CategoryController;
// use App\Controllers\Users\UsersController as UsersController;
// use App\Models\Categories\CategoryModel as CategoryModel;
// use App\Models\Events\EventsModel as EventsModel;
// use App\Models\Users\UsersModel as UsersModel;

// $eventsModel = new EventsModel;
// $eventsController = new EventsController($eventsModel);


// $categoryModel = new CategoryModel;
// $CategoryController = new CategoryController($categoryModel);


// $usersModel = new UsersModel;
// $usersController = new UsersController($usersModel);

// $segments = explode("/", parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH));

// if ($_SERVER['REQUEST_METHOD'] === "GET" && empty($segments[1])) {
//     $eventsController->index();
//     exit;
//     // show all events
// } else if ($_SERVER['REQUEST_METHOD'] === "GET" && $segments[1] === "events" && empty($segments[2])) {
//     $eventsController->index();
//     exit;
//     // show event by id
// } else if ($_SERVER['REQUEST_METHOD'] === "GET" && $segments[1] === "event" && is_numeric($segments[2])) {
//     $event = $eventsController->eventById($segments[2]);
//     exit;
// }
// // show event category id
// else if ($_SERVER['REQUEST_METHOD'] === "GET" && $segments[1] === "events" && $segments[2] === "category" && is_numeric($segments[3])) {
//     $eventsController->eventByCategory($segments[3]);
//     exit;
// } else if ($_SERVER['REQUEST_METHOD'] === "GET" && $segments[1] === "category" && empty($segments[2])) {
//     $CategoryController->index();
//     exit;
// } else if ($_SERVER['REQUEST_METHOD'] === "POST" && $segments[1] === "register" && empty($segments[2])) {
//     $data = (array) json_decode(file_get_contents('php://input'), true);
//     $usersController->signUp($data);
//     exit;
// } else if ($_SERVER['REQUEST_METHOD'] === "POST" && $segments[1] === "login" && empty($segments[2])) {
//     $data = (array) json_decode(file_get_contents('php://input'), true);
//     $usersController->login($data);
//     exit;
// } else if ($_SERVER['REQUEST_METHOD'] === "POST" && $segments[1] === "logout" && empty($segments[2])) {
//     $data = (array) json_decode(file_get_contents('php://input'), true);
//     $data['api_key'] = $_SERVER['HTTP_X_API_KEY'] ?? '';
//     $usersController->logout($data);
//     exit;
// } else {
//     http_response_code(404);
//     echo json_encode(['error' => true, 'message' => "Page Not found"]);
// }
