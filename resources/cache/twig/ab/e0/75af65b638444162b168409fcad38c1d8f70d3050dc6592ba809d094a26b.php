<?php

/* index/header.twig */
class __TwigTemplate_abe075af65b638444162b168409fcad38c1d8f70d3050dc6592ba809d094a26b extends Twig_Template
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
        echo "<header class=\"header\">
    
    <nav>
        <ul>
            <li>
                <a href=\"";
        // line 7
        echo $this->env->getExtension('routing')->getPath("index_index");
        echo "\">Home</a>
            </li>
            <li>
                <a href=\"";
        // line 10
        echo $this->env->getExtension('routing')->getPath("about_index");
        echo "\">About</a>
            </li>
            <li>
                <a href=\"";
        // line 13
        echo $this->env->getExtension('routing')->getPath("article_index");
        echo "\">Article</a>
            </li>
            <li>
                <a href=\"";
        // line 16
        echo $this->env->getExtension('routing')->getPath("message_index");
        echo "\">Message Me</a>
            </li>
        </ul>
    </nav>
    
</header>";
    }

    public function getTemplateName()
    {
        return "index/header.twig";
    }

    public function isTraitable()
    {
        return false;
    }

    public function getDebugInfo()
    {
        return array (  44 => 16,  38 => 13,  32 => 10,  26 => 7,  19 => 2,);
    }
}
