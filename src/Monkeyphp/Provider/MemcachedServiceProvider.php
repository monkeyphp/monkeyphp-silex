<?php
/**
 * MemcachedServiceProvider.php
 */
namespace Monkeyphp\Provider;

use Memcached;
use Pimple;
use Silex\Application;
use Silex\ServiceProviderInterface;
/**
 * Description of MemcachedServiceProvider
 *
 * @author David White <david@monkeyphp.com>
 */
class MemcachedServiceProvider implements ServiceProviderInterface
{
    /**
     * Register the provider
     * 
     * @param Application $app
     * 
     * return null
     */
    public function register(Application $app)
    {
        /**
         * Default configuration options 
         * 
         * @var array
         */
        $app['memcacheds.default_options'] = array(
            'persistent_id' => 'default', 
            'servers' => array(
                array(
                    'host'   => '127.0.0.1', 
                    'port'   => 11211,
                    'weight' => 0
                )
            )
        );
        
        /**
         * Initialise the config values for each Memcached instance
         * 
         * @return array
         */
        $app['memcacheds.options.initializer'] = $app->protect(function() use ($app) {
            static $initialized = false;
            if ($initialized) {
                return;
            }
            $initialized = true;
            
            if (! isset($app['memcacheds.options'])) {
                $app['memcacheds.options'] = array(
                    'default' => isset($app['memcacheds.options']) ? $app['memcacheds.options'] : array()
                );
            }
            
            $tmp = $app['memcacheds.options'];
            
            foreach ($tmp as $name => &$options) {
                $options = array_replace_recursive(
                    $app['memcacheds.default_options'],
                    $options
                );
                
                if (! isset($app['memcacheds.default'])) {
                    $app['memcacheds.default'] = $name;
                }
            }
            $app['memcacheds.options'] = $tmp;
        });
        
        /**
         * Return an array of Memcached instances
         * 
         * @param Application
         * 
         * @return array
         */
        $app['memcacheds'] = $app->share(function($app) {
            $app['memcacheds.options.initializer']();
            
            $memcacheds = new Pimple();
            
            foreach ($app['memcacheds.options'] as $name => $options) {
                
                $memcacheds[$name] = $app->share(function() use ($options) {
                    
                    extract($options);
                    
                    $memcached = new Memcached($persistent_id);
                    $memcached->addServers($servers);
                    
                    return $memcached;
                });
            }
            
            return $memcacheds;
        });
        
        /**
         * Return the default instance of Memcached
         * 
         * @param Application $app
         * 
         * @return Memcached
         */
        $app['memcached'] = $app->share(function($app) {
            $memcacheds = $app['memcacheds'];
            return $memcacheds[$app['memcacheds.default']];
        });
    }

    public function boot(Application $app)
    {
        
    }
}
