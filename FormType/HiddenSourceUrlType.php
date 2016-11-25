<?php

namespace Keiwen\Cacofony\FormType;


use Symfony\Component\Form\Extension\Core\Type\HiddenType;

/**
 * Class HiddenSourceUrlType
 * With associated extension, this form type will contain an encoded url to redirect to
 * after form submission has been handled. If no url provided, original request url will be set.
 * If you want to redirect to predecessor page, you could use the HTTP header referer and
 * the controller method redirectToReferer
 */
class HiddenSourceUrlType extends HiddenType
{
    public function getParent()
    {
        return HiddenType::class;
    }


}
