<?php

namespace Keiwen\Cacofony\FormType;


use Symfony\Component\Form\Extension\Core\Type\HiddenType;

/**
 * Class HiddenSourceUrlType
 * With associated extension, this form type will contain an encoded url to redirect to
 * after form submission has been handled. If no url provided, original request url will be set.
 * When you use a form in many place, and you want to redirect to same page after handling submission,
 * you can use data from this type
 */
class HiddenSourceUrlType extends HiddenType
{
    public function getParent()
    {
        return HiddenType::class;
    }


}
