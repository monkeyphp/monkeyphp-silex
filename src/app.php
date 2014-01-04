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
 * 
 * Cool stuff:
 * http://www.zombieipsum.com/
 *******************************************************************************/
require_once '../vendor/autoload.php';

use Assetic\Asset\AssetCache;
use Assetic\Asset\AssetCollection;
use Assetic\Asset\FileAsset;
use Assetic\Asset\GlobAsset;
use Assetic\Cache\FilesystemCache;
use Assetic\Filter\Sass\SassFilter;
use Monkeyphp\Controller\AboutController;
use Monkeyphp\Controller\Admin\AboutController as AdminAboutController;
use Monkeyphp\Controller\Admin\ArticleController as AdminArticleController;
use Monkeyphp\Controller\Admin\MessageController as AdminMessageController;
use Monkeyphp\Controller\AdminController;
use Monkeyphp\Controller\ArticleController;
use Monkeyphp\Controller\CommentController;
use Monkeyphp\Controller\IndexController;
use Monkeyphp\Controller\LoginController;
use Monkeyphp\Controller\MessageController;
use Monkeyphp\Provider\ElasticSearchServiceProvider;
use Monkeyphp\Provider\MemcachedServiceProvider;
use Monkeyphp\Repository\ArticleRepository;
use Monkeyphp\Repository\CommentRepository;
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
 * register the MemcachedServiceProvider
 *******************************************************************************/
$app->register(new MemcachedServiceProvider());

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
                    new FileAsset(__DIR__ . '/../resources/assets/css/normalize.css'),
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
 *     ____                        _ __             _          
 *    / __ \___  ____  ____  _____(_) /_____  _____(_)__  _____
 *   / /_/ / _ \/ __ \/ __ \/ ___/ / __/ __ \/ ___/ / _ \/ ___/
 *  / _, _/  __/ /_/ / /_/ (__  ) / /_/ /_/ / /  / /  __(__  ) 
 * /_/ |_|\___/ .___/\____/____/_/\__/\____/_/  /_/\___/____/  
 *           /_/                                               
 *******************************************************************************/
/*******************************************************************************
 * ArticleRepository
 *******************************************************************************/
$app['article.repository'] = $app->share(function() use ($app) {
    $elasticsearchClient = $app['elasticsearch'];
    $memcached           = $app['memcached'];
    $articleRepository = new ArticleRepository($elasticsearchClient, $memcached);
    return $articleRepository;
});

/*******************************************************************************
 * CommentRepository
 *******************************************************************************/
$app['comment.repository'] = $app->share(function() use ($app) {
    $elasticsearchClient = $app['elasticsearch'];
    $memcached           = $app['memcached'];
    $commentRepository = new CommentRepository($elasticsearchClient, $memcached);
    return $commentRepository;
});

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
    $twigEnvironment     = $app['twig'];
    $formFactory         = $app['form.factory'];
    $urlGenerator        = $app['url_generator'];
    $articleRepository   = $app['article.repository'];
    $articleController = new ArticleController(
        $twigEnvironment,
        $formFactory,
        $urlGenerator,
        $articleRepository
    );
    return $articleController;
});
/*******************************************************************************
 * CommentController
 *******************************************************************************/
