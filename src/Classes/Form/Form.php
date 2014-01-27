<?php

namespace Classes\Form;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Form\FormFactory;

/**
 * Abstract class for all our forms
 *
 * @author  Ken Depelchin <ken.depelchin@gmail.com>
 */
abstract class Form {

    protected $formFactory;

    protected $form;

    protected $values;

    protected $attributes = array();

    public function __construct(FormFactory $formFactory, $values = array()) {
        $this->formFactory = $formFactory;
        $this->values = $values;
        $this->attributes = $this->buildAttributes();
        $this->build();
    }

    public function bind($request) {
        return $this->form->bind($request);
    }

    public function isValid() {
        return $this->form->isValid();
    }

    public function getData() {
        return $this->form->getData();
    }

    public function getForm() {
        return $this->form;
    }

    /**
     * Override this method if you want to build custom attributes for forms
     * @return array
     */
    public function buildAttributes() {
        return array();
    }
}
