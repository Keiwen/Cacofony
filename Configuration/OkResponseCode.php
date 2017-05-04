<?php

namespace Keiwen\Cacofony\Configuration;


use Sensio\Bundle\FrameworkExtraBundle\Configuration\ConfigurationAnnotation;

/**
 * Class OkResponseCode
 * Overwrite the HTTP status code if initially 200
 *
 * Use this annotation when you don't create response object directly,
 * for example if your action return an array using template annotation
 *
 * If you want to set another success code (let's say 202 Accepted), just add
 * this annotation to your action (could be controller with care)
 *
 * You can set any other code, even 500, but then it would not be handled as a real
 * Symfony error. Check if you can resolve your case with exception controller first
 *
 * @package Keiwen\Cacofony\Configuration
 * @Annotation
 * @Target({"CLASS", "METHOD"})
 */
class OkResponseCode extends ConfigurationAnnotation
{

    protected $httpCode = 200;

    public function getAliasName()
    {
        return 'okResponseCode';
    }

    public function allowArray()
    {
        return false;
    }

    /**
     * @param int $code
     */
    public function setValue($code)
    {
        $this->httpCode = $code;
    }

    /**
     * Returns the status code.
     *
     * @return int
     */
    public function getCode()
    {
        return $this->httpCode;
    }


}
