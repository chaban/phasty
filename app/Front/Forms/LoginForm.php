<?php namespace Phasty\Front\Forms;

use Phalcon\Forms\Form,
	Phalcon\Forms\Element\Text,
	Phalcon\Forms\Element\Password,
	Phalcon\Forms\Element\Submit,
	Phalcon\Forms\Element\Check,
	Phalcon\Forms\Element\Hidden,
	Phalcon\Validation\Validator\PresenceOf,
	Phalcon\Validation\Validator\Email,
	Phalcon\Validation\Validator\Identical;

class LoginForm extends Form
{

	public function initialize()
	{
		//Email
		$email = new Text('email', array('class' => 'form-control',
			'placeholder' => 'Email'
		));

		$email->addValidators(array(
			new PresenceOf(array(
				'message' => 'Email required'
			)),
			new Email(array(
				'message' => 'Wrong email'
			))
		));

		$this->add($email);

		//Password
		$password = new Password('password', array('class' => 'form-control',
			'placeholder' => 'Password'
		));

		$password->addValidator(
			new PresenceOf(array(
				'message' => 'Password required'
			))
		);

		$this->add($password);

		//Remember
		$remember = new Check('remember', array('class' => 'checkbox',
			'value' => 'yes'
		));

		$remember->setLabel('Remember me');

		$this->add($remember);

		//CSRF
		$csrf = new Hidden('csrf');

		$csrf->addValidator(
			new Identical(array(
				'value' => $this->security->getSessionToken(),
				'message' => 'сработала защита от CSRF'
			))
		);

		$this->add($csrf);

		$this->add(new Submit('Enter', array(
			'class' => 'btn btn-success'
		)));
	}

}