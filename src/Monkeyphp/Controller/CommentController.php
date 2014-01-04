<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
namespace Monkeyphp\Controller;

use Monkeyphp\Repository\CommentRepository;
use Symfony\Component\Form\FormFactory;
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
     * @var CommentRepository
     */
    protected $commentRepository;
    
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
        Twig_Environment $twigEnvironment,
        FormFactory $formFactory,
        UrlGenerator $urlGenerator,
        CommentRepository $commentRepository
    ) {
        $this->setTwigEnvironment($twigEnvironment);
        $this->setFormFactory($formFactory);
        $this->setUrlGenerator($urlGenerator);
        $this->setCommentRepository($commentRepository);
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

    public function getCommentRepository()
    {
        return $this->commentRepository;
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

    public function setCommentRepository(CommentRepository $commentRepository)
    {
        $this->commentRepository = $commentRepository;
        return $this;
    }

    public function indexAction(Request $request, $id)
    {
        $comments = $this->getCommentRepository()->fetchCommentsByArticleId($id);
        $html = $this->getTwigEnvironment()->render('comment/index.twig', array('comments' => $comments));
        return new Response($html, 200, array());
    }
}
