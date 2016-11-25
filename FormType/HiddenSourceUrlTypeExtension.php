<?php

namespace Keiwen\Cacofony\FormType;


use Keiwen\Cacofony\Http\Request;
use Symfony\Component\Form\AbstractTypeExtension;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;

class HiddenSourceUrlTypeExtension extends AbstractTypeExtension
{

    protected $requestUrl;

    public function __construct(Request $request)
    {
        $this->requestUrl = urlencode($request->getUrl(true, true));
    }


    public function getExtendedType()
    {
        return HiddenSourceUrlType::class;
    }

    public function buildView(FormView $view, FormInterface $form, array $options)
    {
        parent::buildView($view, $form, $options);
        $data = $form->getData();
        //if data set, do not change
        if(!empty($data)) return;
        //if data empty, put current url
        $view->vars['value'] = $this->requestUrl;
    }


}
