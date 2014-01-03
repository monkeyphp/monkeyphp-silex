<?php
/**
 * ContactController.php
 * 
 * @category   Monkeyphp
 * @package    Monkeyphp
 * @subpackage Monkeyphp\Controller
 * @author     David White <david@monkeyphp.com>
 */
namespace Monkeyphp\Controller;

use DateTime;
use Elasticsearch\Client;
use Monkeyphp\Form\ContactType;
use Symfony\Component\Form\FormFactory;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Generator\UrlGenerator;
use Twig_Environment;

/**
 * ContactController
 * 
 * @category   Monkeyphp
 * @package    Monkeyphp
 * @subpackage Monkeyphp\Controller
 * @author     David White <david@monkeyphp.com>
 */
class MessageController
{
    /**
     * The key that the message id is put under
     * 
     * @var string
     */
    const SESSION_MESSAGE_KEY = 'mnkyphpmssgky';
    
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
     * Instance of UrlGenerator
     * 
     * @var UrlGenerator
     */
    protected $urlGenerator;
    
    /**
     * Instance of Session
     * 
     * @var Session
     */
    protected $session;
    
    /**
     * Instance of Client
     * 
     * @var Client
     */
    protected $elasticsearchClient;
    
    /**
     * Constructor
     *
     * @param Twig_Environment Instance of Twig
     *
     * @return void
     */
    public function __construct(
        Twig_Environment $twig,
        FormFactory $formFactory,
        UrlGenerator $urlGenerator, 
        Session $session,    
        Client $elasticsearchClient
    ) {
        $this->setTwigEnvironment($twig);
        $this->setFormFactory($formFactory);
        $this->setUrlGenerator($urlGenerator);
        $this->setSession($session);
        $this->setElasticsearchClient($elasticsearchClient);
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
     * Return the Twig_Environment instance
     *
     * @return Twig_Environment
     */
    public function getTwigEnvironment()
    {
        return $this->twigEnvironment;
    }

    /**
     * Set the Twig_Environment instance
     *
     * @param Twig_Environment $twigEnvironment
     *
     * @return IndexController
     */
    public function setTwigEnvironment(Twig_Environment $twigEnvironment)
    {
        $this->twigEnvironment = $twigEnvironment;
        return $this;
    }
    
    /**
     * Return the instance of UrlGenerator
     * 
     * @return UrlGenerator
     */
    public function getUrlGenerator()
    {
        return $this->urlGenerator;
    }

    /**
     * Set the instance of UrlGenerator
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
     * Return the instance of Session
     * 
     * @return Session
     */
    public function getSession()
    {
        return $this->session;
    }
    
    /**
     * Set the instance of Session
     * 
     * @param Session $session
     * 
     * @return MessageController
     */
    public function setSession(Session $session)
    {
        $this->session = $session;
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
     * Return an instance of ContactType
     * 
     * @return ContactType
     */
    public function getContactForm()
    {
        if (! isset($this->contactForm)) {
            $this->contactForm = $this->getFormFactory()->create(new ContactType());
        }
        return $this->contactForm;
    }
    
    /**
     * IndexAction
     *
     * @param Request $request
     *
     * @return Response
     */
    public function indexAction(Request $request)
    {
        $contactForm = $this->getContactForm();
        
        $contactForm->handleRequest($request);
        
        if ($contactForm->isValid()) {
            $data = $contactForm->getData();
            
            $params = array(
                'index' => 'monkeyphp',
                'type'  => 'message',
                'body' => array(
                    'created'  => new DateTime(),
                    'modified' => new DateTime(),
                    'email'    => $data['email'],
                    'message'  => $data['message'],
                    'ip'       => $request->getClientIp(),
                )
            );
            
            $result = $this->getElasticsearchClient()->index($params);
            
            if (! array_key_exists('ok', $result) || 
                ! $result['ok'] || 
                (null === ($id = (array_key_exists('_id', $result) && is_string($result['_id'])) ? $result['_id'] : null))) {
                throw new \Exception('The message could not be saved');
            }
            
            $this->getSession()->set(self::SESSION_MESSAGE_KEY, $id);
            
            return new RedirectResponse(
                $this->getUrlGenerator()->generate('contact_thankyou'), 
                302, 
                array()
            );
        }
        
        $html = $this->getTwigEnvironment()->render('message/index.twig', array('form' => $contactForm->createView()));
        $response = new Response($html, 200, array());
        return $response;
    }
    
    /**
     * Thankyou action
     * 
     * Display a thankyou message once a message has been successfully recieved
     * 
     * @param Request $request
     * 
     * @throws NotFoundHttpException
     * @return Response
     */
    public function thankyouAction(Request $request)
    {
        if (! $this->getSession()->has(self::SESSION_MESSAGE_KEY)) {
            throw new NotFoundHttpException('Not found');
        }
        
        $this->getSession()->remove(self::SESSION_MESSAGE_KEY);
        
        $html = $this->getTwigEnvironment()->render('message/thankyou.twig', array());
        $response = new Response($html, 200, array());
        return $response;
    }
}
