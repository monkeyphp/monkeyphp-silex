<?php

/* admin/article/read.twig */
class __TwigTemplate_35e7c453ecc459360e319661bf55b3b404638620babd02c815b46412675b19e4 extends Twig_Template
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
    
    <h2>";
        // line 9
        echo twig_escape_filter($this->env, $this->getAttribute($this->getContext($context, "article"), "title"), "html", null, true);
        echo "</h2>
    
    ";
        // line 12
        echo "    ";
        echo call_user_func_array($this->env->getFilter('markdown')->getCallable(), array($this->getAttribute($this->getContext($context, "article"), "summary")));
        echo "
    ";
        // line 14
        echo "    
    ";
        // line 16
        echo "    ";
        echo call_user_func_array($this->env->getFilter('markdown')->getCallable(), array($this->getAttribute($this->getContext($context, "article"), "body")));
        echo "
    ";
        // line 18
        echo "    
    ";
        // line 19
        echo twig_escape_filter($this->env, $this->getAttribute($this->getContext($context, "article"), "created"), "html", null, true);
        echo "
    
    ";
        // line 21
        echo twig_escape_filter($this->env, $this->getAttribute($this->getContext($context, "article"), "modified"), "html", null, true);
        echo "
    
</section>

";
    }

    public function getTemplateName()
    {
        return "admin/article/read.twig";
    }

    public function isTraitable()
    {
        return false;
    }

    public function getDebugInfo()
    {
        return array (  62 => 21,  57 => 19,  54 => 18,  49 => 16,  46 => 14,  41 => 12,  36 => 9,  31 => 6,  28 => 5,);
    }
}
