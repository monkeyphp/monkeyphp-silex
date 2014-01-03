<?php

/* index/index.twig */
class __TwigTemplate_09883e16bd02e853dcd7a6c9d6f37ca19ba9d15014dce0bf9ea52cc5c68ad018 extends Twig_Template
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
        echo "<p>
    This is the index page
</p>

<p>
    Lorem ipsum dolor sit amet, vocibus perpetua ex vel. Mel essent sententiae intellegam cu, qui aliquip labores laoreet ea, usu affert verear labitur an. Mea an possit indoctum, eu est sint interpretaris, an animal utamur quaestio mea. Feugait consetetur usu ne, an quo discere alterum nominavi. Mel admodum reprimique at, illum deserunt atomorum ei nam.
</p>
<p>
    Ne volutpat voluptaria cum, ei doctus neglegentur nam. Ex ignota dissentiunt vis. At graeci numquam vulputate mei. An dicant aliquip moderatius pro.
</p>
<p>
    No stet unum eam, putent vivendo praesent ne cum. Decore alterum scaevola no sea, an mutat atqui consulatu pro. Ea qui affert scribentur, ne vis bonorum volutpat complectitur, per et rebum lorem. Eam in harum forensibus.
</p>
";
    }

    public function getTemplateName()
    {
        return "index/index.twig";
    }

    public function isTraitable()
    {
        return false;
    }

    public function getDebugInfo()
    {
        return array (  31 => 5,  28 => 4,);
    }
}
