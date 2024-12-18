<?php
namespace Keiwen\Cacofony\EntitiesManagement;

use Doctrine\Persistence\ManagerRegistry;
use Doctrine\Persistence\ObjectManager;

class EntityRegistry
{

    /** @var ManagerRegistry */
    private $managerRegistry;

    private $managerInstances = array();


    const OPERATION_SAVE = 'save';
    const OPERATION_REMOVE = 'remove';
    const OPERATION_DETACH = 'detach';


    /**
     * EntityRegistry constructor.
     *
     * @param ManagerRegistry $managerRegistry
     */
    public function __construct(ManagerRegistry $managerRegistry)
    {
        $this->managerRegistry = $managerRegistry;
    }


    /**
     * @param string $objectClass
     * @return \Doctrine\Common\Persistence\ObjectRepository
     */
    public function getRepository(string $objectClass)
    {
        if(empty($this->managerRegistry)) return null;
        return $this->managerRegistry->getRepository($objectClass);
    }


    /**
     * @param string $className
     * @return ObjectManager|null
     */
    public function getManagerForClass(string $className)
    {
        if(empty($this->managerRegistry)) return null;
        if(isset($this->managerInstances[$className])) return $this->managerInstances[$className];
        $manager = $this->managerRegistry->getManagerForClass($className);
        $this->managerInstances[$className] = $manager;
        return $manager;
    }


    /**
     * @param string $operation
     * @param        $object
     * @param bool $commit
     * @param ObjectManager|null $manager
     * @return bool
     */
    private function operateObject(string $operation,
                                   $object,
                                   bool $commit = true,
                                   ?ObjectManager &$manager = null)
    {
        if(empty($manager)) {
            $manager = $this->getManagerForClass(get_class($object));
        }
        if(empty($manager)) return false;
        switch($operation) {
            case self::OPERATION_SAVE:
                $manager->persist($object);
                break;
            case self::OPERATION_REMOVE:
                $manager->remove($object);
                break;
            case self::OPERATION_DETACH:
                $manager->detach($object);
                break;
            default:
                return false;
        }
        if($commit) {
            $manager->flush();
        }
        return true;
    }


    /**
     * @param string $operation
     * @param object[] $objectList
     * @param bool $commit
     * @param ObjectManager[] $managerList
     * @return bool
     */
    private function operateObjectList(string $operation,
                                    array $objectList,
                                    bool $commit = true,
                                    array &$managerList = array())
    {
        /** @var ObjectManager[] $managerList */

        foreach($objectList as $object) {
            $manager = null;
            $operated = $this->operateObject($operation, $object, false, $manager);
            if(!$operated) return false;
            if(!isset($managerList[get_class($manager)])) {
                $managerList[get_class($manager)] = $manager;
            }
        }
        if($commit) {
            foreach($managerList as $manager) {
                $manager->flush();
            }
        }
        return true;
    }


    /**
     * @param      $object
     * @param bool $commit
     * @return bool
     */
    public function saveObject($object, bool $commit = true)
    {
        return $this->operateObject(self::OPERATION_SAVE, $object, $commit);
    }


    /**
     * @param array $objectList
     * @param bool $commit
     * @return bool
     */
    public function saveObjectList(array $objectList, bool $commit = true)
    {
        return $this->operateObjectList(self::OPERATION_SAVE, $objectList, $commit);
    }


    /**
     * @param      $object
     * @param bool $commit
     * @return bool
     */
    public function removeObject($object, bool $commit = true)
    {
        return $this->operateObject(self::OPERATION_REMOVE, $object, $commit);
    }


    /**
     * @param array $objectList
     * @param bool $commit
     * @return bool
     */
    public function removeObjectList(array $objectList, bool $commit = true)
    {
        return $this->operateObjectList(self::OPERATION_REMOVE, $objectList, $commit);
    }

    /**
     * @param  $object
     * @return bool
     */
    public function detachObject($object)
    {
        return $this->operateObject(self::OPERATION_DETACH, $object, false);
    }


    /**
     * @param array $objectList
     * @return bool
     */
    public function detachObjectList(array $objectList)
    {
        return $this->operateObjectList(self::OPERATION_DETACH, $objectList, false);
    }


    /**
     * @param  $object
     * @return null|object
     */
    public function copyObject($object)
    {
        $copy = clone($object);
        $detached = $this->detachObject($copy);
        if($detached) return $copy;
        return null;
    }

    /**
     * @param array $objectList
     * @return null|array
     */
    public function copyObjectList(array $objectList)
    {
        $copy = array();
        foreach($objectList as $object) {
            $copy[] = $this->copyObject($object);
        }
        return $copy;
    }




}
