<?php

namespace Keiwen\Cacofony\ParamFetcher;


use Keiwen\Cacofony\ParamFetcher\Annotation\RequestParam;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Doctrine\Common\Annotations\Reader;


class ParamReader
{

    /** @var Reader */
    protected $annotationReader;
    /** @var \ReflectionClass */
    protected $controllerReflection;
    /**
     * If true, annotations from method can overwrite annotations from class
     * @var bool
     */
    protected $methodOverClass = true;


    /**
     * ParamReader constructor.
     *
     * @param Reader     $annotationReader
     * @param Controller $controller
     * @param bool       $methodOverClass
     */
    public function __construct(Reader $annotationReader, Controller $controller, bool $methodOverClass = true)
    {
        if(empty($controller)) throw new \RuntimeException('Controller not defined');
        $this->annotationReader = $annotationReader;
        $this->controllerReflection = new \ReflectionClass($controller);
        $this->methodOverClass = $methodOverClass;

    }


    /**
     * Read parameters from class and method (if provided)
     * @param string $method
     * @return RequestParam[]
     */
    public function read($method = '')
    {
        $classParams = $this->getParamsFromClass();
        $methodParams = $this->getParamsFromMethod($method);
        return $this->methodOverClass ? array_merge($classParams, $methodParams) : array_merge($methodParams, $classParams);
    }


    /**
     * @return RequestParam[]
     */
    public function getParamsFromClass()
    {
        $annotations = $this->annotationReader->getClassAnnotations($this->controllerReflection);
        return $this->getParamsFromAnnotations($annotations);
    }


    /**
     * @param string $methodName
     * @return RequestParam[]
     */
    public function getParamsFromMethod(string $methodName)
    {
        if(!$this->controllerReflection->hasMethod($methodName)) return array();

        $method = $this->controllerReflection->getMethod($methodName);
        $annotations = $this->annotationReader->getMethodAnnotations($method);
        return $this->getParamsFromAnnotations($annotations);
    }

    /**
     * @param array $annotations
     * @return RequestParam[]
     */
    protected function getParamsFromAnnotations(array $annotations)
    {
        $params = array();
        foreach($annotations as $annot) {
            if($annot instanceof RequestParam) {
                $params[$annot->name] = $annot;
            }
        }
        return $params;
    }


}