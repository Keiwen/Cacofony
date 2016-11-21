<?php

namespace Keiwen\Cacofony\FormProcessor;


use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\HttpFoundation\Request;
use Keiwen\Cacofony\Exception\FormNotFoundException;

/**
 * Class designed to manage form, from creation to request handling,
 * in order to enlight controllers. When extending this, implement
 * abstract methods and just use process method from your controller.
 * You can also implement listFormAssertions method to check form data
 * validity
 */
abstract class DefaultFormProcessor
{


    protected $data = array();
    protected $defaultData = array();
    protected $formOptions = array();
	protected $toAssert = array();

    /** @var FormFactoryInterface $formFactory */
    protected $formFactory;
    /** @var \Symfony\Component\Form\Form */
    protected $form;

	protected static $autoAssertForm = true;

    const CHECK_EQUAL = 1;
    const CHECK_GREATER = 2;
    const CHECK_LESSER = 3;
    const CHECK_GREATER_OR_EQUAL = 4;
    const CHECK_LESSER_OR_EQUAL = 5;
    const CHECK_STRING_PART = 6;
    const CHECK_IN_ARRAY = 7;
	
	
	const METHOD_EMPTY = 'empty';
	const METHOD_NOTEMPTY = 'not empty';
	const METHOD_DATA = 'data';
	const METHOD_INLIST = 'in list';
	const METHOD_NOTINLIST = 'not in list';
	


    public function __construct(FormFactoryInterface $formfactory, array $defautData = array(), array $formOptions = array())
    {
        $this->formFactory = $formfactory;
        $this->setDefaultData($defautData);
        $this->setFormOptions($formOptions);
    }

	
	
    /**
     * Full form process, from form creation to handle
     * @param Request|null $request
     * @param array $parameters
     * @return bool validated
     */
    public function process(Request $request = null)
	{
        $this->prepareForm();
        $this->getForm();
        return $this->handleRequest($request);
    }


    /**
     * Set default data and options of your form
	 * You can use processor's parameters
	 * @see setDefaultData()
	 * @see setFormOptions()
     */
    protected abstract function prepareForm();

	
	/**
     * @return \Symfony\Component\Form\Form
     */
    public function getForm()
    {
        if(empty($this->form)) {
            $this->form = $this->buildForm();
        }
        return $this->form;
    }


