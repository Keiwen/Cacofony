<?php

namespace Keiwen\Cacofony\Twig;

use Doctrine\Common\Collections\Collection;
use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;

class TwigArray extends AbstractExtension
{

    /**
     * @return array
     */
    public function getFilters(): array
    {
        return array(
            new TwigFilter('shuffle', array($this, 'shuffleFilter')),
        );
    }


    /**
     * Shuffle input array
     * @param array|Collection  $array
     * @return array
     */
    public function shuffleFilter(array $array)
    {
        if ($array instanceof Collection) {
            $shuffledArray = $array->toArray();
        } else {
            $shuffledArray = $array;
        }
        shuffle($shuffledArray);
        return $shuffledArray;
    }


}
