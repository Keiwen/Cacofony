<?php

namespace Keiwen\Cacofony\EntitiesManagement;


class Assimilator
{

    protected $entityRegistry;

    /**
     * Assimilator constructor.
     *
     * @param EntityRegistry $entityRegistry
     */
    public function __construct(EntityRegistry $entityRegistry)
    {
        $this->entityRegistry = $entityRegistry;
    }

}
