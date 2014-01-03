<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
namespace Monkeyphp\Twig;

use Michelf\Markdown;
use Twig_Extension;
use Twig_SimpleFilter;
/**
 * Description of Extension
 *
 * @author David White <david@monkeyphp.com>
 */
class Extension extends Twig_Extension
{
    public function getName()
    {
        return 'monkeyphp';
    }

    public function getFilters()
    {
        return array(
            new Twig_SimpleFilter('markdown', function($string) {
                return Markdown::defaultTransform($string);
            })
        );
    }
}
