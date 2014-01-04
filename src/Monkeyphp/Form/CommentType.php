<?php
/**
 * CommentType.php
 */
namespace Monkeyphp\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Component\Validator\Constraints\NotBlank;

/**
 * Description of CommentType
 *
 * @author David White <david@monkeyphp.com>
 */
class CommentType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('article', 'hidden', array(
            'required' => true,
            'trim'     => true,
            'data'     => $options['article']
        ));
        
        $builder->add('email', 'email', array (
            'label' => 'Email',
            'required' => true,
            'trim' => true,
            'constraints' => array(
                new NotBlank()
            )
        ));
        
        $builder->add('body', 'textarea', array (
            'label' => 'Comment',
            'required' => true,
            'trim' => true,
            'constraints' => array(
                new NotBlank()
            )
        ));
        
        $builder->add('submit', 'submit', array (
            'label' => 'Submit'
        ));
    }
    
    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'article' => null
        ));
    }
    
    public function getName()
    {
        return 'comment';
    }

}
