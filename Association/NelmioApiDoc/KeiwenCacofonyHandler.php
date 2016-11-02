<?php


namespace Keiwen\Cacofony\Association\NelmioApiDoc;

use Keiwen\Cacofony\ParamFetcher\Annotation\GetParam;
use Keiwen\Cacofony\ParamFetcher\Annotation\PostParam;
use Keiwen\Cacofony\ParamFetcher\Annotation\RequestParam;
use Nelmio\ApiDocBundle\DataTypes;
use Nelmio\ApiDocBundle\Extractor\HandlerInterface;
use Nelmio\ApiDocBundle\Annotation\ApiDoc;
use Symfony\Component\Routing\Route;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\Constraints\Regex;

class KeiwenCacofonyHandler implements HandlerInterface
{
    /**
     * @inheritdoc
     */
    public function handle(ApiDoc $annotation, array $annotations, Route $route, \ReflectionMethod $method)
    {
        foreach ($annotations as $annot) {
            if(!$annot instanceof RequestParam) continue;

            $data = array(
                'required'    => $annot->required && $annot->default === null,
                'dataType'    => $this->determineType($annot),
                'description' => $annot->description,
                'default'     => $annot->default,
                'format'      => $annot->constraintRegex,
            );

            if($annot instanceof PostParam && $annot->required) {
                $data['requirement'] = $data['format'];
                unset($data['format']);
                $annotation->addRequirement($annot->name, $data);
            } elseif($annot instanceof GetParam && !$annot->required) {
                foreach($data as $key => $value) {
                    if(empty($value)) unset($data[$key]);
                }
                $annotation->addFilter($annot->name, $data);
            } else {
                $annotation->addParameter($annot->name, $data);
            }
        }

    }


    /**
     * @param RequestParam $annot
     * @return string
     */
    protected function determineType(RequestParam $annot)
    {
        if(!empty($annot->filter)) return $annot->filter;
        if(!empty($annot->constraintRegex)) {
            return 'regex';
        }
        return 'string';

    }

}