$app['comment.controller'] = $app->share(function() use ($app) {
    $twigEnvironment     = $app['twig'];
    $formFactory         = $app['form.factory'];
    $urlGenerator        = $app['url_generator'];
    $commentRepository   = $app['comment.repository'];
    $commentController = new CommentController(
        $twigEnvironment,
        $formFactory,
        $urlGenerator,
        $commentRepository
    );
    return $commentController;
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

$app->match('/article', 'article.controller:indexAction')
    ->method('GET')
    ->bind('article_index');

$app->match('/article/{slug}', 'article.controller:readAction')
    ->method('GET')
    ->bind('article_read');

$app->match('/article/comments/{id}', 'comment.controller:indexAction')
    ->method('GET')
    ->bind('comment_index');

$app->match('/message', 'message.controller:indexAction')
    ->method('GET|POST')
    ->bind('message_index');

$app->match('/message/thankyou', 'message.controller:thankyouAction')
    ->method('GET')
    ->bind('message_thankyou');

$app->match('/login', 'login.controller:loginAction')
    ->method('GET')
    ->bind('login_login');

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
 * Setup routes - this needs moving into a seperate app/console
 *******************************************************************************/
$app->get('/setup', function() use ($app) {

    // create an index
    // http://www.elasticsearch.org/guide/en/elasticsearch/reference/current/mapping-core-types.html
    $params = array(
        'index' => 'monkeyphp', 
        'body' => array(
            
            // mappings
            'mappings' => array (
                
                // user
                'user' => array (
                    'properties' => array(
                        'username' => array(
                            'type' => 'string',
                            'index' => 'not_analyzed'
                        ), 
                        'password' => array(
                            'type' => 'string',
                            'index' => 'not_analyzed'
                        ),
                        'roles' => array(
                            'type'       => 'string', 
                            'index_name' => 'role'
                        ),
                        'enabled' => array(
                            'type' => 'boolean',
                            'null_value' => false
                        ),
                        'userNonExpired' => array(
                            'type' => 'boolean',
                            'null_value' => false
                        ),
                        'credentialsNonExpired' => array(
                            'type' => 'boolean',
                            'null_value' => false
                        ),
                        'userNonLocked' => array(
                            'type' => 'boolean',
                            'null_value' => false
                        ),
                    ),
                ),
                
                // about
                'about' => array(
                    'properties' => array(
                        'body' => array(
                            'type' => 'string'
                        ),
                        'created' => array(
                            'type' => 'date',
                            'format' => 'yyyy/MM/dd HH:mm:ss'
                        ), 
                        'modified' => array(
                            'type' => 'date',
                            'format' => 'yyyy/MM/dd HH:mm:ss'
                        ),
                    ),
                ),
                
                // category
                'category' => array(
                    'properties' => array(
                        'name' => array(
                            'type' => 'string', 
                            'store' => 'no', 
                            'index' => 'not_analyzed'
                        ),
                        'created' => array(
                            'type' => 'date',
                            'format' => 'yyyy/MM/dd HH:mm:ss'
                        ), 
                        'modified' => array(
                            'type' => 'date',
                            'format' => 'yyyy/MM/dd HH:mm:ss'
                        ),
                    ),
                ),
                
                // article
                'article' => array(
                    '_parent' => array(
                        'type' => 'category'
                    ),
                    'properties' => array(
                        'title' => array(
                            'type'  => 'string',
                            'store' => 'no'
                        ),
                        'tags' => array(
                            'type' => 'string', 
                        ), 
                        'summary' => array(
                            'type' => 'string',
                            'store' => 'no',
                            'index' => 'analyzed'
                        ), 
                        'body' => array(
                            'type'  => 'string',
                            'store' => 'no',
                            'index' => 'analyzed'
                        ), 
                        'published' => array(
                            'type'       => 'boolean', 
                            'null_value' => false
                        ), 
                        'slug' => array(
                            "type"           => "string",
                            "include_in_all" => false,
                            "index"          => "not_analyzed",
                            "store"          => "no"
                        ),
                        'created' => array(
                            'type' => 'date',
                            'format' => 'yyyy/MM/dd HH:mm:ss'
                        ), 
                        'modified' => array(
                            'type' => 'date',
                            'format' => 'yyyy/MM/dd HH:mm:ss'
                        ),
                    ),
                ),
                
                // comment
                'comment' => array(
                    '_parent' => array(
                        'type' => 'article'
                    ),
                    'properties' => array(
                        'email' => array(
                            'type' => 'string',
                            'index' => 'not_analyzed'
                        ),
                        'body' => array(
                            'type' => 'string'
                        ),
                        'ip' => array(
                            'type' => 'ip'
                        ),
                        'created' => array(
                            'type' => 'date',
                            'format' => 'yyyy/MM/dd HH:mm:ss'
                        ), 
                        'modified' => array(
                            'type' => 'date',
                            'format' => 'yyyy/MM/dd HH:mm:ss'
                        ),
                        'published' => array(
                            'type'       => 'boolean', 
                            'null_value' => false
                        ),
                    ),
                ),
                
            )
        )
    );
    
    $result = $app['elasticsearch']->indices()->create($params);
    
    var_dump($result);
    
    
    
    // categories
    $categories = array(
        'Technology'              => null,
        'Development'             => null,
        'Life &amp; Other Things' => null,
        'Zombies'                 => null,
        'Science Fiction'         => null
    );
    
    foreach ($categories as $category => $id) {
        $params = array(
            'index' => 'monkeyphp',
            'type' => 'category', 
            'body' => array(
                'name' => $category,
                'created'  => date('Y/m/d H:i:s'),
                'modified' => date('Y/m/d H:i:s'), 
            )
        );
        $result = $app['elasticsearch']->index($params);
        $categories[$category] = $result['_id'];
    }
    
    
    
    // articles
    $articles = array(
        array(
            'parent'    => $categories['Technology'],
            'title'     => 'This is a test article about Php',
            'summary'   => 'Lorem ipsum dolor sit amet, consectetur adipiscing elit. Vestibulum vel volutpat purus. Nulla sed accumsan sapien. Suspendisse aliquam metus ac libero iaculis, sit amet ornare elit consectetur. Fusce rhoncus pretium est et ullamcorper. In eget gravida enim. Aenean fringilla augue id justo tincidunt dictum. Curabitur et nibh ut sem malesuada gravida luctus quis tortor. Mauris ipsum nibh, euismod in lectus at, aliquet vestibulum libero.',
            'body'      => 'Lorem ipsum dolor sit amet, consectetur adipiscing elit. Vestibulum vel volutpat purus. Nulla sed accumsan sapien. Suspendisse aliquam metus ac libero iaculis, sit amet ornare elit consectetur. Fusce rhoncus pretium est et ullamcorper. In eget gravida enim. Aenean fringilla augue id justo tincidunt dictum. Curabitur et nibh ut sem malesuada gravida luctus quis tortor. Mauris ipsum nibh, euismod in lectus at, aliquet vestibulum libero.

Cras condimentum magna in nibh varius eleifend. Mauris interdum risus nec rhoncus sodales. In mattis commodo est, quis ultricies justo sagittis eget. Praesent mattis, odio sit amet scelerisque semper, purus risus eleifend tellus, a tempus risus lorem sagittis tellus. Sed in nibh vitae purus tincidunt tempor. Sed quis purus id erat elementum molestie. Aenean elementum elit sed sapien ullamcorper laoreet. Maecenas molestie, quam id rhoncus suscipit, nulla diam dapibus sem, dapibus dictum orci nibh vitae nunc. Phasellus non libero ullamcorper, volutpat lorem vel, rhoncus risus. Vestibulum quis lobortis mi. Mauris sit amet lacus sit amet nulla laoreet mollis. Curabitur non scelerisque diam. Curabitur eu justo feugiat, scelerisque nisl sed, vulputate erat. Vivamus ullamcorper massa lobortis orci eleifend, a pellentesque erat aliquam. Nulla facilisi.

Aenean pharetra, libero sit amet pretium porta, leo augue viverra lectus, feugiat gravida mi nunc eget quam. Aenean non magna placerat, gravida sem vel, tempus metus. Vestibulum iaculis interdum dui, et ornare nibh tincidunt in. Suspendisse sodales vulputate nulla, dapibus rutrum felis aliquet et. Nulla fringilla massa ut aliquam viverra. In tincidunt mauris nec vestibulum suscipit. Integer egestas mauris aliquam mauris malesuada, et laoreet purus porta. Sed volutpat volutpat velit, in molestie nisl cursus vitae. Sed suscipit id dolor et viverra. Phasellus a porttitor purus. Lorem ipsum dolor sit amet, consectetur adipiscing elit.

Fusce vestibulum mi in blandit egestas. Sed consequat, arcu sit amet accumsan scelerisque, metus velit bibendum erat, quis aliquam turpis quam a nulla. Proin molestie sapien in libero vestibulum, eget interdum sapien lacinia. Donec interdum leo ut dolor iaculis malesuada. Cras at arcu ac lectus faucibus viverra. Ut dignissim quam id vulputate pulvinar. Quisque non tellus leo. Duis ut mi id mauris sollicitudin cursus.

Mauris auctor justo quis adipiscing viverra. Nulla facilisi. Maecenas convallis justo ultrices tempus tincidunt. Nunc ultrices, tortor non viverra tristique, turpis lacus vestibulum velit, eu consequat odio odio eget purus. Quisque hendrerit gravida enim, id ultricies orci adipiscing volutpat. Pellentesque habitant morbi tristique senectus et netus et malesuada fames ac turpis egestas. Aliquam sit amet tellus in mauris vulputate imperdiet non eget elit. Proin quis urna eleifend, elementum purus sit amet, elementum metus. Sed suscipit erat ut vulputate pulvinar. Sed eget lorem eros. Nulla eu ligula ligula. Vivamus accumsan lorem eget nunc fringilla sollicitudin. Donec mattis imperdiet ante.',
            'published' => true,
            'slug'      => 'something-about-php',
            'tags'      => array('PHP', 'Elasticsearch'),
            'created'  => date('Y/m/d H:i:s'),
            'modified' => date('Y/m/d H:i:s'),
            
            'comments' => array(
                array(
                    'email'    => 'home@example.com',
                    'ip'       => '192.168.1.1', 
                    'body'     => 'This is a really interesting article. I really like it!', 
                    'created'  => date('Y/m/d H:i:s'),
                    'modified' => date('Y/m/d H:i:s'),
                ),
                array(
                    'email'    => 'test@example.com',
                    'ip'       => '192.168.1.2', 
                    'body'     => 'This article is total rubbish. You haven\'t got a clue what you are talking about' , 
                    'created'  => date('Y/m/d H:i:s'),
                    'modified' => date('Y/m/d H:i:s'),
                ),
                array(
                    'email'    => 'bob@example.com',
                    'ip'       => '192.168.1.99',
                    'body'     => 'This is mostly awesome',
                    'created'  => date('Y/m/d H:i:s'),
                    'modified' => date('Y/m/d H:i:s'),
                ),
            )
        ),
        
        array(
            'parent'    => $categories['Zombies'],
            'title'     => 'All About Zombies',
            'summary'   => 'Zombies: Lorem ipsum dolor sit amet, consectetur adipiscing elit. Vestibulum vel volutpat purus. Nulla sed accumsan sapien. Suspendisse aliquam metus ac libero iaculis, sit amet ornare elit consectetur. Fusce rhoncus pretium est et ullamcorper. In eget gravida enim. Aenean fringilla augue id justo tincidunt dictum. Curabitur et nibh ut sem malesuada gravida luctus quis tortor. Mauris ipsum nibh, euismod in lectus at, aliquet vestibulum libero.',
            'body'      => 'Zombie ipsum reversus ab viral inferno, nam rick grimes malum cerebro. De carne lumbering animata corpora quaeritis. Summus brains sit​​, morbo vel maleficia? De apocalypsi gorger omero undead survivor dictum mauris. Hi mindless mortuis soulless creaturas, imo evil stalking monstra adventus resi dentevil vultus comedat cerebella viventium. Qui animated corpse, cricket bat max brucks terribilem incessu zomby. The voodoo sacerdos flesh eater, suscitat mortuos comedere carnem virus. Zonbi tattered for solum oculi eorum defunctis go lum cerebro. Nescio brains an Undead zombies. Sicut malus putrid voodoo horror. Nigh tofth eliv ingdead.

Cum horribilem walking dead resurgere de crazed sepulcris creaturis, zombie sicut de grave feeding iride et serpens. Pestilentia, shaun ofthe dead scythe animated corpses ipsa screams. Pestilentia est plague haec decaying ambulabat mortuos. Sicut zeder apathetic malus voodoo. Aenean a dolor plan et terror soulless vulnerum contagium accedunt, mortui iam vivam unlife. Qui tardius moveri, brid eof reanimator sed in magna copia sint terribiles undeath legionis. Alii missing oculis aliorum sicut serpere crabs nostram. Putridi braindead odores kill and infect, aere implent left four dead.

Lucio fulci tremor est dark vivos magna. Expansis creepy arm yof darkness ulnis witchcraft missing carnem armis Kirkman Moore and Adlard caeruleum in locis. Romero morbo Congress amarus in auras. Nihil horum sagittis tincidunt, zombie slack-jawed gelida survival portenta. The unleashed virus est, et iam zombie mortui ambulabunt super terram. Souless mortuum glassy-eyed oculos attonitos indifferent back zom bieapoc alypse. An hoc dead snow braaaiiiins sociopathic incipere Clairvius Narcisse, an ante? Is bello mundi z?

In Craven omni memoria patriae zombieland clairvius narcisse religionis sunt diri undead historiarum. Golums, zombies unrelenting et Raimi fascinati beheading. Maleficia! Vel cemetery man a modern bursting eyeballs perhsaps morbi. A terrenti flesh contagium. Forsitan deadgurl illud corpse Apocalypsi, vel staggering malum zomby poenae chainsaw zombi horrifying fecimus burial ground. Indeflexus shotgun coup de poudre monstra per plateas currere. Fit de decay nostra carne undead. Poenitentiam violent zom biehig hway agite RE:dead pœnitentiam! Vivens mortua sunt apud nos night of the living dead.

Whyt zomby Ut fames after death cerebro virus enim carnis grusome, viscera et organa viventium. Sicut spargit virus ad impetum, qui supersumus flesh eating. Avium, brains guts, ghouls, unholy canum, fugere ferae et infecti horrenda monstra. Videmus twenty-eight deformis pale, horrenda daemonum. Panduntur brains portae rotting inferi. Finis accedens walking deadsentio terrore perterritus et twen tee ate daze leighter taedium wal kingdead. The horror, monstra epidemic significant finem. Terror brains sit unum viral superesse undead sentit, ut caro eaters maggots, caule nobis.

',
            'published' => true,
            'slug'      => 'zombies-brains-and-dev-life',
            'tags'      => array('Zombies', 'Dead'),
            'created'  => date('Y/m/d H:i:s'),
            'modified' => date('Y/m/d H:i:s'),
        ),
        
        
    );
    
    
    foreach ($articles as $article) {
        $params = array(
            'index' => 'monkeyphp',
            'type' => 'article',
            'parent' => $article['parent'],
            
            'body' => array(
                'title'     => $article['title'],
                'summary'   => $article['summary'],
                'body'      => $article['body'],
                'published' => true,
                'slug'      => $article['slug'],
                'tags'      => $article['tags'],
                'created'   => $article['created'],
                'modified'  => $article['modified'],
            )
        );
        
        $result = $app['elasticsearch']->index($params);
        $id = $result['_id'];
        
        if (isset($article['comments']) && is_array($article['comments'])) {
            
            foreach ($article['comments'] as $comment) {
                $params = array(
                    'index'  => 'monkeyphp',
                    'type'   => 'comment',
                    'parent' => $id,
                    'body' => array(
                        'email'    => $comment['email'],
                        'ip'       => $comment['ip'],
                        'body'     => $comment['body'],
                        'created'  => $comment['created'],
                        'modified' => $comment['modified'],
                    )
                );
                
                $result = $app['elasticsearch']->index($params);
                
                var_dump($result);
            }
        }
        
        var_dump($result);
    }
   
            
            
    
    // about
    $params = array(
        'index' => 'monkeyphp',
        'type' => 'about',
        'body' => array(
            'created'  => date('Y/m/d H:i:s'),
            'modified' => date('Y/m/d H:i:s'),
            'body' => 
            
            'I was having the most wonderful dream. Except you were there, and you were there, and you were there! And from now on you\'re all named Bender Jr. And I\'d do it again! And perhaps a third time! But that would be it. Shinier than yours, meatbag.

Bender, hurry! This fuel\'s expensive! Also, we\'re dying! You can see how I lived before I met you. Bender, we\'re trying our best. I guess because my parents keep telling me to be more ladylike. As though! Take me to your leader!

No, of course not. It was… uh… porno. Yeah, that\'s it. There, now he\'s trapped in a book I wrote: a crummy world of plot holes and spelling errors! It\'s okay, Bender. I like cooking too.

Whoa a real live robot; or is that some kind of cheesy New Year\'s costume? And why did \'I\' have to take a cab? For the last time, I don\'t like lilacs! Your \'first\' wife was the one who liked lilacs!

Calculon is gonna kill us and it\'s all everybody else\'s fault! So I really am important? How I feel when I\'m drunk is correct? I guess if you want children beaten, you have to do it yourself. I am the man with no name, Zapp Brannigan! That\'s right, baby. I ain\'t your loverboy Flexo, the guy you love so much. You even love anyone pretending to be him!'
        )
    );
    
    $result = $app['elasticsearch']->index($params);
    
    var_dump($result);
    
    die();
    
    
    
    
    
    
    /////
    
    
    
    $params = array(
        'index' => 'monkeyphp',
        'type' => 'user',
        'body' => array(
            'username'              => 'monkeyphp',
            'password'              => $app['security.encoder.digest']->encodePassword('vampcat81', ''),
            'roles'                 => array('ROLE_ADMIN'),
            'enabled'               => true,
            'userNonExpired'        => true,
            'credentialsNonExpired' => true,
            'userNonLocked'         => true
        ),
    );
    
    $app['elasticsearch']->index($params);
    
    
    
    

    $result = $app['elasticsearch']->index($params);

    $params = array(
        'index' => 'monkeyphp',
        'type' => 'article',
        'body' => array(
            'title' => 'This is a test article about Javascript',
            'category'  => 'Technology',
            'summary'   => '__Some__ content *about* javascript',
            'body'      => 'Lorem ipsom Javascript',
            'published' => true,
            'slug'      => 'something-about-javascript',
            'created'   => new DateTime(),
            'modified'  => new DateTime(),
        )
    );

    $result = $app['elasticsearch']->index($params);

    $params = array(
        'index' => 'monkeyphp',
        'type' => 'article',
        'body' => array(
            'title' => 'This is a test article about Elasticsearch',
            'category'  => 'Technology',
            'summary'   => '__Some__ content *about* elasticsearch',
            'body'      => 'Lorem ipsom Elasticsearch',
            'published' => false,
            'slug'      => 'something-about-elasticsearch',
            'created'   => new DateTime(),
            'modified'  => new DateTime(),
        )
    );

    $result = $app['elasticsearch']->index($params);
    
    
    var_dump($result);
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