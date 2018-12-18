<?php

namespace adminBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;

class CategoryType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('code', TextType::class, array("label"=>"Codigo","required" => "required", "attr" => array(
                "class" => "form-name form-control")))
            ->add('name', TextType::class, array("label"=>"Nombre","required" => "required", "attr" => array(
                "class" => "form-name form-control")))
            ->add('description', TextareaType::class, array("label"=>"Descripcion","required" => "required", "attr" => array(
                "class" => "form-name form-control")))
            ->add('active',  ChoiceType::class, array("label" => "Estado", "required" => "required", "choices" => array(
                "Activo" => "1",
                "Desactivado" => "2"
            ), "attr" => array(
                "class" => "form-name form-control")
            ))
            ->add('guardar', SubmitType::class, array(
                'attr' => array('class' => 'form-submit btn btn-primary ')))
        ;
    }
    
    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'adminBundle\Entity\Category'
        ));
    }
}
