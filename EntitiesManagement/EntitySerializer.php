<?php

namespace Keiwen\Cacofony\EntitiesManagement;

use Doctrine\ORM\PersistentCollection;

class EntitySerializer
{

	protected static $proxiesNames = array('Proxies\\__CG__\\');
	protected static $getterNames = array('get', 'has', 'is');
    protected static $classDoctrineCollection = 'Doctrine\\ORM\\PersistentCollection';


    /**
     * @param $object
     * @return bool
     */
    public static function isObjectValid($object)
    {
        if(!is_object($object)) return false;
        $proxyClass = get_class($object);
        if(static::detectProxy($proxyClass)) return true;
        if(!in_array(ExportableEntityTrait::class, class_uses($object))) return false;
        return true;
    }

	/**
	 * @param ExportableEntityTrait $object
	 * @param array $labels
	 * @param array $blackList
	 * @param array $whiteList
	 * @return array
	 */
	public static function object2Array($object, array $labels = array(), array $blackList = array(), array $whiteList = array())
    {
        if(!static::isObjectValid($object)) return array();
		return static::processObject2ArrayConversion($object, array(), true, $labels, $blackList, $whiteList);
	}
	
	/**
	 * @param ExportableEntityTrait $object
	 * @param array $labels
	 * @param array $blackList
	 * @param array $whiteList
	 * @return string
	 */
	public static function object2Json($object, array $labels = array(), array $blackList = array(), array $whiteList = array())
    {
		return json_encode(static::object2Array($object, $labels, $blackList, $whiteList));
	}
	
	
	/**
	 * ITERATIVE class to convert an entity object to array
	 * If contains other Entity object, these ones will be converted as well
	 * A class is converted only once per process to avoid infinite nesting
	 * We can ignore the initial class
	 *
	 * @param ExportableEntityTrait $object
	 * @param array $processed
	 * @param bool $skipFirst
	 * @param array $labels
	 * @param array $blackList
	 * @param array $whiteList
	 * @return array|null
	 */
	protected static function processObject2ArrayConversion(
                $object,
                array $processed = array(),
                bool $skipFirst = false,
                array $labels = array(),
                array $blackList = array(),
                array $whiteList = array())
    {

		$class = get_class($object);
		$proxy = static::detectProxy($class);

		//check if class already processed
		if(in_array($class, $processed)) {
			return null;
		}
		//add to process
		if(!empty($processed) || !$skipFirst) {
			$processed[] = $class;
		}


		//set labels
		$fieldsLabel = $object::retrieveExportFieldLabels();
		foreach($labels as $fieldName => $label) {
			$fieldsLabel[$fieldName] = $label;
		}

		//determine field list
		$fieldList = $object::retrieveExportFields();
		if(!empty($whiteList)) {
			$fieldList = $whiteList;
		}
		$fieldList = array_combine($fieldList, $fieldList);
		foreach($blackList as $blackElmt) {
			unset($fieldList[$blackElmt]);
		}

		$export = array();
		foreach($fieldList as $fieldName) {
			//skip field != id for proxy when not initialized
			if($proxy && $fieldName != 'id') continue;
			foreach(static::$getterNames as $getter) {
				$getter .= ucfirst($fieldName);
				if(method_exists($object, $getter)) {
					try {
						$fieldValue = $object->$getter();
						$fieldLabel = $fieldName;
						if(!empty($fieldsLabel[$fieldName])) {
							$fieldLabel = $fieldsLabel[$fieldName];
						}

						if(is_object($fieldValue)) {
							$fieldValue = static::handleObjectConversion($fieldValue, $processed);
						} else if(is_array($fieldValue)) {
							$fieldValue = static::handleArrayConversion($fieldValue, $processed);
						}

						$export[$fieldLabel] = $fieldValue;
						break;
					} catch (\Exception $e) {

					}
				}
			}
		}

		return $export;
	}


	/**
	 * @param       $object
	 * @param array $processed
	 * @return array|null
	 */
	protected static function handleObjectConversion($object,
                                                     array $processed = array())
    {
        $classProxy = get_class($object);
        $proxy = static::detectProxy($classProxy);
		if(get_class($object) == static::$classDoctrineCollection) {
			//if doctrine collection turn it to array
			/** @var PersistentCollection $object */
			$object = static::handleArrayConversion($object->toArray(), $processed);
		} else if(in_array(ExportableEntityTrait::class, class_uses(get_class($object))) || $proxy) {
			//entity or proxy, iterate
			/** @var ExportableEntityTrait $object */
			$object = static::processObject2ArrayConversion($object, $processed);
        } else {
            //cannot handle it (including proxies)
            $object = null;
        }
		/** @var array $object */
		return $object;
	}

	/**
	 * @param array $array
	 * @param array $processed
	 * @return array|null
	 */
	protected static function handleArrayConversion(array $array,
                                                    array $processed = array())
    {
		//for an array, loop on it and iterate for object
		$arrayEmpty = true;
		foreach($array as $k => &$v) {
			if(is_object($v)) {
				$v = static::processObject2ArrayConversion($v, $processed);
			} else if(is_array($v)) {
				$v = static::handleArrayConversion($v, $processed);
			}
			if(!empty($v)) $arrayEmpty = false;
		}
		unset($v);
		if($arrayEmpty && !empty($array)) $array = null;

		return $array;
	}


    /**
     * WARNING PROXY
     * we have a proxy when class is not really loaded: we got id attribute and that's it
     * we would need the controller to get doctrine to reload this object without proxy, too heavy
     * so skipped here, we just remove proxy name and others attributes will be empty
     * @param string $className
     * @return bool
     */
	protected static function detectProxy(string &$className)
    {
        $proxy = false;
        foreach(static::$proxiesNames as $prox) {
            if(strpos($className, $prox) !== false) {
                $className = str_replace($prox, '', $className);
                $proxy = true;
            }
        }
        return $proxy;
    }

}
