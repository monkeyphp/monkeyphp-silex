<?php
/*******************************************************************************
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
 *******************************************************************************/
require_once '../vendor/autoload.php';

use Assetic\Asset\AssetCache;
use Assetic\Asset\GlobAsset;
use Assetic\Cache\FilesystemCache;
use Assetic\Filter\Sass\SassFilter;
use Monkeyphp\Controller\AboutController;
use Monkeyphp\Controller\AdminController;
use Monkeyphp\Controller\ContactController;
use Monkeyphp\Controller\IndexController;
use Monkeyphp\Controller\LoginController;
use Monkeyphp\Provider\ElasticSearchServiceProvider;
use Monkeyphp\User\UserProvider;
use Psr\Log\LogLevel;
use Silex\Application;
use Silex\Provider\FormServiceProvider;
use Silex\Provider\MonologServiceProvider;
use Silex\Provider\SecurityServiceProvider;
use Silex\Provider\ServiceControllerServiceProvider;
use Silex\Provider\SessionServiceProvider;
use Silex\Provider\TranslationServiceProvider;
use Silex\Provider\TwigServiceProvider;
use Silex\Provider\UrlGeneratorServiceProvider;
use Silex\Provider\ValidatorServiceProvider;
use SilexAssetic\AsseticServiceProvider;
use Symfony\Component\Debug\Debug;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Translation\Loader\YamlFileLoader;

/*******************************************************************************
 * create a new Application instance
 *******************************************************************************/
$app = new Application();

/*******************************************************************************
 * Trust local proxies
 *******************************************************************************/
Request::setTrustedProxies(array('127.0.0.1'));

/*******************************************************************************
 * set the current environment
 *******************************************************************************/
defined('APPLICATION_ENV') ||
    define('APPLICATION_ENV',
        (getenv('APPLICATION_ENV') ? getenv('APPLICATION_ENV') : 'production'));

if (APPLICATION_ENV === 'development') {
    ini_set('error_reporting', -1);
    ini_set('display_errors', 1);
    $app['debug'] = true;
    Debug::enable();
}

/*******************************************************************************
 * register the SessionServiceProvider
 *******************************************************************************/
$app->register(new SessionServiceProvider());

/*******************************************************************************
 * register the ValidatorServiceProvider
 *******************************************************************************/
$app->register(new ValidatorServiceProvider());

/*******************************************************************************
 * register the TwigServiceProvider
 *******************************************************************************/
$app->register(new TwigServiceProvider(), array(
    'twig.path'    => __DIR__ . '/../resources/templates',
    'twig.options' => array(
        'debug'            => true,
        'charset'          => 'utf-8',
        'cache'            => __DIR__ . '/../resources/cache/twig',
        'auto_reload'      => true,
        'strict_variables' => true,
        'autoescape'       => true,
        'optimizations'    => -1
    )
));

/*******************************************************************************
 * register the TranslationServiceProvider
 *******************************************************************************/
$app->register(new TranslationServiceProvider(), array(
    'locale_fallbacks' => array('en'),
));

$app['translator'] = $app->share($app->extend('translator', function($translator, $app) {
    $translator->addLoader('yaml', new YamlFileLoader());
    $translator->addResource('yaml', __DIR__ . '/../resources/locales/en.yml', 'en');
    $translator->addResource('yaml', __DIR__ . '/../resources/locales/de.yml', 'de');
    $translator->addResource('yaml', __DIR__ . '/../resources/locales/fr.yml', 'fr');
    return $translator;
}));

/*******************************************************************************
 * register the ElasticsearchServiceProvider
 *******************************************************************************/
$app->register(new ElasticSearchServiceProvider());

/*******************************************************************************
 * register the FormServiceProvider
 *******************************************************************************/
$app->register(new FormServiceProvider(), array(
    'form.secret' => '1234567890ABCDEFGHIJKLMNOPQRSTUVWXYZ'
));

/*******************************************************************************
 * register the ServiceControllerServiceProvider
 *******************************************************************************/
$app->register(new ServiceControllerServiceProvider());

/*******************************************************************************
 * register the UrlGeneratorServiceProvider
 *******************************************************************************/
$app->register(new UrlGeneratorServiceProvider());

/*******************************************************************************
 * register the MonologServiceProvider
 *******************************************************************************/
$app->register(new MonologServiceProvider(), array(
    'monolog.logfile'  => __DIR__ . '/../resources/logs/app.log',
    'monolog.logname'  => $app,
    'monolog.loglevel' => LogLevel::INFO
));

/*******************************************************************************
 * register the AsseticServiceProvider
 *******************************************************************************/
$app->register(new AsseticServiceProvider(), array(
    'assetic.path_to_web' => __DIR__ . '/../web/assets',
    'assetic.options' => array(
        'debug' => $app['debug'],
        'auto_dump_assets' => $app['debug'],
    )
));

