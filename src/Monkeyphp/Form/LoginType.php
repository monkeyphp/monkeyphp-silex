<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
namespace Monkeyphp\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;
/**
 * Description of LoginType
 *
 * @author davidwhite
 */
class LoginType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('_username', 'text', array (
            'label' => 'Username',
            'required' => true,
            'trim' => true,
            'constraints' => array(
                new NotBlank(),
                new Length(array('min'=> 8))
            )
        ));

        $builder->add('_password', 'password', array (
            'label' => 'Password',
            'required' => true,
            'trim' => true,
            'constraints' => array(
                new NotBlank(),
                new Length(array('min'=> 8))
            )
        ));

        $builder->add('submit', 'submit', array(
            'label' => 'Login',
        ));
    }

    public function getName()
    {
        return 'login';
    }

}
