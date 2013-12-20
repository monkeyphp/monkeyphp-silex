<?php
/**
 * IndexController.php
 * 
 * @package    Monkeyphp
 * @subpackage Monkeyphp\Controller
 * @author     David White
 */
namespace Monkeyphp\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Twig_Environment;

/**
 * IndexController
 * 
 * @package    Monkeyphp
 * @subpackage Monkeyphp\Controller
 * @author     David White
 */
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
     * @param \Symfony\Component\HttpFoundation\Request $request
     * 
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function indexAction(Request $request)
    {
        $html = $this->getTwigEnvironment()->render('index/index.twig', array());
        $response = new Response($html, 200, array());
        return $response;
    }
}
