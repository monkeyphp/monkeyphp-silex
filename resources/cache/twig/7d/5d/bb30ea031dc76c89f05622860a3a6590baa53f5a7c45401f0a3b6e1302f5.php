<?php

/* layout.twig */
class __TwigTemplate_7d5dbb30ea031dc76c89f05622860a3a6590baa53f5a7c45401f0a3b6e1302f5 extends Twig_Template
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
        ";
        // line 9
        $this->displayBlock('content', $context, $blocks);
        // line 12
        echo "    </body>
</html>
";
    }

    // line 9
    public function block_content($context, array $blocks = array())
    {
        // line 10
        echo "            Monkeyphp
        ";
    }

    public function getTemplateName()
    {
        return "layout.twig";
    }

    public function getDebugInfo()
    {
        return array (  40 => 10,  37 => 9,  31 => 12,  29 => 9,  20 => 2,);
    }
}
