<?php

namespace DataProcessingProjectBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;


use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;

use DataProcessingProjectBundle\Form\AdvertType;

class ApplicationType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add( 'firstName', TextType::class )
            ->add( 'lastName', TextType::class )
            //->add( 'applicationDate', DateTimeType::class, array( 'format' => 'yyyy-MM-dd') )
            ->add( 'departureDate', DateType::class )
            ->add( 'arrivalDate', DateType::class )
            ->add( 'nbPeople', IntegerType::class )
            ->add( 'choiceNumber', IntegerType::class )
            ->add( 'save', SubmitType::class )
            ;
    }
    
    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'DataProcessingProjectBundle\Entity\Application'
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'dataprocessingprojectbundle_application';
    }


}
