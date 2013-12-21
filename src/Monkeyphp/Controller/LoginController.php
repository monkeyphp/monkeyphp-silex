<?php
namespace Monkeyphp\Controller;

use Symfony\Component\Form\Form;
use Symfony\Component\Form\FormFactory;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Generator\UrlGenerator;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;
use Twig_Environment;

/**
 * Description of LoginController
 *
 * @author David White <david@monkeyphp.com>
 */
class LoginController
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
     * Constructor
     * 
     * @param Twig_Environment $twigEnvironment
     * @param FormFactory      $formFactory
     * @param UrlGenerator     $urlGenerator
     * 
     * @return void
     */
    public function __construct(Twig_Environment $twigEnvironment, FormFactory $formFactory, UrlGenerator $urlGenerator)
    {
        $this->setTwigEnvironment($twigEnvironment);
        $this->setFormFactory($formFactory);
        $this->setUrlGenerator($urlGenerator);
    }
    
    public function getTwigEnvironment()
    {
        return $this->twigEnvironment;
    }
    
    /**
     * 
     * @return FormFactory
     */
    public function getFormFactory()
    {
        return $this->formFactory;
    }

    public function setTwigEnvironment($twigEnvironment)
    {
        $this->twigEnvironment = $twigEnvironment;
        return $this;
    }

    public function setFormFactory($formFactory)
    {
        $this->formFactory = $formFactory;
        return $this;
    }
    
    public function getUrlGenerator()
    {
        return $this->urlGenerator;
    }

    public function setUrlGenerator(UrlGenerator $urlGenerator)
    {
        $this->urlGenerator = $urlGenerator;
        return $this;
    }

    /**
     * LoginAction
     * 
     * @param Request $request
     * 
     * @return Response|RedirectResponse
     */    
    public function loginAction(Request $request)
    {
        $loginForm = $this->getLoginForm();
        
        $loginForm->handleRequest($request);
        
        if ($loginForm->isValid()) {
            
            $url = $this->getUrlGenerator()->generate('admin_index');
            $response = new RedirectResponse($url, 302, array());
            return $response;
        }
        
        $html = $this->getTwigEnvironment()->render('login/login.html', array('loginForm' => $loginForm->createView));
        $response = new Response($html, 200, array());
        return $response;
    }
    
    /**
     * Return an instance of Form configured for logging in
     * 
     * @return Form
     */
    protected function getLoginForm()
    {
        $form = $this->getFormFactory()
            ->createBuilder('login')
            ->add(
                'username', 
                'text',
                array(
                    'constraints' => array(
                        new NotBlank(),
                        new Length(array('min' => 8))
                    )
                )
            )
            ->add(
                'password',
                'password',
                array(
                    'constraints' => array(
                        new NotBlank(),
                        new Length(array('min' => 8))
                    )
                )
            )
            ->add(
                'submit', 
                'submit', 
                array()
            )
            ->getForm();
        
        return $form;
    }
}
