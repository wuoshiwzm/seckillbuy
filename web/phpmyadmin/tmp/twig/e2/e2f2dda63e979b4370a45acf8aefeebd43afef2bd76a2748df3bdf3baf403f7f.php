<?php

/* console/toolbar.twig */
class __TwigTemplate_a0374e70010de8a653da0a476e0baa69053544140f1ed599a0e599f0278477c7 extends Twig_Template
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
        // line 1
        echo "<div class=\"toolbar ";
        echo twig_escape_filter($this->env, ($context["parentDivClasses"] ?? null), "html", null, true);
        echo "\">
    ";
        // line 2
        $context['_parent'] = $context;
        $context['_seq'] = twig_ensure_traversable(($context["contentArray"] ?? null));
        foreach ($context['_seq'] as $context["_key"] => $context["content"]) {
            // line 3
            echo "        ";
            if (array_key_exists("content", $context)) {
                // line 4
                echo "        <div class=\"";
                echo twig_escape_filter($this->env, $this->getAttribute($context["content"], 0, array(), "array"), "html", null, true);
                echo "\">
            ";
                // line 5
                echo (($this->getAttribute($context["content"], "image", array(), "array", true, true)) ? ($this->getAttribute($context["content"], "image", array(), "array")) : (""));
                echo "
            <span>";
                // line 6
                echo twig_escape_filter($this->env, $this->getAttribute($context["content"], 1, array(), "array"), "html", null, true);
                echo "</span>
        </div>
        ";
            }
            // line 9
            echo "    ";
        }
        $_parent = $context['_parent'];
        unset($context['_seq'], $context['_iterated'], $context['_key'], $context['content'], $context['_parent'], $context['loop']);
        $context = array_intersect_key($context, $_parent) + $_parent;
        // line 10
        echo "</div>
";
    }

    public function getTemplateName()
    {
        return "console/toolbar.twig";
    }

    public function isTraitable()
    {
        return false;
    }

    public function getDebugInfo()
    {
        return array (  52 => 10,  46 => 9,  40 => 6,  36 => 5,  31 => 4,  28 => 3,  24 => 2,  19 => 1,);
    }

    /** @deprecated since 1.27 (to be removed in 2.0). Use getSourceContext() instead */
    public function getSource()
    {
        @trigger_error('The '.__METHOD__.' method is deprecated since version 1.27 and will be removed in 2.0. Use getSourceContext() instead.', E_USER_DEPRECATED);

        return $this->getSourceContext()->getCode();
    }

    public function getSourceContext()
    {
        return new Twig_Source("", "console/toolbar.twig", "D:\\projs\\seckillbuy\\web\\phpmyadmin\\templates\\console\\toolbar.twig");
    }
}
