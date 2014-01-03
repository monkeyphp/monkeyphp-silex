<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
namespace Monkeyphp\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Component\Validator\Constraints\NotBlank;

/**
 * Description of ArticleType
 *
 * @author David White <david@monkeyphp.com>
 */
class ArticleType extends AbstractType
{
    /**
     * 
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        // title
        $builder->add('title', 'text', array(
            'label'       => 'Title', 
            'required'    => true,
            'trim'        => true,
            'constraints' => array(
                new NotBlank()
            )
        ));
        
        // category
        $builder->add('category', 'choice', array(
            'label' => 'Category', 
            'required' => true,
            'choices' => $options['categories'],
            'multiple' => false
        ));
        
        // summary
        $builder->add('summary', 'textarea', array(
            'label'       => 'Summary',
            'required'    => true,
            'trim'        => true,
            'constraints' => array(
                new NotBlank()
            )
        ));
        
        // body
        $builder->add('body', 'textarea', array(
            'label'       => 'Body',
            'required'    => true,
            'trim'        => true,
            'constraints' => array(
                new NotBlank()
            )
        ));
        
        // tags
        $builder->add('tags', 'choice', array(
            'label'    => 'Tags',
            'multiple' => true,
            'required' => false,
            'choices'  => $options['tags']
            
        ));
        
        // published
        $builder->add('published', 'checkbox', array(
            'label'    => 'Published',
            'required' => false, 
            'value'    => '1'
        ));
        
        // slug(s)
        $builder->add('slug', 'text', array(
            'label'    => 'Slug', 
            'required' => true,
            'trim'     => true
        ));
        
        // submit
        $builder->add('submit', 'submit', array());
    }
    
    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'tags' => array(
                
            ), 
            'categories' => array(
                
            )
        ));
    }
    
    public function getName()
    {
        return 'article';
    }
}
