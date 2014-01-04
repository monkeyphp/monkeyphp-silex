<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
namespace Monkeyphp\Repository;

use Elasticsearch\Client;
use Memcached;
/**
 * Description of AbstractRepository
 *
 * @author David White <david@monkeyphp.com>
 */
abstract class AbstractRepository
{
    /**
     * Instance of Elasticsearch\Client
     * 
     * @var Client
     */
    protected $elasticsearchClient;
    
    /**
     *
     * @var Memcached
     */
    protected $memcached;
    
    public function __construct(Client $elasticsearchClient, Memcached $memcached = null)
    {
        $this->setElasticsearchClient($elasticsearchClient);
        $this->setMemcached($memcached);
    }
    
    public function getElasticsearchClient()
    {
        return $this->elasticsearchClient;
    }

    public function setElasticsearchClient(Client $elasticsearchClient)
    {
        $this->elasticsearchClient = $elasticsearchClient;
        return $this;
    }
    
    public function getMemcached()
    {
        return $this->memcached;
    }

    public function setMemcached(Memcached $memcached = null)
    {
        $this->memcached = $memcached;
        return $this;
    }
}
