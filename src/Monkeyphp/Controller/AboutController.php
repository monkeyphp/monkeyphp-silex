<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
namespace Monkeyphp\Controller;

use DateTime;
use DateTimeZone;
use Elasticsearch\Client;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Twig_Environment;

/**
 * Description of AboutController
 *
 * @author davidwhite
 */
class AboutController
{
    /**
     * Instance of Twig_Environment
     *
     * @var Twig_Environment
     */
    protected $twigEnvironment;
    
    /**
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
    public function __construct(Twig_Environment $twig, Client $elasticsearchClient)
    {
        $this->setTwigEnvironment($twig);
        $this->setElasticsearchClient($elasticsearchClient);
    }

    public function getElasticsearchClient()
    {
        return $this->elasticsearchClient;
    }

    /**
     * Set the Client isntance
     * 
     * @param Client $elasticsearchClient
     * 
     * @return AboutController
     */
    public function setElasticsearchClient(Client $elasticsearchClient)
    {
        $this->elasticsearchClient = $elasticsearchClient;
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
     * IndexAction
     *
     * @param Request $request
     *
     * @return Response
     */
    public function indexAction(Request $request)
    {
        $params = array(
            'index' => 'monkeyphp',
            'type' => 'about',
            'body' => array(
                'fields' => array(
                    'created',
                    'modified',
                    'body'
                ),
                'from' => 0,
                'size' => 1,
                'query' => array(
                    'match_all' => array()
                )
            )
        );
        
        $results = $this->getElasticsearchClient()->search($params);
        
        $about = null;
        
        if ($hits = (array_key_exists('hits', $results) && is_array($results['hits'])) ? $results['hits'] : false) {

            if (array_key_exists('total', $hits) && $hits['total'] === 1) {
            
                if ($hits = (array_key_exists('hits', $hits) && is_array($hits['hits'])) ? $hits['hits'] : false) {
                    
                    $hit = reset($hits);
                    
                    if ((null !== ($id = (isset($hit['_id'])) ? $hit['_id'] : null)) && 
                        $fields = (array_key_exists('fields', $hit) && is_array($hit['fields'])) ? $hit['fields'] : false
                    ) {
                        
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
                        
                        $about = array(
                            'created' => $created, 
                            'modifed' => $modified, 
                            'body' => isset($fields['body']) ? $fields['body'] : null
                        );
                    }
                }
            }
        }
        
        $html = $this->getTwigEnvironment()->render('about/index.twig', array('about' => $about));
        $response = new Response($html, 200, array());
        return $response;
    }
}
