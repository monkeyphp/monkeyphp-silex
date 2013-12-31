<?php

/* layout.twig */
class __TwigTemplate_6194f4279a5475dab5f626b02b0cf7cb1de42b5a34b7768b56eb2e74b79833ac extends Twig_Template
{
    public function __construct(Twig_Environment $env)
    {
        parent::__construct($env);

        $this->parent = false;

        $this->blocks = array(
            'content' => array($this, 'block_content'),
        );
    }

    protected function doDisplay(array $context, array $blocks = array())
    {
        // line 2
        echo "<!DOCTYPE html>
<html>
    <head>
        <title>Monkeyphp</title>
        <link rel=\"stylesheet\" href=\"/assets/css/styles.css\" />
    </head>
    <body>
        <esi:include src=\"";
        // line 9
        echo $this->env->getExtension('routing')->getPath("index_header");
        echo "\" />

        ";
        // line 11
        $this->displayBlock('content', $context, $blocks);
        // line 14
        echo "
        <esi:include src=\"";
        // line 15
        echo $this->env->getExtension('routing')->getPath("index_footer");
        echo "\" />
    </body>
</html>
";
    }

    // line 11
    public function block_content($context, array $blocks = array())
    {
        // line 12
        echo "            Monkeyphp
        ";
    }

    public function getTemplateName()
    {
        return "layout.twig";
    }

    public function isTraitable()
    {
        return false;
    }

    public function getDebugInfo()
    {
        return array (  50 => 12,  47 => 11,  39 => 15,  36 => 14,  34 => 11,  29 => 9,  20 => 2,);
    }
}
