<?php

namespace Keiwen\Cacofony\EntitiesManagement;

use Keiwen\Cacofony\Controller\DefaultController;
use Keiwen\Cacofony\Exception\FormNotFoundException;

trait ExportableEntityTrait
{



    /**
     * @return array
     */
    public static function retrieveExportFields()
    {
        $rClass = new \ReflectionClass(static::class);
        $props = $rClass->getProperties();
        $toExport = array();
        foreach($props as $prop) {
            $toExport[] = $prop->getName();
        }
        return $toExport;
    }

    /**
     * @return array
     */
    public static function retrieveExportFieldLabels()
    {
        return array();
    }

    /**
     * @param array $blackList
     * @return array
     */
	public function toArray(array $blackList = array())
    {
		return EntitySerializer::object2Array($this, array(), $blackList, array());
	}


    /**
     * @param array $blackList
     * @return string
     */
	public function toJson(array $blackList = array())
    {
        return EntitySerializer::object2Json($this, array(), $blackList, array());
	}


}
