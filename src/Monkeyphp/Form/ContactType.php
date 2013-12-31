<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
namespace Monkeyphp\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints\NotBlank;

/**
 * Description of ContactType
 *
 * @author davidwhite
 */
class ContactType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('email', 'email', array (
            'label' => 'Email',
            'required' => true,
            'trim' => true,
            'constraints' => array(
                new NotBlank()
            )
        ));

        $builder->add('message', 'textarea', array (
            'label' => 'Message',
            'required' => true,
            'trim' => true,
            'constraints' => array(
                new NotBlank()
            )
        ));

        $builder->add('submit', 'submit', array (
            'label' => 'Send'
        ));
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'contact';
    }
}
