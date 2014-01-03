<?php

/* about/index.twig */
class __TwigTemplate_3317ec334ad9f1b16222704cc87b5853fe1f4b115dc86146c3eb966f76fe9dc2 extends Twig_Template
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

    // line 4
    public function block_content($context, array $blocks = array())
    {
        // line 5
        echo "
    ";
        // line 6
        if (array_key_exists("about", $context)) {
            // line 7
            echo "        ";
            if ($this->getAttribute($this->getContext($context, "about", true), "body", array(), "any", true, true)) {
                echo " 
        ";
                // line 9
                echo "        ";
                echo call_user_func_array($this->env->getFilter('markdown')->getCallable(), array($this->getAttribute($this->getContext($context, "about"), "body")));
                echo "
        ";
                // line 11
                echo "        ";
            }
            // line 12
            echo "    ";
        } else {
            // line 13
            echo "        
    ";
        }
        // line 15
        echo "
";
    }

    public function getTemplateName()
    {
        return "about/index.twig";
    }

    public function isTraitable()
    {
        return false;
    }

    public function getDebugInfo()
    {
        return array (  56 => 15,  52 => 13,  49 => 12,  46 => 11,  41 => 9,  36 => 7,  34 => 6,  31 => 5,  28 => 4,);
    }
}
