<?php
/**
 * ArticleController.php
 * 
 * @category   Monkeyphp
 * @package    Monkeyphp
 * @subpackage Monkeyphp\Controller
 * @author     David White <david@monkeyphp.com>
 */
namespace Monkeyphp\Controller;

use Monkeyphp\Repository\ArticleRepository;
use Symfony\Component\Form\FormFactory;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Generator\UrlGenerator;
use Twig_Environment;

/**
 * ArticleController
 * 
 * @category   Monkeyphp
 * @package    Monkeyphp
 * @subpackage Monkeyphp\Controller
 * @author     David White <david@monkeyphp.com>
 */
class ArticleController
{
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
     *
     * @var ArticleRepository
     */
    protected $articleRepository;
    
    /**
     * Constructor
     * 
     * @param Twig_Environment  $twigEnvironment
     * @param FormFactory       $formFactory
     * @param UrlGenerator      $urlGenerator
     * @param ArticleRepository $articleRepository
     * 
     * @return void
     */
    public function __construct(
        Twig_Environment $twigEnvironment,
        FormFactory $formFactory,
        UrlGenerator $urlGenerator,
        ArticleRepository $articleRepository
    ) {
        $this->setTwigEnvironment($twigEnvironment);
        $this->setFormFactory($formFactory);
        $this->setUrlGenerator($urlGenerator);
        $this->setArticleRepository($articleRepository);
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
    
    public function getArticleRepository()
    {
        return $this->articleRepository;
    }

    public function setArticleRepository(ArticleRepository $articleRepository)
    {
        $this->articleRepository = $articleRepository;
        return $this;
    }

    public function setTwigEnvironment(Twig_Environment $twigEnvironment)
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

    /**
     * List all of the current published articles in the database
     *
     * @param Request $request
     * 
     * @return Response
     */
    public function indexAction(Request $request)
    {
        $articles = $this->getArticleRepository()->fetchArticles();
        $html = $this->getTwigEnvironment()->render('article/index.twig', array('articles' => $articles));
        return new Response($html, 200, array());
    }
    
    /**
     * Read action
     * 
     * @param Request $request
     * @param string  $slug
     * 
     * @return Response
     */
    public function readAction(Request $request, $slug)
    {
        $article = $this->getArticleRepository()->findArticleBySlug($slug);
        $html = $this->getTwigEnvironment()->render('article/read.twig', array('article' => $article));
        return new Response($html, 200, array());
    }
     
}