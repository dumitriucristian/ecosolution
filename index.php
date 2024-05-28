<?php
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Factory\AppFactory;
use Slim\Views\Twig;
use Slim\Views\TwigMiddleware;
use Dotenv\Dotenv;

require __DIR__ . '/vendor/autoload.php';

   
$dotenv = Dotenv::createImmutable(__DIR__);
$dotenv->load();
//var_dump($_ENV);

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
    $view = Twig::fromRequest($request);

   return  $view->render($response, 'contact.html'); 
})->setName('contact');

$app->post('/contact', function (Request $request, Response $response, $args) {

       //$data = $request->getParsedBody();
      // $name = filter_var($data['name'], FILTER_SANITIZE_STRING);
      // $email = filter_var($data['email'], FILTER_SANITIZE_EMAIL);
     //  $message = filter_var($data['message'], FILTER_SANITIZE_STRING);

        $data = ['success'];
    $view = Twig::fromRequest($request);
   return  $view->render($response, 'contact.html', $data); 
})->setName('contact');

$app->get('/services', function (Request $request, Response $response, $args) {
    $view = Twig::fromRequest($request);
   return  $view->render($response, 'services.html'); 
})->setName('services');

$app->get('/about', function (Request $request, Response $response, $args) {
    $view = Twig::fromRequest($request);
   return  $view->render($response, 'about.html'); 
})->setName('about');

$app->run();