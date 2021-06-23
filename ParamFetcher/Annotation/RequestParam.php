<?php


namespace Keiwen\Cacofony\ParamFetcher\Annotation;

use Keiwen\Utils\Sanitize\StringSanitizer;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Validator\Constraints;
use Symfony\Component\Validator\Constraint;


/**
 * @Annotation
 * @Target({"CLASS", "METHOD"})
 */
class RequestParam
{


    /** @var string */
    public $name;
    /** @var mixed */
    public $default = null;
    /** @var string */
    public $description = null;
    /** @var bool */
    public $required = false;
    /** @var bool */
    public $allowBlank = true;
    /** @var string */
    public $filter = null;
    /** @var string */
    public $constraintRegex = null;


    /**
     * Parameter name
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @return mixed
     */
    public function getDefault()
    {
        return $this->default;
    }

    /**
     * @return string
     */
    public function getDescription()
    {
        return $this->description;
    }


    /**
     * @return Constraint[]
     */
    public function getConstraints()
    {
        $constraints = array();
        if($this->required) {
            $constraints[] = new Constraints\NotNull();
        }
        if(!$this->allowBlank) {
            $constraints[] = new Constraints\NotBlank();
        }

        if(!empty($this->constraintRegex)) {
            $constraints[] = new Constraints\Regex(array(
                'pattern' => '#^(?:'.$this->constraintRegex.')$#xsu',
                'message' => sprintf(
                    'Parameter \'%s\' value does not match requirements \'%s\'',
                    $this->getName(),
                    $this->constraintRegex
                ),
            ));
        }

        return $constraints;
    }


    /**
     * @param Request $request
     * @param mixed   $default value
     * @return mixed
     */
    public function getValue(Request $request, $default = null)
    {
        $value = $this->retrieveValueInRequest($request, $default);

        if(!empty($this->filter) && $value !== null) {
            $value = (new StringSanitizer())->get($value, $this->filter);
        }
        return $value;
    }


    /**
     * @param Request $request
     * @param null    $default
     * @return mixed
     */
    protected function retrieveValueInRequest(Request $request, $default = null)
    {
        return $request->get($this->getName(), $default);
    }


}
