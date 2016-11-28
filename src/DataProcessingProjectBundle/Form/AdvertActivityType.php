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

class AdvertActivityType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add( 'price', MoneyType::class )
            ->add( 'location', TextType::class )
            ->add( 'activity', ActivityType::class )
            ->add( 'save', SubmitType::class )
            ;
    }
    
    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'DataProcessingProjectBundle\Entity\AdvertActivity'
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'dataprocessingprojectbundle_advert_activity';
    }


}
