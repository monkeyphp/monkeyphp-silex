<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
namespace Monkeyphp\Controller;

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
     * Constructor
     *
     * @param Twig_Environment Instance of Twig
     *
     * @return void
     */
    public function __construct(Twig_Environment $twig)
    {
        $this->setTwigEnvironment($twig);
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
        $html = $this->getTwigEnvironment()->render('about/index.twig', array());
        $response = new Response($html, 200, array());
        return $response;
    }
}
