<?php

namespace Keiwen\Cacofony\Route\Annotation;

/**
 * @Annotation
 * @Target("METHOD")
 */
class Options extends SingleMethodRoute
{

    protected static $singleMethod = 'OPTIONS';

}