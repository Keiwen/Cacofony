<?php

namespace Keiwen\Cacofony;

use Keiwen\Cacofony\DependencyInjection\Compiler\TranslatorPass;
use Symfony\Component\HttpKernel\Bundle\Bundle;
use Symfony\Component\DependencyInjection\ContainerBuilder;

class KeiwenCacofonyBundle extends Bundle
{

    public function build(ContainerBuilder $container)
    {
        parent::build($container);

        $container->addCompilerPass(new TranslatorPass());
    }


}
