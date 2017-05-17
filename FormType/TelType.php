<?php

namespace Keiwen\Cacofony\FormType;

use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\AbstractType;


class TelType extends TextType
{

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'tel';
    }

}