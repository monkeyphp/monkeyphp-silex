<?php
/**
 * AboutController.php
 * 
 * @category   Monkeyphp
 * @package    Monkeyphp
 * @subpackage Monkeyphp\Controller
 * @author     David White <david@monkeyphp.com>
 */
namespace Monkeyphp\Controller;

use Monkeyphp\Repository\AboutRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Twig_Environment;

/**
 * AboutController
 * 
 * @category   Monkeyphp
 * @package    Monkeyphp
 * @subpackage Monkeyphp\Controller
 * @author     David White <david@monkeyphp.com>
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
     * @var AboutRepository
     */
    protected $aboutRepository;
    
    /**
     * Constructor
     *
     * @param Twig_Environment Instance of Twig
     *
     * @return void
     */
    public function __construct(Twig_Environment $twig, AboutRepository $aboutRepository)
    {
        $this->setTwigEnvironment($twig);
        $this->setAboutRepository($aboutRepository);
    }
    
    public function getAboutRepository()
    {
        return $this->aboutRepository;
    }

    public function setAboutRepository(AboutRepository $aboutRepository)
    {
        $this->aboutRepository = $aboutRepository;
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
        $about = $this->getAboutRepository()->fetchAbout();
        $html = $this->getTwigEnvironment()->render('about/index.twig', array('about' => $about));
        $response = new Response($html, 200, array());
        return $response;
    }
}
