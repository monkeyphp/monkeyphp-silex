<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
namespace Monkeyphp\Entity;
/**
 * Description of AbstractEntity
 *
 * @author David White <david@monkeyphp.com>
 */
abstract class AbstractEntity
{
    protected $id;
    
    protected $created;
    
    protected $modified;
    
    public function __construct(array $options = array())
    {
        $this->setOptions($options);
    }
    
    public function setOptions(array $options = array())
    {
        $methods = get_class_methods($this);
        
        foreach ($options as $key => $value) {
            $method = 'set' . ucfirst($key);
            if (in_array($method, $methods)) {
                $this->$method($value);
            }
        }
        return $this;
    }
    
    public function getId()
    {
        return $this->id;
    }

    public function getCreated()
    {
        return $this->created;
    }

    public function getModified()
    {
        return $this->modified;
    }

    public function setId($id)
    {
        $this->id = $id;
        return $this;
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
}
