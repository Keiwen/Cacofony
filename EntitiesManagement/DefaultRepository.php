<?php
namespace Keiwen\Cacofony\EntitiesManagement;

use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\Mapping\ClassMetadata;
use Doctrine\ORM\Query;

class DefaultRepository extends EntityRepository {

	//TODO keep it??
    public function __construct(EntityManager $em, ClassMetadata $class)
    {
        parent::__construct($em, $class);
    }


}