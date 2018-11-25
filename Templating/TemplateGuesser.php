<?php
namespace Keiwen\Cacofony\Templating;

use Symfony\Component\HttpFoundation\Request;

class TemplateGuesser extends \Sensio\Bundle\FrameworkExtraBundle\Templating\TemplateGuesser
{

    /**
     * {@inheritdoc}
     */
    public function guessTemplateName($controller, Request $request)
    {
        $templateName = parent::guessTemplateName($controller, $request);
        // remove our bundle if added
        return str_replace('@KeiwenCacofony/', '', $templateName);
    }

}
