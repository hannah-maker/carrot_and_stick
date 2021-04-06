<?php
require './pdos/DatabasePdo.php';
require './vendor/autoload.php';

require './pdos/UserPdo.php';//로그인, 가입 등
require './pdos/ReviewPdo.php';
require './pdos/GoalPdo.php';

use \Monolog\Logger as Logger;
use Monolog\Handler\StreamHandler;

header('Access-Control-Allow-Origin: *');
header('Access-Control-Max-Age: 86400');
header('Access-Control-Allow-Headers: x-requested-with');
header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS');

date_default_timezone_set('Asia/Seoul');
ini_set('default_charset', 'utf8mb4');
//$fcmToken = "d6DvQXqrVJc:APA91bFUL1iYVCY-k8Cr18WJ40GoqPw-EJJ0Vra8owhxNVuvJF-S2j6YRk8vb7iKju74LGaAII_ml40OQMLzhMpcZF2iPE58nEpNaezATBmffjT6WlKNK-fMtHwKdaA6OLJzGlIOjZ9O";
////에러출력하게 하는 코드
//error_reporting(E_ALL); ini_set("display_errors", 1);

//Main Server API
$dispatcher = FastRoute\simpleDispatcher(function (FastRoute\RouteCollector $r) {
    /* ******************   Test   ****************** */
    $r->addRoute('GET', '/testAPI', ['IndexController', 'test']);
    $r->addRoute('GET', '/board/{boardNo}', ['GoalController', 'boardDetail']);
    $r->addRoute('GET', '/boards', ['GoalController', 'boardList']);
    $r->addRoute('POST', '/board', ['GoalController', 'addArticle']);
    $r->addRoute('GET', '/category', ['GoalController', 'categoryList']);




//    $r->addRoute('GET', '/goal/{goalNo}', ['GoalController', 'goalListDetail']);


    $r->addRoute('GET', '/jwt', ['MainController', 'validateJWT']);
    $r->addRoute('POST', '/user', ['MainController', 'signUp']);
    $r->addRoute('POST', '/user/token', ['MainController', 'login']);
    $r->addRoute('GET', '/data', ['MainController', 'testJwtData']);











    $r->addRoute('GET', '/user', ['MainController', 'user']); //마이페이지 조회





    $r->addRoute('PATCH', '/user', ['MainController', 'deleteUser']);

    $r->addRoute('GET', '/goal/recommendation', ['GoalController', 'login']);
    $r->addRoute('POST', '/goal', ['GoalController', 'addGoal']);
    $r->addRoute('GET', '/goal/ongoing', ['GoalController', 'ongoingGoal']);
    $r->addRoute('GET', '/goal/finished', ['GoalController', 'finishedGoal']);
    $r->addRoute('GET', '/goal/checkList', ['GoalController', 'checkListPage']);

    $r->addRoute('GET', '/goal/{goalNo}', ['GoalController', 'goalListDetail']);

    $r->addRoute('PATCH', '/goal', ['GoalController', 'updateGoal']);
    $r->addRoute('DELETE', '/goal', ['GoalController', 'deleteGoal']);
    $r->addRoute('POST', '/goal/check', ['GoalController', 'goalCheck']);

    $r->addRoute('POST', '/collection', ['IndexController', 'postCollection']);
    $r->addRoute('GET', '/collection', ['IndexController', 'getCollection']);
//
//


//    $r->addRoute('GET', '/users', 'get_all_users_handler');
//    // {id} must be a number (\d+)
//    $r->addRoute('GET', '/user/{id:\d+}', 'get_user_handler');
//    // The /{title} suffix is optional
//    $r->addRoute('GET', '/articles/{id:\d+}[/{title}]', 'get_article_handler');
});

// Fetch method and URI from somewhere
$httpMethod = $_SERVER['REQUEST_METHOD'];
$uri = $_SERVER['REQUEST_URI'];

// Strip query string (?foo=bar) and decode URI
if (false !== $pos = strpos($uri, '?')) {
    $uri = substr($uri, 0, $pos);
}
$uri = rawurldecode($uri);

$routeInfo = $dispatcher->dispatch($httpMethod, $uri);

// 로거 채널 생성
$accessLogs = new Logger('ACCESS_LOGS');
$errorLogs = new Logger('ERROR_LOGS');
// log/your.log 파일에 로그 생성. 로그 레벨은 Info
$accessLogs->pushHandler(new StreamHandler('logs/access.log', Logger::INFO));
$errorLogs->pushHandler(new StreamHandler('logs/errors.log', Logger::ERROR));
// add records to the log
//$log->addInfo('Info log');
// Debug 는 Info 레벨보다 낮으므로 아래 로그는 출력되지 않음
//$log->addDebug('Debug log');
//$log->addError('Error log');

switch ($routeInfo[0]) {
    case FastRoute\Dispatcher::NOT_FOUND:
        // ... 404 Not Found
        echo "404 Not Found";
        break;
    case FastRoute\Dispatcher::METHOD_NOT_ALLOWED:
        $allowedMethods = $routeInfo[1];
        // ... 405 Method Not Allowed
        echo "405 Method Not Allowed";
        break;
    case FastRoute\Dispatcher::FOUND:
        $handler = $routeInfo[1];
        $vars = $routeInfo[2];

        switch ($routeInfo[1][0]) {
            case 'IndexController':
                $handler = $routeInfo[1][1];
                $vars = $routeInfo[2];
                require './controllers/IndexController.php';
                break;
            case 'MainController':
                $handler = $routeInfo[1][1];
                $vars = $routeInfo[2];
                require './controllers/MainController.php';
                break;
            case 'ReviewController':
                $handler = $routeInfo[1][1];
                $vars = $routeInfo[2];
                require './controllers/ReviewController.php';
                break;
            case 'GoalController':
                $handler = $routeInfo[1][1]; $vars = $routeInfo[2];
                require './controllers/GoalController.php';
                break;
            /*case 'SearchController':
                $handler = $routeInfo[1][1]; $vars = $routeInfo[2];
                require './controllers/SearchController.php';
                break;
            case 'ReviewController':
                $handler = $routeInfo[1][1]; $vars = $routeInfo[2];
                require './controllers/ReviewController.php';
                break;
            case 'ElementController':
                $handler = $routeInfo[1][1]; $vars = $routeInfo[2];
                require './controllers/ElementController.php';
                break;
            case 'AskFAQController':
                $handler = $routeInfo[1][1]; $vars = $routeInfo[2];
                require './controllers/AskFAQController.php';
                break;*/
        }

        break;
}
