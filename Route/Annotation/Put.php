<?php

namespace Keiwen\Cacofony\Route\Annotation;

/**
 * @Annotation
 * @Target("METHOD")
 */
class Put extends SingleMethodRoute
{

    protected static $singleMethod = 'PUT';

}