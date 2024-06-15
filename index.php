<?php
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Factory\AppFactory;
use Slim\Views\Twig;
use Slim\Views\TwigMiddleware;
use Dotenv\Dotenv;
use Symfony\Component\Translation\Translator;
use Symfony\Component\Translation\Loader\ArrayLoader;
use Slim\Exception\HttpNotFoundException;

require __DIR__ . '/vendor/autoload.php';

//translations
require __DIR__ . '/translations/home_translations.php';

   
$dotenv = Dotenv::createImmutable(__DIR__);
$dotenv->load();
//var_dump($_ENV);

$languages = ['es','en'];


$app = AppFactory::create();
//$app->addRoutingMiddleware();
// Create Twig
$twig = Twig::create('templates', ['cache' => false]);

// Set up Symfony Translator
$translator = new Translator('en');
$translator->addLoader('array', new ArrayLoader());



foreach ($translations as $locale => $messages) {
    $translator->addResource('array', $messages, $locale);
}

// Add Twig-View Middleware
$app->add(TwigMiddleware::create($app, $twig));



// Middleware to handle the language selection
$app->add(function ($request, $handler) use ($translator) {
    $path = $request->getUri()->getPath();
    $segments = explode('/', trim($path, '/'));
    $locale = $segments[0] ?? 'en';

    if (!in_array($locale, ['en', 'es'])) {
        // Default to English if an unsupported language is provided
        $locale = 'en';
    }

    $translator->setLocale($locale);
    $request = $request->withAttribute('translator', $translator);
    $request = $request->withAttribute('locale', $locale);

    return $handler->handle($request);
});
$app->get('/', function (Request $request, Response $response, $args) use ($translations) {
    
    return $response
  ->withHeader('Location', '/en/home')
  ->withStatus(302);
    
});


$app->get('/en', function (Request $request, Response $response, $args) use ($translations) {
    $locale = 'en';

    $locale = $request->getAttribute('locale');
    if(!in_array($locale,$translations)) {
        $request = $request->withAttribute('locale', 'en');
        $locale = 'en'; 

    }
    $objectTranslations = (json_decode(json_encode($translations)));

    $translations = ["translations" => $objectTranslations->$locale->home];
   
    $view = Twig::fromRequest($request);
    return  $view->render($response, 'index.html',  $translations); 
 
});



$app->get('/{locale}/home', function (Request $request, Response $response, $args) use ($translations){

  
    $locale = $request->getAttribute('locale');

    if(!array_key_exists($locale, $translations)) {$locale = 'en'; }
    $objectTranslations = (json_decode(json_encode($translations)));
    $translations = ["translations" => $objectTranslations->$locale->home, "menu" => $objectTranslations->$locale->menu];
    $view = Twig::fromRequest($request);
    return  $view->render($response, 'index.html',  $translations); 
 
});

$twig = Twig::create('templates', ['cache' => false]);



$app->get('/{locale}/contact', function (Request $request, Response $response, $args) use ($translations) {

    $locale = $request->getAttribute('locale');
    if(!array_key_exists($locale, $translations)) {$locale = 'en'; }
   $objectTranslations = (json_decode(json_encode($translations)));

   $translations = ["translations" => $objectTranslations->$locale->services,  "menu" => $objectTranslations->$locale->menu];
    $view = Twig::fromRequest($request);

   return  $view->render($response, 'contact.html', $translations); 
})->setName('contact');

$app->post('/contact', function (Request $request, Response $response, $args) {

     $data = ['success'];
    $view = Twig::fromRequest($request);
   return  $view->render($response, 'contact.html', $data); 
})->setName('contact');



$app->get('/{locale}/services', function (Request $request, Response $response, $args) use($translations){

    $locale = $request->getAttribute('locale');
     if(!array_key_exists($locale, $translations)) {$locale = 'en'; }
    $objectTranslations = (json_decode(json_encode($translations)));

    $translations = ["translations" => $objectTranslations->$locale->services,  "menu" => $objectTranslations->$locale->menu];

    $view = Twig::fromRequest($request);
   return  $view->render($response, 'services.html', $translations); 
})->setName('services');




$app->get('/{locale}/about', function (Request $request, Response $response, $args) use($translations){
    $locale = $request->getAttribute('locale');
    if(!array_key_exists($locale, $translations)) {$locale = 'en'; }
   $objectTranslations = (json_decode(json_encode($translations)));

   $translations = ["translations" => $objectTranslations->$locale->services,  "menu" => $objectTranslations->$locale->menu];

   $view = Twig::fromRequest($request);
   return  $view->render($response, 'about.html', $translations); 
})->setName('about');



try {

    $app->run();

} catch (HttpNotFoundException $e){
    // Create a new response object
    $response = new \Slim\Psr7\Response();
    
    // Render the 404-page.html with Twig
    $response = $twig->render($response, '404-page.html', $translations);
    
    header('HTTP/1.1 404 Not Found');
    echo $response->getBody();
}
