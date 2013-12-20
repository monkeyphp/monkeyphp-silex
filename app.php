<?php
/* require the composer autoloader */
require_once 'vendor/autoload.php';

/* use statements */
use Silex\Application;
use Silex\Provider\TwigServiceProvider;

/* create a new Application instance */
$app = new Application(array());

/* register the TwigServiceProvider */
$app->register(new TwigServiceProvider(), array(
    'twig.path' => __DIR__ . '/templates',
    'twig.options' => array(
        'debug'            => false,
        'charset'          => 'utf-8',
        'cache'            => '/tmp/cache/twig',
        'auto_reload'      => true,
        'strict_variables' => true,
        'autoescape'       => true,
        'optimizations'    => -1
    )
));

/* register the ServiceControllerServiceProvider  */
$app->register(new Silex\Provider\ServiceControllerServiceProvider());

/* register the IndexController */
$app['index.controller'] = $app->share(function() use ($app) {
    $twigEnvironment = $app['twig'];
    $indexController = new \Monkeyphp\Controller\IndexController($twigEnvironment);
    return $indexController;
});

/* register the '/' route */
$app->get('/', 'index.controller:indexAction');

/* run the application */
$app->run();
