<?php

namespace Keiwen\Cacofony\WidgetBundle\Controller;


use Keiwen\Cacofony\Controller\AppController;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;

class WidgetDisplayController extends AppController
{

    /**
     * @Route("/{widget}", name="displayWidget")
     * @Template("KeiwenCacofonyWidgetBundle:WidgetDisplay:displayWidget.html.twig")
     */
    public function displayWidgetAction($widget)
    {
        $this->addTemplateParam('widgetName', $widget);
        return $this->renderTemplate();
    }


}