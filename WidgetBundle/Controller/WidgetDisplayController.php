<?php

namespace Keiwen\Cacofony\WidgetBundle\Controller;


use Keiwen\Cacofony\Controller\AppController;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\HttpFoundation\Request;

class WidgetDisplayController extends AppController
{

    /**
     * @Route("/{widget}", name="displayWidget")
     * @Template("KeiwenCacofonyWidgetBundle:WidgetDisplay:displayWidget.html.twig")
     */
    public function displayWidgetAction(Request $request, $widget)
    {
        $this->addTemplateParam('widgetName', $widget);
        $this->addTemplateParam('widgetParam', $request->query->all());
        return $this->renderTemplate();
    }


}
