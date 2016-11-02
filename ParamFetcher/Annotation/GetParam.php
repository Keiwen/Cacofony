<?php


namespace Keiwen\Cacofony\ParamFetcher\Annotation;

use Symfony\Component\HttpFoundation\Request;


/**
 * @Annotation
 * @Target({"CLASS", "METHOD"})
 */
class GetParam extends RequestParam
{


    /**
     * @inheritdoc
     */
    protected function retrieveValueInRequest(Request $request, $default = null)
    {
        return $request->query->get($this->getName(), $default);
    }


}