$app['assetic.filtermanager'] = $app->share(
    $app->extend('assetic.filter_manager', function ($filterManager, $app) {
        $filterManager->set('sass', new SassFilter('/usr/local/bin/sass'));
        return $filterManager;
    })
);

$app['assetic.asset_manager'] = $app->share(
    $app->extend('assetic.asset_manager', function ($assetManager, $app) {
        $assetManager->set(
            'styles',
            new AssetCache(
                new GlobAsset(
                    __DIR__ . '/../resources/assets/sass/styles.scss',
                   array($app['assetic.filter_manager']->get('sass'))
                ),
                new FilesystemCache(__DIR__ . '/../resources/cache/assetic')
            )
        );
        $assetManager->get('styles')->setTargetPath('css/styles.css');

        return $assetManager;
    })
);

/*******************************************************************************
 * register the SecurityServiceProvider
 *******************************************************************************/
$app->register(new SecurityServiceProvider(), array(
    'security.firewalls' => array(
        'admin' => array(
            'pattern' => '^/admin',
            'form' => array(
                'login_path' => '/login',
                'check_path' => '/admin/login_check',
            ),
            'logout' => array(
                'logout_path' => '/admin/logout'
            ),
            'users' => $app->share(function() use ($app) {
                return new UserProvider($app['elasticsearch']);
            }),
        ),
    ),
));

/*******************************************************************************
 * IndexController
 *******************************************************************************/
$app['index.controller'] = $app->share(function() use ($app) {
    $twigEnvironment = $app['twig'];
    $indexController = new IndexController($twigEnvironment);
    return $indexController;
});

/*******************************************************************************
 * AboutController
 *******************************************************************************/
$app['about.controller'] = $app->share(function() use ($app) {
    $twigEnvironment = $app['twig'];
    $aboutController = new AboutController($twigEnvironment);
    return $aboutController;
});

/*******************************************************************************
 * ContactController
 *******************************************************************************/
$app['contact.controller'] = $app->share(function() use ($app) {
    $twigEnvironment = $app['twig'];
    $contactController = new ContactController($twigEnvironment);
    return $contactController;
});

/*******************************************************************************
 * LoginController
 *******************************************************************************/
$app['login.controller'] = $app->share(function() use ($app) {
    $twigEnvironment = $app['twig'];
    $formFactory     = $app['form.factory'];
    $urlGenerator    = $app['url_generator'];
    $loginController = new LoginController($twigEnvironment, $formFactory, $urlGenerator);
    return $loginController;
});

/*******************************************************************************
 * AdminController
 *******************************************************************************/
$app['admin.controller'] = $app->share(function() use ($app) {
    $twigEnvironment = $app['twig'];
    $adminController = new AdminController($twigEnvironment);
    return $adminController;
});

// register the '/' route
$app->get('/', 'index.controller:indexAction')->bind('index_index');

// register the '/footer' route
$app->get('/footer', 'index.controller:footerAction')->bind('index_footer');

// register the '/header' route
$app->get('/header', 'index.controller:headerAction')->bind('index_header');

// '/about' route
$app->get('/about', 'about.controller:indexAction')->bind('about_index');

// '/contact' route
$app->get('/contact', 'contact.controller:indexAction')->bind('contact_index');

// register the '/login' route
$app->get('/login', 'login.controller:loginAction')->bind('login_login');

// register the '/admin' route
$app->get('/admin', 'admin.controller:indexAction')->bind('admin_index');


//$app->get('/setup', function() use ($app) {
//
//    // create an index
////    $index = array(
////        'index' => 'monkeyphp',
////        'body' => array(
////            'settings' => array(
////                'number_of_shards' => 1,
////                'number_of_replicas' => 1
////            )
////        )
////    );
////
////    $a = $app['elasticsearch']->indices()->create($index);
//
//    $params = array(
//        'index' => 'monkeyphp',
//        'type' => 'user',
//        'body' => array(
//            'username'              => 'monkeyphp',
//            'password'              => $app['security.encoder.digest']->encodePassword('vampcat81', ''),
//            'roles'                 => array('ROLE_ADMIN'),
//            'enabled'               => true,
//            'userNonExpired'        => true,
//            'credentialsNonExpired' => true,
//            'userNonLocked'         => true
//        ),
//    );
//
//    //$b = $app['elasticsearch']->index($params);
//
//    var_dump($b);
//});

/*******************************************************************************
 * Run the application
 *******************************************************************************/
if (APPLICATION_ENV !== 'production') {
    $app->run();
} elseif (isset($app['http_cache'])) {
    $app['http_cache']->run();
} else {
    $app->run();
}