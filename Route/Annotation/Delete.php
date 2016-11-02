<?php

namespace Keiwen\Cacofony\Route\Annotation;

/**
 * @Annotation
 * @Target("METHOD")
 */
class Delete extends SingleMethodRoute
{

    protected static $singleMethod = 'DELETE';

}