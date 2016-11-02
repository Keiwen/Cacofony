<?php


namespace Keiwen\Cacofony\ParamFetcher\Annotation;

use Symfony\Component\HttpFoundation\Request;


/**
 * @Annotation
 * @Target({"CLASS", "METHOD"})
 */
class PostParam extends RequestParam
{
    /** @var bool */
    public $required = true;
    /** @var bool */
    public $allowBlank = false;


    /**
     * @inheritdoc
     */
    protected function retrieveValueInRequest(Request $request, $default = null)
    {
        return $request->request->get($this->getName(), $default);
    }


}
