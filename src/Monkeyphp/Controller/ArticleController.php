<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
namespace Monkeyphp\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Description of ArticleController
 *
 * @author David White <david@monkeyphp.com>
 */
class ArticleController
{
    public function indexAction(Request $request)
    {
        $html = '<p>I am the Article index template</p>';
        
        return new Response($html, 200, array());
    }
}
