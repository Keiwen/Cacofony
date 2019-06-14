<?php

namespace Keiwen\Cacofony\Reader;


use Doctrine\Common\Annotations\AnnotationReader;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class TemplateAnnotationReader
{

    protected $rController;
    protected $bundleName;
    protected $defaultTemplateExtension = '.html.twig';
    protected $methodSuffix = 'Action';

    public function __construct(AbstractController $controller, string $bundleName = 'AppBundle')
    {
        $this->rController = new \ReflectionClass($controller);
        $this->bundleName = $bundleName;
    }


    /**
     * @param string $defaultTemplateExtension
     */
    public function setDefaultTemplateExtension(string $defaultTemplateExtension)
    {
        $this->defaultTemplateExtension = $defaultTemplateExtension;
    }

    /**
     * @param string $methodSuffix
     */
    public function setMethodSuffix(string $methodSuffix)
    {
        $this->methodSuffix = $methodSuffix;
    }


    /**
     * @param string $methodName
     * @return string
     */
    public function getTemplateFromAnnotation(string $methodName)
    {
        $method = $this->rController->getMethod($methodName);
        if(empty($method)) return '';
        $annotationReader = new AnnotationReader();
        $annotations = $annotationReader->getMethodAnnotations($method);
        foreach($annotations as $annot) {
            if(!$annot instanceof Template) continue;
            $template = $annot->getTemplate();
            break;
        }
        if(empty($template)) $template = '';
        return $this->buildTemplateName($template, $methodName);
    }


    /**
     * @param string $annotatedTemplate
     * @param string $methodName
     * @return string
     */
    protected function buildTemplateName(string $annotatedTemplate, string $methodName)
    {
        if(strpos($annotatedTemplate, '@') !== false || strpos($annotatedTemplate, 'Bundle:') !== false) {
            //namespace path or bundle path defined, dont change
            return $annotatedTemplate;
        }
        $template = str_replace('/', ':', $annotatedTemplate);
        if(empty($template)) {
            //default template name from method name
            $template = preg_replace('#(.*)('.$this->methodSuffix.')$#', '${1}', $methodName);
            $template .= $this->defaultTemplateExtension;
        } else {
            //check that extension provided
            $extension = preg_replace('#(.*)\.(.*)$#', '${2}', $template);
            if(empty($extension) || $extension == $template) $template .= $this->defaultTemplateExtension;
        }

        if(strpos($template, ':') !== false) {
            //path defined, just add bundle name
            return $this->bundleName . ':' . $template;
        } else {
            //set default path
            $path = $this->rController->getName();
            $path = explode('\\', $path);
            $path = array_pop($path);
            $path = preg_replace('#(.*)(Controller)$#', '${1}', $path);

            return implode(':', array($this->bundleName, lcfirst($path), $template));
        }
    }


}
