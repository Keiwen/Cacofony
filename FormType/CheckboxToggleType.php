<?php

namespace Keiwen\Cacofony\FormType;


use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\OptionsResolver\OptionsResolver;


/**
 * Create input for bootstrap toggle
 * @see http://www.bootstraptoggle.com/
 *
 * @package Keiwen\Cacofony\FormType
 */
class CheckboxToggleType extends AbstractType
{


    public function getParent()
    {
        return CheckboxType::class;
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
    }



    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefined(array('label_on', 'label_off', 'style_on', 'style_off', 'style'));
        $resolver->setDefaults(array(
            'label_on' => 'On',
            'label_off' => 'Off',
            'style_on' => 'success',
            'style_off' => 'danger',
        ));
    }


    public function buildView(FormView $view, FormInterface $form, array $options)
    {
        parent::buildView($view, $form, $options);
        $view->vars['label_on'] = $options['label_on'];
        $view->vars['label_off'] = $options['label_off'];
        $view->vars['style_on'] = $options['style_on'];
        $view->vars['style_off'] = $options['style_off'];
        if(isset($options['style'])) $view->vars['style'] = $options['style'];
    }


}
