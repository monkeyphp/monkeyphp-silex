<?php

/* article/_article.twig */
class __TwigTemplate_1bd37915b5218cd678dfc115ae517dd97fa421b3be01d9718a175a0c9ae06415 extends Twig_Template
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
        // line 6
        echo "<article class=\"article article--";
        echo twig_escape_filter($this->env, twig_lower_filter($this->env, $this->getAttribute($this->getContext($context, "article"), "category")), "html", null, true);
        echo "\">

    <!-- meta -->
    <div class=\"acticle__meta\">
        <!-- category -->
        ";
        // line 11
        echo twig_escape_filter($this->env, $this->getAttribute($this->getContext($context, "article"), "category"), "html", null, true);
        echo "
        <!-- /category -->

        <!-- created -->
        ";
        // line 15
        echo twig_escape_filter($this->env, twig_title_string_filter($this->env, twig_slice($this->env, twig_date_format_filter($this->env, $this->getAttribute($this->getContext($context, "article"), "created"), "M"), 0, 2)), "html", null, true);
        echo " ";
        echo twig_escape_filter($this->env, twig_date_format_filter($this->env, $this->getAttribute($this->getContext($context, "article"), "created"), "j"), "html", null, true);
        echo "
        <!-- /created -->
    </div>
    <!-- /meta -->

    <div class=\"article__content\">
        ";
        // line 21
        echo twig_escape_filter($this->env, $this->getAttribute($this->getContext($context, "article"), "title"), "html", null, true);
        echo "

        ";
        // line 24
        echo "        ";
        echo call_user_func_array($this->env->getFilter('markdown')->getCallable(), array($this->getAttribute($this->getContext($context, "article"), "summary")));
        echo "
        ";
        // line 26
        echo "
        <a href=\"";
        // line 27
        echo twig_escape_filter($this->env, $this->env->getExtension('routing')->getPath("article_read", array("slug" => $this->getAttribute($this->getContext($context, "article"), "slug"))), "html", null, true);
        echo "\">Read more &hellip;</a>
    </div>

    
</article>
";
    }

    public function getTemplateName()
    {
        return "article/_article.twig";
    }

    public function isTraitable()
    {
        return false;
    }

    public function getDebugInfo()
    {
        return array (  59 => 27,  56 => 26,  51 => 24,  46 => 21,  35 => 15,  28 => 11,  19 => 6,);
    }
}
