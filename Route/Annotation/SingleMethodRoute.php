<?php

namespace Keiwen\Cacofony\Route\Annotation;


use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;

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
