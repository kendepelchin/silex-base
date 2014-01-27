<?php

namespace Classes\Form\User;

use Classes\Form\Form;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Class for register form
 *
 * @extends \Classes\Form\Form
 * @author  Ken Depelchin <ken.depelchin@gmail.com>
 */
class RegisterForm extends Form {

    public function __construct($formFactory) {
        parent::__construct($formFactory);
    }

    public function build() {
        $this->form = $this->formFactory->createBuilder('form')
            ->add('email', 'email', array(
                'constraints' => array(
                    new Assert\Email(),
                    new Assert\NotBlank()
                ),
                'attr' => array('placeholder' => 'Your email')
            ))
            ->add('username', 'text', array(
                'constraints' => array(
                    new Assert\NotBlank(),
                    new Assert\Regex(array(
                        'pattern' => '/ /',
                        'match' => false,
                        'message' => 'No spaces allowed.'))
                ),
                'attr' => array('placeholder' => 'Username')
            ))
            ->add('password', 'password', array(
                'constraints' => array(
                    new Assert\NotBlank(),
                    new Assert\Length(array('max' => 30))
                )
            ))->getForm();
    }
}
