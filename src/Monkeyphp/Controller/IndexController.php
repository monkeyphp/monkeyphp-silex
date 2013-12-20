<?php
/**
 * IndexController.php
 * 
 */
namespace Monkeyphp\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Twig_Environment;


class IndexController
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
     * @return void
     */
    public function __construct(Twig_Environment $twig)
    {
        $this->setTwigEnvironment($twig);
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


    public function indexAction(Request $request)
    {
        $html = $this->getTwigEnvironment()->render('index/index.twig', array());
        $response = new Response($html, 200, array());
        return $response;
    }
}
