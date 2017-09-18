<?php
namespace Keiwen\Cacofony\Configuration;


use Sensio\Bundle\FrameworkExtraBundle\Configuration\ConfigurationAnnotation;

/**
 * Class TemplateParam
 *
 * Use this annotation to add some template parameters controller-wide,
 * like if you need a common parameters for all actions in same controller
 *
 * @Annotation
 * @Target({"CLASS", "METHOD"})
 */
class TemplateParam extends ConfigurationAnnotation
{

    const ALIAS_NAME = 'cacoTemplateParam';

    protected $parameter;
    protected $paramValue = true;


    /**
     * @param string $parameter The template parameter name
     */
    public function setValue($parameter)
    {
        $this->parameter = $parameter;
    }

    /**
     * @return string The template parameter name
     */
    public function getValue()
    {
        return $this->parameter;
    }

    /**
     * @param mixed $paramValue The template parameter value
     */
    public function setParamValue($paramValue)
    {
        $this->paramValue = $paramValue;
    }

    /**
     * @return mixed
     */
    public function getParamValue()
    {
        return $this->paramValue;
    }

    /**
     * Returns the annotation alias name.
     * @return string
     * @see ConfigurationInterface
     */
    public function getAliasName()
    {
        return self::ALIAS_NAME;
    }


    /**
     * Allow multiple directive.
     * @return bool
     * @see ConfigurationInterface
     */
    public function allowArray()
    {
        return true;
    }


}
