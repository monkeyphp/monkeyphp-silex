<?php
namespace Monkeyphp\Controller;

use Monkeyphp\Form\LoginType;
use Symfony\Component\Form\FormFactory;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Generator\UrlGenerator;
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
     * @var UrlGenerator
     */
    protected $urlGenerator;

    /**
     *
     * @var FormFactory
     */
    protected $formFactory;

    /**
     *
     * @var LoginType
     */
    protected $loginForm;
    
    /**
     * Constructor
     * 
     * @param Twig_Environment $twigEnvironment
     * @param FormFactory      $formFactory
     * @param UrlGenerator     $urlGenerator
     * 
     * @return void
     */
    public function __construct(
        Twig_Environment $twigEnvironment,
        FormFactory $formFactory,
        UrlGenerator $urlGenerator
    ) {
        $this->setTwigEnvironment($twigEnvironment);
        $this->setFormFactory($formFactory);
        $this->setUrlGenerator($urlGenerator);
    }
    
    public function getTwigEnvironment()
    {
        return $this->twigEnvironment;
    }

    public function setTwigEnvironment($twigEnvironment)
    {
        $this->twigEnvironment = $twigEnvironment;
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

    public function getFormFactory()
    {
        return $this->formFactory;
    }

    public function setFormFactory(FormFactory $formFactory)
    {
        $this->formFactory = $formFactory;
        return $this;
    }

    /**
     * @link http://stackoverflow.com/questions/13384056/symfony2-1-using-form-with-method-get/13474522#13474522
     * @return type
     */
    public function getLoginForm()
    {
        if (! isset($this->loginForm)) {
            $this->loginForm = $this->getFormFactory()->createNamed(null, new LoginType());
        }
        return $this->loginForm;
    }

    public function setLoginForm(LoginType $loginForm)
    {
        $this->loginForm = $loginForm;
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
        
        $html = $this->getTwigEnvironment()->render('login/login.twig', array('form' => $loginForm->createView()));
        $response = new Response($html, 200, array());
        return $response;
    }
}
