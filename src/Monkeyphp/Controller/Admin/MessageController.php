<?php
/**
 * MessageController.php
 * 
 * @package    Monkeyphp
 * @package    Monkeyphp
 * @subpackage Monkeyphp\Controller\Admin
 * @author     David White <david@monkeyphp.com>
 */
namespace Monkeyphp\Controller\Admin;

use DateTime;
use DateTimeZone;
use Elasticsearch\Client;
use Symfony\Component\Form\FormFactory;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Generator\UrlGenerator;
use Twig_Environment;

/**
 * MessageController
 * 
 * @package    Monkeyphp
 * @package    Monkeyphp
 * @subpackage Monkeyphp\Controller\Admin
 * @author     David White <david@monkeyphp.com>
 */
class MessageController
{
    /**
     * Instance of Twig_Environment
     * 
     * @var Twig_Environment
     */
    protected $twigEnvironment;
    
    /**
     * Instance of FormFactory
     * 
     * @var FormFactory
     */
    protected $formFactory;
    
    /**
     *
     * @var UrlGenerator
     */
    protected $urlGenerator;
    
    /**
     * Instance of Client
     * 
     * @var Client
     */
    protected $elasticsearchClient;
    
    /**
     * Constructor
     * 
     * @param Twig_Environment $twigEnvironment
     * 
     * @return void
     */
    public function __construct(
        Twig_Environment $twigEnvironment,
        FormFactory $formFactory,
        UrlGenerator $urlGenerator,
        Client $elasticsearchClient
    ) {
        $this->setTwigEnvironment($twigEnvironment);
        $this->setFormFactory($formFactory);
        $this->setUrlGenerator($urlGenerator);
        $this->setElasticsearchClient($elasticsearchClient);
    }
    
    /**
     * Return the Twig_Environment instance
     * 
     * @return Twig_Environment
     */
    public function getTwigEnvironment()
    {
        return $this->twigEnvironment;
    }

    /**
     * Set the Twig_Envrionment instance
     * 
     * @param Twig_Environment $twigEnvironment
     * 
     * @return MessageController
     */
    public function setTwigEnvironment(Twig_Environment $twigEnvironment)
    {
        $this->twigEnvironment = $twigEnvironment;
        return $this;
    }
    
    /**
     * Return the FormFactory instance
     * 
     * @return FormFactory
     */
    public function getFormFactory()
    {
        return $this->formFactory;
    }
    
    /**
     * Set the FormFactory instance
     * 
     * @param FormFactory $formFactory
     * 
     * @return MessageController
     */
    public function setFormFactory(FormFactory $formFactory)
    {
        $this->formFactory = $formFactory;
        return $this;
    }
    
    /**
     * Return the UrlGenerator instance
     * 
     * @return UrlGenerator
     */
    public function getUrlGenerator()
    {
        return $this->urlGenerator;
    }

    /**
     * Set the UrlGenerator instance
     * 
     * @param UrlGenerator $urlGenerator
     * 
     * @return MessageController
     */
    public function setUrlGenerator(UrlGenerator $urlGenerator)
    {
        $this->urlGenerator = $urlGenerator;
        return $this;
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
     * Set the Client instance
     * 
     * @param Client $elasticsearchClient
     * 
     * @return MessageController
     */
    public function setElasticsearchClient(Client $elasticsearchClient)
    {
        $this->elasticsearchClient = $elasticsearchClient;
        return $this;
    }

    /**
     * Index action
     * 
     * List all of the messages currently in the database
     * 
     * @param Request $request
     * 
     * @return Response
     */
    public function indexAction(Request $request)
    {
        $params = array(
            'index' => 'monkeyphp',
            'type'  => 'message',
            'body'  => array(
                'fields' => array(
                    'created',
                    'modified',
                    'email', 
                    'message', 
                    'ip'
                ),
                'query' => array(
                    'match_all' => array()
                ),
            ) 
        );
        
        $results = $this->getElasticsearchClient()->search($params);
        
        $messages = array();
        
        if ($hits = (array_key_exists('hits', $results) && is_array($results['hits'])) ? $results['hits'] : false) {
            
            if ($hits = (array_key_exists('hits', $hits) && is_array($hits['hits'])) ? $hits['hits'] : false) {
                
                foreach ($hits as $hit) {
                    
                    if ((null !== ($id = (isset($hit['_id'])) ? $hit['_id'] : null)) && 
                        $fields = (array_key_exists('fields', $hit) && is_array($hit['fields'])) ? $hit['fields'] : false
                    ) {

                        if (isset($fields['message']) && isset($fields['email'])) {
                            
                            $created  = null;
                            $modified = null;
                            
                            if (isset($fields['created']) && 
                                is_array($fields['created']) && 
                                isset($fields['created']['date'])
                            ) {
                                $timezone = (isset($fields['created']['timezone'])) ? $fields['created']['timezone'] : null;
                                $dateTimeZone = (! is_null($timezone) && is_string($timezone)) ? new DateTimeZone($timezone) : null;
                                $created = new DateTime($fields['created']['date'], $dateTimeZone);
                            }
                            
                            if (isset($fields['modified']) && 
                                is_array($fields['modified']) && 
                                isset($fields['modified']['date'])
                            ) {
                                $timezone = (isset($fields['modified']['timezone'])) ? $fields['modified']['timezone'] : null;
                                $dateTimeZone = (! is_null($timezone) && is_string($timezone)) ? new DateTimeZone($timezone) : null;
                                $modified = new DateTime($fields['modified']['date'], $dateTimeZone);
                            }
                            
                            $messages[] = array(
                                'id'      => $id,
                                'message' => $fields['message'],
                                'email'   => $fields['email'],
                                'ip'      => isset($fields['ip']) ? $fields['ip'] : null,
                                'created' => $created,
                                'modifed' => $modified,
                            );
                        }
                    }
                }
                
            }
        }
        
        $html = $this->getTwigEnvironment()->render('admin/message/index.twig', array('messages' => $messages));
        return new Response($html, 200, array());
    }
    
    
    
    /**
     * Delete the message specified by the supplied id
     * 
     * @param Request $request
     * @param string $id
     * 
     * @return Response
     */
    public function deleteAction(Request $request, $id)
    {
        $form = $this->getFormFactory()->createBuilder('form')
            ->add('id', 'hidden', array('data' => $id))
            ->add('delete', 'submit', array('label' => 'Delete'))
            ->add('cancel', 'submit', array('label' => 'Cancel'))
            ->getForm();
        
        if ($request->isMethod('POST')) {
            
            $form->handleRequest($request);
            
            if ($form->isValid()) {
                if (true === ($form->get('delete')->isClicked())) {

                    $params = array(
                        'index' => 'monkeyphp',
                        'type'  => 'message',
                        'id'    => $id
                    );

                    $result = $this->getElasticsearchClient()->delete($params);
                    
                    return new RedirectResponse($this->getUrlGenerator()->generate('admin_message_index'), 303, array());
                }
            }
        }
        
        $html = $this->getTwigEnvironment()->render('admin/message/delete.twig', array('form' => $form->createView()));
        return new Response($html, 200, array());
    }
}
