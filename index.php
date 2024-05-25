<?php
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Factory\AppFactory;
use Slim\Views\Twig;
use Slim\Views\TwigMiddleware;

require __DIR__ . '/vendor/autoload.php';

$app = AppFactory::create();
//$app->addRoutingMiddleware();
// Create Twig
$twig = Twig::create('templates', ['cache' => false]);

// Add Twig-View Middleware
$app->add(TwigMiddleware::create($app, $twig));


$app->get('/', function (Request $request, Response $response, $args) {
    $view = Twig::fromRequest($request);
    return  $view->render($response, 'index.html'); 
 
});


$app->get('/home', function (Request $request, Response $response, $args) {
    $view = Twig::fromRequest($request);
    return  $view->render($response, 'index.html'); 
 
});

$twig = Twig::create('templates', ['cache' => false]);

$app->get('/contact', function (Request $request, Response $response, $args) {
    //var_dump('test'); die('sss');
    $view = Twig::fromRequest($request);
   return  $view->render($response, 'contact.html'); 
    //print_r($view); die();
})->setName('contact');


$app->get('/services', function (Request $request, Response $response, $args) {
    //var_dump('test'); die('sss');
    $view = Twig::fromRequest($request);
   return  $view->render($response, 'services.html'); 
    //print_r($view); die();
})->setName('services');

$app->get('/about', function (Request $request, Response $response, $args) {
    //var_dump('test'); die('sss');
    $view = Twig::fromRequest($request);
   return  $view->render($response, 'about.html'); 
    //print_r($view); die();
})->setName('about');

$app->run();