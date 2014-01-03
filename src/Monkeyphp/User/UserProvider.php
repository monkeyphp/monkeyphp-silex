<?php
/**
 * UserProvider.php
 * 
 * @category   Monkeyphp
 * @package    Monkeyphp
 * @subpackage Monkeyphp\User
 * @author     David White <david@monkeyphp.com>
 */
namespace Monkeyphp\User;

use Elasticsearch\Client;
use InvalidArgumentException;
use Symfony\Component\Security\Core\Exception\UnsupportedUserException;
use Symfony\Component\Security\Core\Exception\UsernameNotFoundException;
use Symfony\Component\Security\Core\User\User;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\UserProviderInterface;

/**
 * UserProvider
 * 
 * @category   Monkeyphp
 * @package    Monkeyphp
 * @subpackage Monkeyphp\User
 * @author     David White <david@monkeyphp.com>
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
     * @param Client $elasticsearchClient
     * 
     * @return void
     */
    public function __construct(Client $elasticsearchClient)
    {
        $this->setElasticsearchClient($elasticsearchClient);
    }
    
    /**
     * Return the instance of Client
     * 
     * @return Client
     */
    public function getElasticsearchClient()
    {
        return $this->elasticsearchClient;
    }

    /**
     * Set the instance of Client
     * 
     * @param Client $elasticsearchClient
     * 
     * @return UserProvider
     */
    public function setElasticsearchClient(Client $elasticsearchClient)
    {
        $this->elasticsearchClient = $elasticsearchClient;
        return $this;
    }

    /**
     * @link http://www.elasticsearch.org/guide/en/elasticsearch/client/php-api/current/_quickstart.html
     *
     * @param string $username The username
     *
     * @throws InvalidArgumentException
     * @throws UsernameNotFoundException
     * @return User
     */
    public function loadUserByUsername($username)
    {
        if (! is_string($username)) {
            throw new InvalidArgumentException('A string is expected');
        }

        $params = array(
            'index' => 'monkeyphp',
            'type'  => 'user',
            'body'  => array(
                'from' => 0,
                'size' => 1,
                'fields' => array(
                    'username',
                    'password',
                    'roles',
                    'enabled',
                    'userNonExpired',
                    'credentialsNonExpired',
                    'userNonLocked'
                ),
                'query' => array(
                    'match' => array(
                        'username' => trim(strtolower($username))
                    ),
                ),
            ),
        );

        $result = $this->getElasticsearchClient()->search($params);
        
        if (null === ($hits = (array_key_exists('hits', $result) && is_array($result['hits'])) ? $result['hits'] : null)) {
            throw new UsernameNotFoundException(sprintf('Username "%s" does not exist', $username));
        }

        if (! array_key_exists('total', $hits) ||
            $hits['total']  !== 1 ||
            (null === ($hits = (array_key_exists('hits', $hits) && is_array($hits['hits'])) ? $hits['hits'] : null)) ||
            (false === ($data = reset($hits)))
        ) {
            throw new UsernameNotFoundException(sprintf('Username "%s" does not exist', $username));
        }

        if (null === ($fields = (array_key_exists('fields', $data) && is_array($data['fields'])) ? $data['fields'] : null)) {
            throw new UsernameNotFoundException(sprintf('Username "%s" does not exist', $username));
        }

        // check that both username, password and roles exist in the returned dataset
        if (! isset($fields['username']) || ! is_string($fields['username']) ||
            ! isset($fields['password']) || ! is_string($fields['password']) ||
            ! isset($fields['roles'])    || ! is_array($fields['roles'])
        ) {
            throw new UsernameNotFoundException(sprintf('Username "%s" does not exist', $username));
        }

        $username              = $fields['username'];
        $password              = $fields['password'];
        $roles                 = $fields['roles'];
        $enabled               = (isset($fields['enabled']))               ? $fields['enabled']               : false;
        $userNonExpired        = (isset($fields['userNonExpired']))        ? $fields['userNonExpired']        : false;
        $credentialsNonExpired = (isset($fields['credentialsNonExpired'])) ? $fields['credentialsNonExpired'] : false;
        $userNonLocked         = (isset($fields['userNonLocked']))         ? $fields['userNonLocked']         : false;

        $user = new User(
            $username, 
            $password,
            $roles,
            $enabled,
            $userNonExpired,
            $credentialsNonExpired,
            $userNonLocked
        );

        return $user;
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
