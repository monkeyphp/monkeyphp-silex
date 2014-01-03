<?php

/* admin/about/index.twig */
class __TwigTemplate_f67766a09033577ba69144e41e10e0f6fd6ada9d4b1ee7cbbb83e03282e09c7e extends Twig_Template
{
    public function __construct(Twig_Environment $env)
    {
        parent::__construct($env);

        $this->parent = $this->env->loadTemplate("layout.twig");

        $this->blocks = array(
            'content' => array($this, 'block_content'),
        );
    }

    protected function doGetParent(array $context)
    {
        return "layout.twig";
    }

    protected function doDisplay(array $context, array $blocks = array())
    {
        $this->parent->display($context, array_merge($this->blocks, $blocks));
    }

    // line 5
    public function block_content($context, array $blocks = array())
    {
        // line 6
        echo "
<section>
    
    ";
        // line 9
        if (array_key_exists("about", $context)) {
            // line 10
            echo "        ";
            if ($this->getAttribute($this->getContext($context, "about", true), "body", array(), "any", true, true)) {
                echo " 
        ";
                // line 12
                echo "        ";
                echo call_user_func_array($this->env->getFilter('markdown')->getCallable(), array($this->getAttribute($this->getContext($context, "about"), "body")));
                echo "
        ";
                // line 14
                echo "        ";
            }
            // line 15
            echo "    ";
        } else {
            // line 16
            echo "        
    ";
        }
        // line 18
        echo "</section>

";
    }

    public function getTemplateName()
    {
        return "admin/about/index.twig";
    }

    public function isTraitable()
    {
        return false;
    }

    public function getDebugInfo()
    {
        return array (  58 => 18,  54 => 16,  51 => 15,  48 => 14,  43 => 12,  38 => 10,  36 => 9,  31 => 6,  28 => 5,);
    }
}
