<?php

/* admin/message/_message.twig */
class __TwigTemplate_47cc3b72804e222d5ffcb20c1334876931a1e3dc1853fbfecfc0a7be065ad59c extends Twig_Template
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
        echo "
";
        // line 4
        echo "<article>
    <p>
        ";
        // line 6
        echo twig_escape_filter($this->env, $this->getAttribute($this->getContext($context, "message"), "message"), "html", null, true);
        echo "
    </p>
    <p>
        from ";
        // line 9
        echo twig_escape_filter($this->env, $this->getAttribute($this->getContext($context, "message"), "email"), "html", null, true);
        echo "
        at ";
        // line 10
        echo twig_escape_filter($this->env, $this->getAttribute($this->getContext($context, "message"), "ip"), "html", null, true);
        echo "
    </p>
    
    <!-- actions -->
    <ul>
        <li>
            <a href=\"";
        // line 16
        echo twig_escape_filter($this->env, $this->env->getExtension('routing')->getPath("admin_message_delete", array("id" => $this->getAttribute($this->getContext($context, "message"), "id"))), "html", null, true);
        echo "\">Delete</a>
        </li>
    </ul>
    <!-- /actions -->
    
</article>";
    }

    public function getTemplateName()
    {
        return "admin/message/_message.twig";
    }

    public function isTraitable()
    {
        return false;
    }

    public function getDebugInfo()
    {
        return array (  45 => 16,  36 => 10,  32 => 9,  26 => 6,  22 => 4,  19 => 2,);
    }
}
