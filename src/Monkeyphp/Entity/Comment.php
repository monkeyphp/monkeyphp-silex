<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
namespace Monkeyphp\Entity;
/**
 * Description of Comment
 *
 * @author David White <david@monkeyphp.com>
 */
class Comment extends AbstractEntity
{
    protected $created;
    protected $modified;
    protected $body;
    protected $ip;
    protected $email;
    protected $published;
    
    public function getCreated()
    {
        return $this->created;
    }

    public function getModified()
    {
        return $this->modified;
    }

    public function getBody()
    {
        return $this->body;
    }

    public function getIp()
    {
        return $this->ip;
    }

    public function getEmail()
    {
        return $this->email;
    }

    public function getPublished()
    {
        return $this->published;
    }

    public function setCreated($created)
    {
        $this->created = $created;
        return $this;
    }

    public function setModified($modified)
    {
        $this->modified = $modified;
        return $this;
    }

    public function setBody($body)
    {
        $this->body = $body;
        return $this;
    }

    public function setIp($ip)
    {
        $this->ip = $ip;
        return $this;
    }

    public function setEmail($email)
    {
        $this->email = $email;
        return $this;
    }

    public function setPublished($published)
    {
        $this->published = $published;
        return $this;
    }


}
