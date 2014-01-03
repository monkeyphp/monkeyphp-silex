<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
namespace Monkeyphp\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use DateTime;
use DateTimeZone;

/**
 * Description of ArticleController
 *
 * @author David White <david@monkeyphp.com>
 */
class ArticleController
{
    /**
     *
     * @var Twig_Environment
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

    public function setFormFactory(\Symfony\Component\Form\FormFactory $formFactory)
    {
        $this->formFactory = $formFactory;
        return $this;
    }

    public function setUrlGenerator(\Symfony\Component\Routing\Generator\UrlGenerator $urlGenerator)
    {
        $this->urlGenerator = $urlGenerator;
        return $this;
    }

    public function setElasticsearchClient(\Elasticsearch\Client $elasticsearchClient)
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
                'fields' => array(
                    'title',
                    'category',
                    'summary',
                    'slug',
                    'created',
                    'modified'
                ),
                'query' => array(
                    'filtered' => array(
                        'filter' => array(
                            'bool' => array(
                                'must' => array(
                                    'term' => array(
                                        'published' => true,
                                    ),
                                ),
                            ),
                        ),
                        'query' => array(
                            'match_all' => array(
                                //
                            ),
                        ),
                    ),
                ),
            ),
        );

        $result = $this->getElasticsearchClient()->search($params);

        $articles = array();

        if ($hits = (array_key_exists('hits', $result) && is_array($result['hits'])) ? $result['hits'] : false) {
            if ($hits = (array_key_exists('hits', $hits) && is_array($hits['hits'])) ? $hits['hits'] : false) {
                foreach ($hits as $hit) {
                    if ((null !== ($id = (isset($hit['_id'])) ? $hit['_id'] : null)) &&
                        $fields = (array_key_exists('fields', $hit) && is_array($hit['fields'])) ? $hit['fields'] : false
                    ) {

                        if (isset($fields['title']) &&
                            isset($fields['slug']) &&
                            isset($fields['summary'])
                        ) {
                            $created = $modified = null;

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

                            $articles[] = array(
                                'id'       => $id,
                                'title'    => $fields['title'],
                                'category' => $fields['category'],
                                'slug'     => $fields['slug'],
                                'summary'  => $fields['summary'],
                                'created'  => $created,
                                'modifed'  => $modified
                            );
                        }

                    }
                }
            }
        }

        $html = $this->getTwigEnvironment()->render('article/index.twig', array('articles' => $articles));
        return new Response($html, 200, array());
    }
}
