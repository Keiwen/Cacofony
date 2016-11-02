<?php

namespace Keiwen\Cacofony\Route\Annotation;

/**
 * @Annotation
 * @Target("METHOD")
 */
class Post extends SingleMethodRoute
{

    protected static $singleMethod = 'POST';

}