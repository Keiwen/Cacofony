<?php

namespace Keiwen\Cacofony\EntitiesManagement;

use Keiwen\Cacofony\Controller\DefaultController;
use Keiwen\Cacofony\Exception\FormNotFoundException;

trait EnhancedEntityTrait
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

    public static function searchFormInNamespace()
    {
        return array('EntityForm', 'Form');
    }

	/**
	 * @param DefaultController $controller
	 * @return \Doctrine\Common\Persistence\ObjectRepository
	 */
	public static function getRepository(DefaultController $controller)
    {
		return $controller->getRepository(get_called_class());
	}

	/**
	 * @param DefaultController $controller
	 * @return self[]
	 */
	public static function findAll(DefaultController $controller)
    {
		$repo = self::getRepository($controller);
		return $repo->findAll();
	}

	/**
	 * @param DefaultController $controller
	 * @param array $criteria
	 * @param array $orderBy
	 * @param int $limit
	 * @param int $offset
	 * @return self[]
	 */
	public static function findBy(DefaultController $controller,
                                  array $criteria,
                                  array $orderBy = array(),
                                  int $limit = null,
                                  int $offset = null)
    {
		$repo = self::getRepository($controller);
		return $repo->findBy($criteria, $orderBy, $limit, $offset);
	}


	/**
	 * @param DefaultController $controller
	 * @param int $id
	 * @return self|null
	 */
	public static function find(DefaultController $controller, int $id)
    {
		$repo = self::getRepository($controller);
		return $repo->find($id);
	}

	/**
	 * @param DefaultController $controller
	 * @param array $options
	 * @param bool $handleRequest
	 * @return \Symfony\Component\Form\Form
	 * @throws FormNotFoundException
	 */
	public function createForm(DefaultController $controller,
                               array $options = array(),
							   bool $handleRequest = false)
    {
		$class = get_class($this);
		$searchFormPattern = str_replace('\\Entity\\', '\\{placeholder}\\', $class);
		$found = false;
		$testedClasses = array();
        $formClass = '';
		foreach(static::searchFormInNamespace() as $searchForm) {
			$formClass = str_replace('{placeholder}', $searchForm, $searchFormPattern);
			if(class_exists($formClass)) {
				$found = true;
				break;
			}
			$testedClasses[] = $formClass;
			$formClass .= 'Form';
			if(class_exists($formClass)) {
				$found = true;
				break;
			}
			$testedClasses[] = $formClass;
		}
		if(!$found) {
			throw new FormNotFoundException("Form not found among [" . implode(', ', $testedClasses) . "] for class $class");
		}

		$form = $controller->createEntityForm($formClass, $this, $options);
		if($handleRequest) {
			$form->handleRequest($controller->getRequest());
		}
		return $form;
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
