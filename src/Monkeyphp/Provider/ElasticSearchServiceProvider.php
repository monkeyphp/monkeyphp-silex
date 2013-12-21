<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
namespace Monkeyphp\Provider;

use Elasticsearch\Client;
use Pimple;
use Psr\Log\LogLevel;
use Silex\Application;
use Silex\ServiceProviderInterface;

/**
 * Description of ElasticSearchServiceProvider
 *
 * @author David White <david@monkeyphp.com>
 */
class ElasticSearchServiceProvider implements ServiceProviderInterface
{
    public function register(Application $app)
    {
        // default options
        $app['elasticsearch.default_options'] = array(
            'hosts' => array(
                '127.0.0.1:9200'
            ),
            'connection_class'         => '\Elasticsearch\Connections\GuzzleConnection',
            'connection_factory_class' => '\Elasticsearch\Connections\ConnectionFactory',
            'connection_pool_class'    => '\Elasticsearch\ConnectionPool\StaticConnectionPool',
            'selector_class'           => '\Elasticsearch\ConnectionPool\Selectors\RoundRobinSelector',
            'serializer_class'         => '\Elasticsearch\Serializers\ArrayToJSONSerializer',
            'sniff_on_start'           => '', 
            'connection_params'        => array(), 
            'logging'                  => false, 
            'log_object'               => null, 
            'log_path'                 => 'elasticsearch.log', 
            'log_level'                => LogLevel::WARNING,
            'trace_object'             => null, 
            'trace_path'               => 'elasticsearch.log',
            'trace_level'              => LogLevel::WARNING,
            'guzzle_options'           => array(), 
            'connection_pool_params'   => array(
                'randomize_hosts' => true
            ),
        );
        
        /**
         * 
         */
        $app['elasticsearches.options.initializer'] = $app->protect(function() use($app) {
            static $initialized = false;
            if ($initialized) {
                return;
            }
            $initialized = true;
            
            if (! isset($app['elasticsearches.options'])) {
                $app['elasticsearches.options'] = array(
                    'default' => isset($app['elasticsearch.options']) ? $app['elasticsearch.options'] : array()
                );
            }
            
            $tmp = $app['elasticsearches.options'];
            foreach ($tmp as $name => &$options) {
                $options = array_replace($app['elasticsearch.default_options'], $options);
                if (! isset($app['elasticsearches.default'])) {
                    $app['elasticsearches.default'] = $name;
                }
            }
            $app['elasticsearches.options'] = $tmp;
        });
        
        /**
         * Return an array of ElasticSearch\Client instances
         * 
         * @return array
         */
        $app['elasticsearches'] = $app->share(function($app) {
            $app['elasticsearches.options.initializer']();
            
            $elasticsearches = new Pimple();
            
            foreach ($app['elasticsearches.options'] as $name => $options) {
                $elasticsearches[$name] = $app->share(function($elasticsearches) use ($options) {
                    $params = array(
                        'hosts'                  => $options['hosts'],
                        'connectionClass'        => $options['connection_class'],
                        'connectionFactoryClass' => $options['connection_factory_class'],
                        'connectionPoolClass'    => $options['connection_pool_class'],
                        'selectorClass'          => $options['selector_class'],
                        'serializerClass'        => $options['serializer_class'],
                        'sniffOnStart'           => $options['sniff_on_start'],
                        'connectionParams'       => $options['connection_params'],
                        'logging'                => $options['logging'],
                        'logObject'              => $options['log_object'],
                        'logPath'                => $options['log_path'],
                        'logLevel'               => $options['log_level'],
                        'traceObject'            => $options['trace_object'],
                        'tracePath'              => $options['trace_path'],
                        'traceLevel'             => $options['trace_level'],
                        'guzzleOptions'          => $options['guzzle_options'],
                        'connectionPoolParams'   => array(
                            'randomizeHosts' => $options['connection_pool_params']['randomize_hosts']
                        )
                    );
                    return new Client($params);
                });
            }
            
            return $elasticsearches;
        });
        
        /**
         * Return the default client instance
         */
        $app['elasticsearch'] = $app->share( function() use($app) {
            $elasticsearches = $app['elasticsearches'];
            return $elasticsearches[$app['elasticsearches.default']];
        });
        
    }

    public function boot(Application $app)
    {
        
    }
}
