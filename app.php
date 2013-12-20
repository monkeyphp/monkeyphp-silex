<?php
/* require the composer autoloader */
require_once 'vendor/autoload.php';

/* use statements */

use Monkeyphp\Controller\AdminController;
use Monkeyphp\Controller\IndexController;
use Silex\Application;
use Silex\Provider\ServiceControllerServiceProvider;
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
$app->register(new ServiceControllerServiceProvider());

/* register the IndexController */
$app['index.controller'] = $app->share(function() use ($app) {
    $twigEnvironment = $app['twig'];
    $indexController = new IndexController($twigEnvironment);
    return $indexController;
});

/* register the AdminController */
$app['admin.controller'] = $app->share(function() use ($app) {
    $twigEnvironment = $app['twig'];
    $adminController = new AdminController($twigEnvironment);
    return $adminController;
});

/* register the '/' route */
$app->get('/', 'index.controller:indexAction');

/* register the 'footer' route */
$app->get('footer', 'index.controller:footerAction');

/* register the 'header' route */
$app->get('header', 'index.controller:headerAction');

/* register the '/admin' route */
$app->get('/admin', 'admin.controller:indexAction');


/* run the application */
$app->run();
