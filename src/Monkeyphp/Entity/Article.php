<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
namespace Monkeyphp\Entity;
/**
 * Description of Article
 *
 * @author David White <david@monkeyphp.com>
 */
class Article extends AbstractEntity
{
    protected $category;
    
    protected $title;
    
    protected $tags;
    
    protected $summary;
    
    protected $body;
    
    protected $published;
    
    protected $slug;
    
    public function getCategory()
    {
        return $this->category;
    }

    public function getTitle()
    {
        return $this->title;
    }

    public function getTags()
    {
        return $this->tags;
    }

    public function getSummary()
    {
        return $this->summary;
    }

    public function getBody()
    {
        return $this->body;
    }

    public function getPublished()
    {
        return $this->published;
    }

    public function getSlug()
    {
        return $this->slug;
    }

    public function setCategory($category)
    {
        $this->category = $category;
        return $this;
    }

    public function setTitle($title)
    {
        $this->title = $title;
        return $this;
    }

    public function setTags($tags)
    {
        $this->tags = $tags;
        return $this;
    }

    public function setSummary($summary)
    {
        $this->summary = $summary;
        return $this;
    }

    public function setBody($body)
    {
        $this->body = $body;
        return $this;
    }

    public function setPublished($published)
    {
        $this->published = $published;
        return $this;
    }

    public function setSlug($slug)
    {
        $this->slug = $slug;
        return $this;
    }


    
}
