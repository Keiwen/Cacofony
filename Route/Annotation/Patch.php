<?php

namespace Keiwen\Cacofony\Route\Annotation;

/**
 * @Annotation
 * @Target("METHOD")
 */
class Patch extends SingleMethodRoute
{

    protected static $singleMethod = 'PATCH';

}