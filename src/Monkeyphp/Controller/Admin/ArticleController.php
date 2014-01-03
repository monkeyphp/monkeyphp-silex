<?php


/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
namespace Monkeyphp\Controller\Admin;

use DateTime;
use Elasticsearch\Client;
use Exception;
use Monkeyphp\Form\ArticleType;
use Symfony\Component\Form\FormFactory;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Generator\UrlGenerator;
use Twig_Environment;

/**
 * Description of ArticleController
 *
 * @author David White <david@monkeyphp.com>
 */
class ArticleController
{
    /**
     *
     * @var ArticleType
     */
    protected $articleForm;
    
    /**
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
    
    
    public function getArticleForm()
    {
        if (! isset($this->articleForm)) {
            $this->articleForm = $this->getFormFactory()->create(new ArticleType());//, null, $options);
        }
        return $this->articleForm;
    }
    
    public function getCategories()
    {
        
    }
    
    public function getTwigEnvironment()
    {
        return $this->twigEnvironment;
    }

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
    
    public function setFormFactory(FormFactory $formFactory)
    {
        $this->formFactory = $formFactory;
        return $this;
    }
    
    public function getUrlGenerator()
    {
        return $this->urlGenerator;
    }

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
     * Set the instance of Client
     * 
     * @param Client $elasticsearchClient
     * 
     * @return ArticleController
     */
    public function setElasticsearchClient(Client $elasticsearchClient)
    {
        $this->elasticsearchClient = $elasticsearchClient;
        return $this;
    }

    
    /**
     * Index action
     * 
     * @param Request $request
     * 
     * @return Response
     */
    public function indexAction(Request $request)
    {
        return new Response('<p>I am the Admin Article Index template</p>', 200, array());
    }
    
    /**
     * Create a new article
     * 
     * @param Request $request
     * 
     * @return Response
     */
    public function createAction(Request $request)
    {
        $articleForm = $this->getArticleForm();
        
        $articleForm->handleRequest($request);
        
        if ($articleForm->isValid()) {
            
            $data = $articleForm->getData();
            
            // save the article
            $params = array(
                'index' => 'monkeyphp',
                'type'  => 'article',
                'body'  => array(
                    'title'     => $data['title'], 
                    'category'  => $data['category'], 
                    'summary'   => $data['summary'], 
                    'body'      => $data['body'], 
                    'published' => $data['published'], 
                    'slug'      => $data['slug'],
                    'created'   => new DateTime(), 
                    'modified'  => new DateTime()
                )
            );
            
            $result = $this->getElasticsearchClient()->index($params);
            
            if (! array_key_exists('ok', $result) || 
                ! $result['ok'] || 
                (null === ($id = (array_key_exists('_id', $result) && is_string($result['_id'])) ? $result['_id'] : null))) {
                throw new Exception('The message could not be saved');
            }
            
            return new RedirectResponse(
                $this->getUrlGenerator()->generate('admin_article_read', array('id' => $id)), 
                303, 
                array()
            );
        }
        
        $html = $this->getTwigEnvironment()->render('admin/article/create.twig', array('form' => $articleForm->createView()));
        return new Response($html, 200, array());
    }
    
    /**
     * Read the article specified by the supplied id string
     * 
     * @param Request $request
     * @param string $id
     * 
     * @throws NotFoundHttpException
     * @return Response
     */
    public function readAction(Request $request, $id)
    {
        $params = array(
            'index' => 'monkeyphp',
            'type' => 'article',
            'id' => $id,
            'fields' => array(
                'title',
                'summary',
                'body',
                'category',
                'published',
                'slug',
                'created',
                'modified'
            )
        );
        
        $result = $this->getElasticsearchClient()->get($params);
        
        if (! is_array($result) || 
            ! isset($result['exists']) || 
            $result['exists'] !== true || 
            (null === ($fields = (isset($result['fields']) && is_array($result['fields'])) ? $result['fields'] : null))
        ) {
            throw new NotFoundHttpException('The article could not be located');
        }
        
        $modified = '';
        $created  = ''; 
        
        $article = array(
            'title'     => isset($fields['title'])   ? $fields['title'] : null,
            'summary'   => isset($fields['summary']) ? $fields['summary'] : null, 
            'body'      => isset($fields['body'])    ? $fields['body'] : null, 
            'category'  => '',
            'published' => '',
            'slug'      => isset($fields['slug']) ? $fields['slug'] : null, 
            'created'   => $created,
            'modified'  => $modified
        );
        return new Response(
            $this->getTwigEnvironment()->render('admin/article/read.twig', array('article' => $article)),
            200, 
            array()
        );
    }
}
