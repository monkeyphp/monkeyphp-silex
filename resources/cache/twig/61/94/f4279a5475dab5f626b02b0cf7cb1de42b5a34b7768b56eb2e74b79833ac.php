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
        return array (  40 => 10,  37 => 9,  29 => 9,  20 => 2,  31 => 12,  28 => 4,);
    }
}
