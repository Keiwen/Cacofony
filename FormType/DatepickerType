<?php

namespace Keiwen\Cacofony\FormType;


use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\OptionsResolver\OptionsResolver;

class DatepickerType extends AbstractType
{

    public function getParent()
    {
        return DateType::class;
    }


    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'widget' => 'single_text',
            'html5' => false,
            'attr' => array('class' => 'datepicker'),
        ));
    }



}
