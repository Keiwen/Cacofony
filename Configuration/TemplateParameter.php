<?php
namespace Keiwen\Cacofony\Configuration;


use Sensio\Bundle\FrameworkExtraBundle\Configuration\ConfigurationAnnotation;
use Symfony\Component\HttpFoundation\Request;

/**
 * Class TemplateParameter
 *
 * Use this attribute to add some template parameters controller-wide,
 * like if you need a common parameters for all actions in same controller
 */
#[\Attribute(\Attribute::IS_REPEATABLE | \Attribute::TARGET_CLASS | \Attribute::TARGET_METHOD)]
class TemplateParameter
{

    const REQUEST_ATTRIBUTE_NAME = '_cacoTemplateParameter';

    protected $name;
    protected $value;


    public function __construct(string $name, $value = true)
    {
        $this->name = $name;
        $this->value = $value;
    }

    /**
     * @param string $parameter The template parameter name
     */
    public function setName($parameter)
    {
        $this->name = $parameter;
    }

    /**
     * @return string The template parameter name
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @param mixed $paramValue The template parameter value
     */
    public function setValue($paramValue)
    {
        $this->value = $paramValue;
    }

    /**
     * @return mixed
     */
    public function getValue()
    {
        return $this->value;
    }

    public static function getArrayFromRequest(Request $request)
    {
        $templateParameters = array();
        /** @var TemplateParameter[] $fromRequest */
        $fromRequest = $request->attributes->get(self::REQUEST_ATTRIBUTE_NAME, array());
        if (!$fromRequest) return array();
        foreach($fromRequest as $tpFromRequest) {
            $templateParameters[$tpFromRequest->getName()] = $tpFromRequest->getValue();
        }
        return $templateParameters;
    }

}
