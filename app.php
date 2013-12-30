<?php
/**
 * Monkeyphp.com
 *
 * Personal website of David White <david@monkeyphp.com>
 *
 *   /~\
 *  C oo
 *  _( ^)
 * /    ~\
 *
 * @category Monkeyphp
 * @package  Application
 * @author   David White [monkeyphp] <david@monkeyphp.com>
 */

// require the composer autoloader
require_once 'vendor/autoload.php';

// use statements
use Monkeyphp\Controller\AdminController;
use Monkeyphp\Controller\IndexController;
use Monkeyphp\Controller\LoginController;
use Monkeyphp\Provider\ElasticSearchServiceProvider;
use Monkeyphp\User\UserProvider;
use Silex\Application;
use Silex\Provider\FormServiceProvider;
use Silex\Provider\SecurityServiceProvider;
use Silex\Provider\ServiceControllerServiceProvider;
use Silex\Provider\TwigServiceProvider;
use Silex\Provider\UrlGeneratorServiceProvider;

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

// register the ElasticsearchServiceProvider
$app->register(new ElasticSearchServiceProvider(), array());

// register the FormServiceProvider
$app->register(new FormServiceProvider(), array(
    'form.secret' => '0123456789ABCDEEFGHIJKLMNOPQRSTUVWXYZ'
));

// register the SecurityServiceProvider
$app->register(new SecurityServiceProvider(), array(
    'security.firewalls' => array(
        'admin' => array(
            'pattern' => '^/admin',
            'form' => array(
                'login_path' => '/login', 
                'check_path' => '/admin/login_check', 
            ),
            'logout' => array(
                'logout_path' => '/logout'
            ),
            'users' => $app->share(function() use ($app) {
                return new UserProvider($app['elasticsearch']);
            }),
        ),
    ),
));

/* register the ServiceControllerServiceProvider  */
$app->register(new ServiceControllerServiceProvider());

/* register the UrlGeneratorServiceProvider */
$app->register(new UrlGeneratorServiceProvider());

/* register the IndexController */
$app['index.controller'] = $app->share(function() use ($app) {
    $twigEnvironment = $app['twig'];
    $indexController = new IndexController($twigEnvironment);
    return $indexController;
});

/* register the LoginController */
$pp['login.controller'] = $app->share(function() use ($app) {
    $twigEnvironment = $app['twig'];
    $formFactory     = $app['form.factory'];
    $urlGenerator    = $app['url_generator'];
    $loginController = new LoginController($twigEnvironment, $formFactory, $urlGenerator);
    return $loginController;
});

/* register the AdminController */
$app['admin.controller'] = $app->share(function() use ($app) {
    $twigEnvironment = $app['twig'];
    $adminController = new AdminController($twigEnvironment);
    return $adminController;
});

/* register the '/' route */
$app->get('/', 'index.controller:indexAction')->bind('index_index');

/* register the '/footer' route */
$app->get('/footer', 'index.controller:footerAction')->bind('index_footer');

/* register the '/header' route */
$app->get('/header', 'index.controller:headerAction')->bind('index_header');

/* register the '/login' route */
$app->get('/login', 'login.controller:loginAction')->bind('login_login');

/* register the '/admin' route */
$app->get('/admin', 'admin.controller:indexAction')->bind('admin_index');


/* run the application */
$app->run();
