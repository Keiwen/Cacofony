<?php

namespace Keiwen\Cacofony\Route\Annotation;


use Symfony\Component\Routing\Annotation\Route;

class SingleMethodRoute extends Route
{

    protected static $singleMethod = 'ANY';

    /**
     * @inheritdoc
     */
    public function getMethods()
    {
        return array(static::$singleMethod);
    }
}
