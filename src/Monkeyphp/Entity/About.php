<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
namespace Monkeyphp\Entity;
/**
 * Description of About
 *
 * @author David White <david@monkeyphp.com>
 */
class About extends AbstractEntity
{
    /**
     * The body content of the About entity
     * 
     * @var string
     */
    protected $body;
    
    public function getBody()
    {
        return $this->body;
    }

    public function setBody($body)
    {
        $this->body = $body;
        return $this;
    }
}
