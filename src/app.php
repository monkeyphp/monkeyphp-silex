<?php
/*******************************************************************************
 *     __  ___            __                    __                              
 *    /  |/  /___  ____  / /_____  __  ______  / /_  ____   _________  ____ ___ 
 *   / /|_/ / __ \/ __ \/ //_/ _ \/ / / / __ \/ __ \/ __ \ / ___/ __ \/ __ `__ \
 *  / /  / / /_/ / / / / ,< /  __/ /_/ / /_/ / / / / /_/ // /__/ /_/ / / / / / /
 * /_/  /_/\____/_/ /_/_/|_|\___/\__, / .___/_/ /_/ .___(_)___/\____/_/ /_/ /_/ 
 *                              /____/_/         /_/                            
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
use Assetic\Asset\AssetCollection;
use Assetic\Asset\GlobAsset;
use Assetic\Cache\FilesystemCache;
use Assetic\Filter\Sass\SassFilter;
use Monkeyphp\Controller\AboutController;
use Monkeyphp\Controller\Admin\AboutController as AdminAboutController;
use Monkeyphp\Controller\Admin\ArticleController as AdminArticleController;
use Monkeyphp\Controller\Admin\MessageController as AdminMessageController;
use Monkeyphp\Controller\AdminController;
use Monkeyphp\Controller\ArticleController;
use Monkeyphp\Controller\IndexController;
use Monkeyphp\Controller\LoginController;
use Monkeyphp\Controller\MessageController;
use Monkeyphp\Provider\ElasticSearchServiceProvider;
use Monkeyphp\Twig\Extension;
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
 *     ___                ___            __  _           
 *    /   |  ____  ____  / (_)________ _/ /_(_)___  ____ 
 *   / /| | / __ \/ __ \/ / / ___/ __ `/ __/ / __ \/ __ \
 *  / ___ |/ /_/ / /_/ / / / /__/ /_/ / /_/ / /_/ / / / /
 * /_/  |_/ .___/ .___/_/_/\___/\__,_/\__/_/\____/_/ /_/ 
 *       /_/   /_/                                       
 * 
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
 *    _____                 _               
 *   / ___/___  ______   __(_)_______  _____
 *   \__ \/ _ \/ ___/ | / / / ___/ _ \/ ___/
 *  ___/ /  __/ /   | |/ / / /__/  __(__  ) 
 * /____/\___/_/    |___/_/\___/\___/____/                                        
 * 
 *******************************************************************************/
/*******************************************************************************
 * register the SessionServiceProvider
 *******************************************************************************/
$app->register(new SessionServiceProvider());

/*******************************************************************************
 * register the ValidatorServiceProvider
 *******************************************************************************/
$app->register(new ValidatorServiceProvider());

/*******************************************************************************
 * Register the TwigServiceProvider
 * 
 * Add the Monkeyphp\Twig\Extension to the Twig_Environment object
 * @link http://twig.sensiolabs.org/doc/advanced.html#environment-aware-filters
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

$app['twig'] = $app->share(
    $app->extend('twig', function($twig, $app) {
        $twig->addExtension(new Extension());
        return $twig;
    })
);

/*******************************************************************************
 * register the TranslationServiceProvider
 *******************************************************************************/
$app->register(new TranslationServiceProvider(), array(
    'locale_fallbacks' => array('en'),
));

$app['translator'] = $app->share(
    $app->extend('translator', function($translator, $app) {
        $translator->addLoader('yaml', new YamlFileLoader());
        $translator->addResource('yaml', __DIR__ . '/../resources/locales/en.yml', 'en');
        $translator->addResource('yaml', __DIR__ . '/../resources/locales/de.yml', 'de');
        $translator->addResource('yaml', __DIR__ . '/../resources/locales/fr.yml', 'fr');
        return $translator;
    })
);

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
            new AssetCollection(
                array(
                    new Assetic\Asset\FileAsset(__DIR__ . '/../resources/assets/css/normalize.css'),
                    new AssetCache(
                        new GlobAsset(
                           array(
                               __DIR__ . '/../resources/assets/sass/styles.scss'
                            ),
                           array($app['assetic.filter_manager']->get('sass'))
                        ),
                        new FilesystemCache(__DIR__ . '/../resources/cache/assetic')
                    )
                )
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
 *    ______            __             ____              
 *   / ____/___  ____  / /__________  / / /__  __________
 *  / /   / __ \/ __ \/ __/ ___/ __ \/ / / _ \/ ___/ ___/
 * / /___/ /_/ / / / / /_/ /  / /_/ / / /  __/ /  (__  ) 
 * \____/\____/_/ /_/\__/_/   \____/_/_/\___/_/  /____/  
 *                                                  
 *******************************************************************************/
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
    $elasticsearch   = $app['elasticsearch'];
    $aboutController = new AboutController($twigEnvironment, $elasticsearch);
    return $aboutController;
});

/*******************************************************************************
 * ArticleController
 *******************************************************************************/
$app['article.controller'] = $app->share(function() use ($app) {
    $articleController = new ArticleController();
    return $articleController;
});

/*******************************************************************************
 * MessageController
 *******************************************************************************/
