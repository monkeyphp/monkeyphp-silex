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
<html lang=\"en\">
    <head>
        <title>Monkeyphp</title>
        <meta http-equiv=\"Content-Type\" content=\"text/html; charset=utf-8\" />
        <link rel=\"stylesheet\" href=\"/assets/css/styles.css\" />
    </head>
    <body>
        
        <section class=\"container\">
            
            <!-- header -->
            <esi:include src=\"";
        // line 14
        echo $this->env->getExtension('routing')->getPath("index_header");
        echo "\" />
            <!-- /header -->
            
            <!-- content -->
            <section class=\"content\">
                ";
        // line 19
        $this->displayBlock('content', $context, $blocks);
        // line 22
        echo "            </section>
            <!-- /content -->
            
            <!-- footer -->
            <esi:include src=\"";
        // line 26
        echo $this->env->getExtension('routing')->getPath("index_footer");
        echo "\" />
            <!-- /footer -->
            
        </section>
        
    </body>
</html>
";
    }

    // line 19
    public function block_content($context, array $blocks = array())
    {
        // line 20
        echo "                    <p>Monkeyphp</p>
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
        return array (  65 => 20,  62 => 19,  50 => 26,  44 => 22,  42 => 19,  34 => 14,  20 => 2,);
    }
}
