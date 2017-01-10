<?php

namespace Keiwen\Cacofony\FormType;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Keiwen\Cacofony\FormType\DatepickerType;

class DatepickerRangeType extends AbstractType
{



    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('dateFrom', DatepickerType::class);
        $builder->add('dateTo', DatepickerType::class);
    }

}