$app['message.controller'] = $app->share(function() use ($app) {
    $twigEnvironment = $app['twig'];
    $formFactory     = $app['form.factory'];
    $urlGenerator    = $app['url_generator'];
    $session         = $app['session'];
    $elasticsearch   = $app['elasticsearch'];
    $contactController = new MessageController(
        $twigEnvironment, 
        $formFactory, 
        $urlGenerator, 
        $session,    
        $elasticsearch);
    return $contactController;
});

/*******************************************************************************
 * LoginController
 *******************************************************************************/
$app['login.controller'] = $app->share(function() use ($app) {
    $twigEnvironment = $app['twig'];
    $formFactory     = $app['form.factory'];
    $urlGenerator    = $app['url_generator'];
    $loginController = new LoginController(
        $twigEnvironment,
        $formFactory,
        $urlGenerator
    );
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

/*******************************************************************************
 * Admin/ArticleController
 *******************************************************************************/
$app['admin.article.controller'] = $app->share(function() use ($app) {
    $twigEnvironment     = $app['twig'];
    $formFactory         = $app['form.factory'];
    $urlGenerator        = $app['url_generator'];
    $elasticsearchClient = $app['elasticsearch'];
    $adminArticleController = new AdminArticleController(
        $twigEnvironment, 
        $formFactory,
        $urlGenerator,
        $elasticsearchClient
    );
    return $adminArticleController;
});

/*******************************************************************************
 * Admin/AboutController
 *******************************************************************************/
$app['admin.about.controller'] = $app->share(function() use ($app) {
    $twigEnvironment     = $app['twig'];
    $formFactory         = $app['form.factory'];
    $urlGenerator        = $app['url_generator'];
    $elasticsearchClient = $app['elasticsearch'];
    $adminAboutController = new AdminAboutController(
        $twigEnvironment,
        $formFactory,
        $urlGenerator,
        $elasticsearchClient
    );
    return $adminAboutController;
});

/*******************************************************************************
 * Admin/MessageController
 *******************************************************************************/
$app['admin.message.controller'] = $app->share(function() use ($app) {
    $twigEnvironment     = $app['twig'];
    $formFactory         = $app['form.factory'];
    $urlGenerator        = $app['url_generator'];
    $elasticsearchClient = $app['elasticsearch'];
    $adminMessageController = new AdminMessageController(
        $twigEnvironment,
        $formFactory,
        $urlGenerator,
        $elasticsearchClient
    );
    return $adminMessageController;
});


/*******************************************************************************
 *     ____              __           
 *    / __ \____  __  __/ /____  _____
 *   / /_/ / __ \/ / / / __/ _ \/ ___/
 *  / _, _/ /_/ / /_/ / /_/  __(__  ) 
 * /_/ |_|\____/\__,_/\__/\___/____/                                  
 * 
 *******************************************************************************/
$app->match('/', 'index.controller:indexAction')
    ->method('GET')
    ->bind('index_index');

$app->match('/footer', 'index.controller:footerAction')
    ->method('GET')
    ->bind('index_footer');

$app->match('/header', 'index.controller:headerAction')
    ->method('GET')
    ->bind('index_header');

$app->match('/about', 'about.controller:indexAction')
    ->method('GET')
    ->bind('about_index');

$app->match('/article', 'article.controller:indexAction')->method('GET')->bind('article_index');

$app->match('/message', 'message.controller:indexAction')->method('GET|POST')->bind('message_index');

$app->match('/message/thankyou', 'message.controller:thankyouAction')->method('GET')->bind('message_thankyou');

$app->match('/login', 'login.controller:loginAction')->method('GET')->bind('login_login');

/*******************************************************************************
 * Admin routes
 *******************************************************************************/
$app->match('/admin', 'admin.controller:indexAction')
    ->method('GET')
    ->bind('admin_index');

$app->match('/admin/about', 'admin.about.controller:indexAction')
    ->method('GET')
    ->bind('admin_about_index');

$app->match('/admin/article', 'admin.article.controller:indexAction')
    ->method('GET')
    ->bind('admin_article_index');

$app->match('/admin/article/create', 'admin.article.controller:createAction')
    ->method('GET|POST')
    ->bind('admin_article_create');

$app->match('/admin/article/read/{id}', 'admin.article.controller:readAction')
    ->method('GET')
    ->bind('admin_article_read');

$app->match('/admin/message', 'admin.message.controller:indexAction')
    ->method('GET')
    ->bind('admin_message_index');

$app->match('/admin/message/delete/{id}', 'admin.message.controller:deleteAction')
    ->method('GET|POST')
    ->bind('admin_message_delete');




/*******************************************************************************
 * Setup routes
 *******************************************************************************/
$app->get('/setup', function() use ($app) {

    // create an index
//    $index = array(
//        'index' => 'monkeyphp',
//        'body' => array(
//            'settings' => array(
//                'number_of_shards' => 1,
//                'number_of_replicas' => 1
//            )
//        )
//    );
//
//    $a = $app['elasticsearch']->indices()->create($index);

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
//    $b = $app['elasticsearch']->index($params);
    
//    $params = array(
//        'index' => 'monkeyphp',
//        'type' => 'about',
//        'body' => array(
//            'created' => new \DateTime(),
//            'modified' => new \DateTime(),
//            'body' => '__This__ is *the* about me content'
//        )
//    );
//    $c = $app['elasticsearch']->index($params);
//    var_dump($c);
});

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