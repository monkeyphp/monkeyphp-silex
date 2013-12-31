<?php

/* index/footer.twig */
class __TwigTemplate_b9cfce53a39d3d3a4e6645e6c92ed8f267df39b8d5b92f8ed31d46cff727406f extends Twig_Template
{
    public function __construct(Twig_Environment $env)
    {
        parent::__construct($env);

        $this->parent = false;

        $this->blocks = array(
        );
    }

    protected function doDisplay(array $context, array $blocks = array())
    {
        // line 2
        echo "<footer>
    <nav>
        <ul>
            <li>
                <a href=\"";
        // line 6
        echo $this->env->getExtension('routing')->getPath("login_login");
        echo "\">Login</a>
            </li>
        </ul>
    </nav>
</footer>";
    }

    public function getTemplateName()
    {
        return "index/footer.twig";
    }

    public function isTraitable()
    {
        return false;
    }

    public function getDebugInfo()
    {
        return array (  25 => 6,  19 => 2,);
    }
}
