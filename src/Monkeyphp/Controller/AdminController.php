<?php
namespace Monkeyphp\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Twig_Environment;

/**
 * Description of AdminController
 *
 * @author David White <david@monkeyphp.com>
 */
class AdminController
{
    /**
     *
     * @var Twig_Environment
     */
    protected $twigEnvironment;
    
    public function __construct(Twig_Environment $twigEnvironment)
    {
        $this->setTwigEnvironment($twigEnvironment);
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
     * IndexAction
     * 
     * @param Request $request
     * 
     * @return Response
     */
    public function indexAction(Request $request)
    {
        $html = $this->getTwigEnvironment()->render('admin/index.twig', array());
        return new Response($html, 200, array());
    }
}
