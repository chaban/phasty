<?php namespace Phasty\Front\Forms;

use Phalcon\Forms\Form,
	Phalcon\Forms\Element\Text,
	Phalcon\Forms\Element\Submit,
	Phalcon\Forms\Element\Hidden,
	Phalcon\Validation\Validator\PresenceOf,
	Phalcon\Validation\Validator\Email;
use Phalcon\Validation\Validator\Identical;

class ForgotPasswordForm extends Form
{

	public function initialize()
	{
		$email = new Text('email', array('class' => 'form-control',
			'placeholder' => 'Email used for sign up'
		));

		$email->addValidators(array(
			new PresenceOf(array(
				'message' => '<div class="alert alert-danger">The e-mail is required</div>'
			)),
			new Email(array(
				'message' => '<div class="alert alert-danger">The e-mail is not valid</div>'
			))
		));

		$this->add($email);

		//CSRF
		$csrf = new Hidden('csrf');

		$csrf->addValidator(
			new Identical(array(
				'value' => $this->security->getSessionToken(),
				'message' => '<div class="alert alert-danger">Please reload this page</div>'
			))
		);

		$this->add($csrf);

		$this->add(new Submit('Remind password', array(
			'class' => 'btn btn-primary'
		)));
	}

}