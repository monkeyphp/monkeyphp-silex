<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
namespace Monkeyphp\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Description of ArticleController
 *
 * @author David White <david@monkeyphp.com>
 */
class ArticleController
{
    /**
     *
     * @var \Twig_Environment
     */
    protected $twigEnvironment;

    /**
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
     *
     * @var Client
     */
    protected $elasticsearchClient;

    public function __construct(
        \Twig_Environment $twigEnvironment,
        \Symfony\Component\Form\FormFactory $formFactory,
        \Symfony\Component\Routing\Generator\UrlGenerator $urlGenerator,
        \Elasticsearch\Client $elasticsearchClient
    ) {
        $this->setTwigEnvironment($twigEnvironment);
        $this->setFormFactory($formFactory);
        $this->setUrlGenerator($urlGenerator);
        $this->setElasticsearchClient($elasticsearchClient);
    }

    public function getTwigEnvironment()
    {
        return $this->twigEnvironment;
    }

    public function getFormFactory()
    {
        return $this->formFactory;
    }

    public function getUrlGenerator()
    {
        return $this->urlGenerator;
    }

    public function getElasticsearchClient()
    {
        return $this->elasticsearchClient;
    }

    public function setTwigEnvironment(\Twig_Environment $twigEnvironment)
    {
        $this->twigEnvironment = $twigEnvironment;
        return $this;
    }

    public function setFormFactory(FormFactory $formFactory)
    {
        $this->formFactory = $formFactory;
        return $this;
    }

    public function setUrlGenerator(UrlGenerator $urlGenerator)
    {
        $this->urlGenerator = $urlGenerator;
        return $this;
    }

    public function setElasticsearchClient(Client $elasticsearchClient)
    {
        $this->elasticsearchClient = $elasticsearchClient;
        return $this;
    }



    /**
     * List all of the current published articles in the database
     *
     * @param Request $request
     * 
     * @return Response
     */
    public function indexAction(Request $request)
    {
        $params = array(
            'index' => 'monkeyphp',
            'type'  => 'article',
            'body'  => array(
                'filtered' => array(
                    'filter' => array(
                        'bool' => array(
                            'must' => array(
                                'published' => true
                            ),
                        ),
                    ),
                ),
            ),
        );

        $html = '<p>I am the Article index template</p>';
        
        return new Response($html, 200, array());
    }
}
