<?php

namespace Keiwen\Cacofony\Controller;

use Keiwen\Cacofony\Http\Response;
use Symfony\Component\Form\Form;
use Symfony\Component\Form\FormView;

abstract class AbstractFormController extends AppController
{

    protected $initialized = false;
    /** @var Form */
    protected $form;
    /** @var  Response|array|mixed */
    protected $formResult;



    abstract protected static function getFormClass(): string;


    protected static function getDefaultData()
    {
        return array();
    }

    protected static function getFormParameters(): array
    {
        return array();
    }

    abstract public static function formAction();

    /**
     */
    protected function generateForm()
    {
        $this->formResult = null;
        //create form
        $this->form = $this->createForm(static::getFormClass(), static::getDefaultData(), static::getFormParameters());
        //handle MASTER request (if embedded controller)
        $this->form->handleRequest($this->getMasterRequest());
        $this->initialized = true;

        if($this->form->isSubmitted()) {
            if($this->form->isValid()) {
                $this->formResult = $this->formHandlerValid();
            } else {
                $this->formResult = $this->formHandlerError();
            }
        }
    }


    /**
     * @return Form
     */
    public function getForm()
    {
        if(!$this->initialized) {
            $this->generateForm();
        }
        return $this->form;
    }

    /**
     * @return FormView
     */
    public function getFormView()
    {
        return $this->getForm()->createView();
    }

    /**
     * @return array|Response|mixed
     */
    public function getFormResult()
    {
        if(!$this->initialized) {
            $this->generateForm();
        }
        return $this->formResult;
    }


    /**
     * Handle form when submitted and valid
     * @return array|Response|mixed
     */
    abstract public function formHandlerValid();

    /**
     * Handle form when submitted and NOT valid
     * @return array|Response|mixed
     */
    abstract public function formHandlerError();


}
