<?php

namespace DataProcessingProjectBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;


use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;

use Symfony\Component\Form\Extension\Core\Type\MoneyType;

use DataProcessingProjectBundle\Form\ImageType;
use DataProcessingProjectBundle\Form\AdvertType;
use DataProcessingProjectBundle\Form\ActivityType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;

class AdvertPreexistingActivityType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            //->remove( 'activity')
            ->add( 'activity', EntityType::class, array(
                'class' => 'DataProcessingProjectBundle:Activity',
                'choice_label' => 'name',
                'multiple' => true, 
                ))
            ;
    }
    
    
    public function getParent(){

        return AdvertActivityType::class;
    }


    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'dataprocessingprojectbundle_advert_preexisting_activity';
    }


}
