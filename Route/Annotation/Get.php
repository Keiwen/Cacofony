<?php

namespace Keiwen\Cacofony\Route\Annotation;

/**
 * @Annotation
 * @Target("METHOD")
 */
class Get extends SingleMethodRoute
{

    protected static $singleMethod = 'GET';

}