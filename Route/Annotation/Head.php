<?php

namespace Keiwen\Cacofony\Route\Annotation;

/**
 * @Annotation
 * @Target("METHOD")
 */
class Head extends SingleMethodRoute
{

    protected static $singleMethod = 'HEAD';

}