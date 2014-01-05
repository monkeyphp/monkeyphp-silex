<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
namespace Monkeyphp\Controller;

use DateTime;
use Monkeyphp\Entity\Comment;
use Monkeyphp\Form\CommentType;
use Monkeyphp\Repository\ArticleRepository;
use Symfony\Component\Form\FormFactory;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Generator\UrlGenerator;
use Twig_Environment;

/**
 * Description of CommentController
 *
 * @author David White <david@monkeyphp.com>
 */
class CommentController
{
    const SESSION_COMMENT_KEY = 'mnkyphpcmmntky';
    
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
     * @param CommentRepository $commentRepository
     * 
     * @return void
     */
    public function __construct(
        Twig_Environment  $twigEnvironment,
        FormFactory       $formFactory,
        UrlGenerator      $urlGenerator,
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

    public function getArticleRepository()
    {
        return $this->articleRepository;
    }

    public function setArticleRepository(ArticleRepository $articleRepository)
    {
        $this->articleRepository = $articleRepository;
        return $this;
    }

        
    public function indexAction(Request $request, $id)
    {
        $comments = $this->getArticleRepository()->fetchCommentsByArticle($id);
        $html = $this->getTwigEnvironment()->render('comment/index.twig', array('comments' => $comments));
        return new Response($html, 200, array());
    }
    
    public function createAction(Request $request, $id)
    {   
        $form = $this->getFormFactory()->create(
            new CommentType(), 
            null,
            array(
                'action'  => $this->getUrlGenerator()->generate('comment_create', array('id' => $id)),
                'article' => $id
            )
        );
        
        $form->handleRequest($request);
        
        if ($form->isValid()) {
            
            $data = $form->getData();
            
            $options = array (
                'email'     => $data['email'],
                'body'      => $data['body'],
                'created'   => new DateTime(),
                'modified'  => new DateTime(),
                'ip'        => $request->getClientIp(),
                'published' => false,
                'articleId' => $data['article']
            );
            
            $comment = new Comment($options);
            
            if (null !== ($id = $this->getArticleRepository()->saveComment($comment))) {
                
                $this->getSession()->set(self::SESSION_COMMENT_KEY, $id);
                $url = $this->getUrlGenerator()->generate('comment_thankyou');
                return new RedirectResponse($url, 302, array());
                //$url = $this->getUrlGenerator()->generate('article_read', array('slug' => $slug));
            }
            
            throw new \Exception('Could not save the Comment');
        }
        
        $html = $this->getTwigEnvironment()->render('comment/create.twig', array('form' => $form->createView()));
        return new Response($html, 200, array());
    }
}
