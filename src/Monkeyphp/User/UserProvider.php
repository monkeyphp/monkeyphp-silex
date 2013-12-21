<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
namespace Monkeyphp\User;

use Elasticsearch\Client;
use Symfony\Component\Security\Core\Exception\UnsupportedUserException;
use Symfony\Component\Security\Core\User\User;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\UserProviderInterface;

/**
 * Description of UserProvider
 *
 * @author David White <david@monkeyphp.com>
 */
class UserProvider implements UserProviderInterface
{   
    
    /**
     * Instance of Elasticsearch Client
     * 
     * @var Client
     */
    protected $elasticsearchClient;
    
    /**
     * Constructor
     * 
     * @param \Elasticsearch\Client $elasticsearchClient
     * 
     * @return void
     */
    public function __construct(Client $elasticsearchClient)
    {
        $this->setElasticsearchClient($elasticsearchClient);
    }
    
    public function getElasticsearchClient()
    {
        return $this->elasticsearchClient;
    }

    public function setElasticsearchClient($elasticsearchClient)
    {
        $this->elasticsearchClient = $elasticsearchClient;
        return $this;
    }

        
    public function loadUserByUsername($username)
    {
        
    }
    
    /**
     * Refresh a User instance
     * 
     * @param UserInterface $user The User instance
     * 
     * @throws UnsupportedUserException
     * @return UserInterface
     */
    public function refreshUser(UserInterface $user)
    {
        if (! $user instanceof User) {
            throw new UnsupportedUserException(sprintf('Instances of %s are not supported', get_class($user)));
        }
        return $this->loadUserByUsername($user->getUsername());
    }

    /**
     * Return a boolean indicating that this class supports the supplied class
     * argument
     * 
     * @param string $class
     * 
     * @return boolean
     */
    public function supportsClass($class)
    {
        return $class === 'Symfony\Component\Security\Core\User\User';
    }
}