    /**
     * @return \Symfony\Component\Form\FormInterface
     */
    protected function buildForm()
    {
        $form = $this->formFactory->create($this->getFormClass(), $this->getDefaultData(), $this->getFormOptions());
        return $form;
    }

	
    /**
     * Define the form class.
	 * Overwrite to return a specific form class.
	 * By default, it will try to guess form class
	 * from processor class, replacing 'FormProcessor'
	 * by 'Form'
     */
    public function getFormClass()
	{
		$processorClass = static::class;
		$guessedFormClass = str_replace('FormProcessor', 'Form', $processorClass);
		if(class_exists($guessedFormClass)) return $guessedFormClass;
		throw new FormNotFoundException("Form not found for processor $processorClass (tried $guessedFormClass).
			Overwrite getFormClass() method in your processor to specify targeted form class");
	}

	
	/**
     * @return array
     */
    public function getDefaultData()
    {
        return $this->defaultData;
    }

	
    /**
     * @param array $data
     */
    public function setDefaultData(array $data)
    {
        $this->defaultData = $data;
    }


    /**
     * @return array
     */
    public function getData()
    {
        return empty($this->data) ? $this->defaultData : $this->data;
    }

	/**
     * @return array
     */
    public function getFormOptions()
    {
        return $this->formOptions;
    }

    /**
     * @param array $options
     */
    public function setFormOptions(array $options)
    {
        $this->formOptions = $options;
    }


	/**
     * @param Request $request
     * @return bool
     */
    public function handleRequest(Request $request)
    {
        if(empty($this->form)) return false;
        $this->storeRequest($request);
        if($this->form->isValid()) {
            $this->data = $this->form->getData();
			if(static::$autoAssertForm) {
				if(!$this->assertForm()) return false;
			}
            return $this->handleSubmission();
        }
        return false;
    }

	/**
     * store request without further management
     * @param Request $request
     */
    protected function storeRequest(Request $request)
    {
        if(!empty($this->form)) {
            $this->form->handleRequest($request);
        }
    }
	
	
    /**
     * Handle form submission. Check if data are ok, save
	 * your entities, etc...
	 * If auto assert on, form is validated before this method.
	 * Return a boolean to validate or not the handling.
	 * @see testFormData()
	 * @see checkFormData()
	 * @see checkFormDataInList()
	 * @see assertForm())
     * @return bool
     */
    protected abstract function handleSubmission();




    /**
	 * Check if data is filled in form
     * @param string $dataName
     * @return bool
     */
    public function isFormDataEmpty(string $dataName)
    {
        $data = $this->getData();
        return empty($data[$dataName]);
    }



    /**
	 * Check a form data
     * @param string $dataName
     * @param mixed $baseValue value to compare with form value
     * @param int $checkType see check constants
     * @param bool $oneIsEnough at least one match in a list
     * @param bool $acceptEmpty ok if value empty
     * @return bool
     */
    public function checkFormData(string $dataName,
                                  $baseValue,
                                  int $checkType = self::CHECK_EQUAL,
                                  bool $oneIsEnough = true,
                                  bool $acceptEmpty = true)
    {
        $data = $this->getData();
        if(empty($data[$dataName])) {
            return $acceptEmpty;
        }
        
        $listData = is_array($data[$dataName]) ? $data[$dataName] : array($data[$dataName]);
        $checkSingle = false;
        $checkAll = true;
        foreach($listData as $dataValue) {
            if($this->testFormData($dataValue, $baseValue, $checkType)) {
                $checkSingle = true;
            } else {
                $checkAll = false;
            }
        }
        return $oneIsEnough ? $checkSingle : $checkAll;
    }

    


    /**
	 * Check in form data the list of value
	 * @see checkFormData()
     * @param string $dataName
     * @param array $baseValues
     * @param bool $expected false to invalidate if value are actually found
     * @param int $checkType
     * @param bool $acceptEmpty
     * @param bool $oneIsEnough
     * @return bool
     */
    public function checkFormDataInList(string $dataName,
                                        array $baseValues,
                                        bool $expected = true,
                                        int $checkType = self::CHECK_EQUAL,
                                        bool $oneIsEnough = true,
                                        bool $acceptEmpty = true)
    {
        $data = $this->getData();
        if(empty($data[$dataName])) return $acceptEmpty;

        $found = !$expected;
        foreach($baseValues as $baseValue) {
            $checkValue = $this->checkFormData($dataName, $baseValue, $checkType, $oneIsEnough, $acceptEmpty);
            if($checkValue) {
                $found = $expected;
                break;
            }
        }
        return $found;
    }

	
	
    /**
	 * compare a form value to a given value (called by check methods)
     * @param mixed $formDataValue
     * @param mixed $baseValue
     * @param int $checkType
     * @return bool
     */
    protected function testFormData($formDataValue, $baseValue, int $checkType = self::CHECK_EQUAL)
    {
        switch($checkType) {
            case self::CHECK_EQUAL:
                return ($formDataValue == $baseValue);
            case self::CHECK_GREATER:
                return $formDataValue > $baseValue;
            case self::CHECK_LESSER:
                return $formDataValue < $baseValue;
            case self::CHECK_GREATER_OR_EQUAL:
                return $formDataValue >= $baseValue;
            case self::CHECK_LESSER_OR_EQUAL:
                return $formDataValue <= $baseValue;
            case self::CHECK_STRING_PART:
                if(!is_string($baseValue)) return false;
                return (strripos($baseValue, $formDataValue) !== false);
            case self::CHECK_IN_ARRAY:
                if(!is_array($baseValue)) return false;
                return in_array($formDataValue, $baseValue);
        }
        return false;
    }


    /**
     * Define a assertion to be validated
     * @see checkFormData()
     * @param string $dataName
     * @param string $validationMethod see METHOD constants
     * @param mixed  $baseValue
     * @param int    $checkType see CHECK constants
     * @param bool   $oneIsEnough
     * @param bool   $acceptEmpty
     */
	public function addFormAssertion(string $dataName,
									string $validationMethod = self::METHOD_EMPTY,
									$baseValue = '',
									int $checkType = self::CHECK_EQUAL,
									bool $oneIsEnough = true,
									bool $acceptEmpty = true)
	{
		$this->toAssert[] = func_get_args();
	}
	
	
	/**
	 * Define assertions to be validated after form submission
	 * @see addFormAssertion()
     */
	public function listFormAssertions()
	{
		
	}
	
	
	/**
	 * Go through all defined assertions to check validity
	 * This method is called before handleSubmission()
     * @param string $firstDataFailed first form data name with assertion failed
	 * @return bool all assertions passed
     */
	public function assertForm(&$firstDataFailed = '')
	{
		foreach($this->toAssert as $toAssert) {
			list($dataName, $assertMethod, $baseValue, $checkType, $oneIsEnough, $acceptEmpty) = $toAssert;
			switch($assertMethod) {
				case static::METHOD_EMPTY:
					$assert = $this->isFormDataEmpty($dataName);
					break;
				case static::METHOD_NOTEMPTY:
					$assert = !$this->isFormDataEmpty($dataName);
					break;
				case static::METHOD_DATA:
					$assert = $this->checkFormData($dataName, $baseValue, $checkType, $oneIsEnough, $acceptEmpty);
					break;
				case static::METHOD_INLIST:
					$assert = $this->checkFormDataInList($dataName, $baseValue, true, $checkType, $oneIsEnough, $acceptEmpty);
					break;
				case static::METHOD_NOTINLIST:
					$assert = $this->checkFormDataInList($dataName, $baseValue, false, $checkType, $oneIsEnough, $acceptEmpty);
					break;
				default:
					$assert = false;
			}
			if(!$assert) {
				$firstDataFailed = $dataName;
				return false;
			}
		}
		return true;
	}
	
	

}
