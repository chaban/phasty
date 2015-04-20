<?php namespace Phasty\Front\Forms;

use Phalcon\Forms\Form, Phalcon\Forms\Element\Text, Phalcon\Forms\Element\Hidden, Phalcon\Forms\Element\Password,
	Phalcon\Forms\Element\Submit, Phalcon\Forms\Element\Check, Phalcon\Validation\Validator\PresenceOf, Phalcon\Validation\Validator\Email,
	Phalcon\Validation\Validator\Identical, Phalcon\Validation\Validator\StringLength, Phalcon\Validation\Validator\Confirmation;

class SocialForm extends Form
{

	public function initialize($entity = null, $options = null)
	{

		$name = new Text('name');

		$name->setLabel('Логин');

		$name->addValidators(array(new PresenceOf(array('message' =>
					'<div class="alert alert-danger"> Поле Логин обязательно для заполнения</div>'))));

		$this->add($name);

		//Email
		$email = new Text('email');

		$email->setLabel('E-Mail');

		$email->addValidators(array(new PresenceOf(array('message' =>
					'<div class="alert alert-danger">Адрес электронной почты обязателен</div>')), new Email(array('message' =>
					'<div class="alert alert-danger">Не верный адрес электронной почты</div>'))));

		$this->add($email);

		//CSRF
		$csrf = new Hidden('csrf');

		$csrf->addValidator(new Identical(array('value' => $this->security->getSessionToken(), 'message' =>
				'<div class="alert alert-danger">сработала защита от CSRF</div>')));

		$this->add($csrf);

		//Sign Up
		$this->add(new Submit('Сохранить', array('class' => 'btn btn-success')));

	}

	/**
	 * Prints messages for a specific element
	 */
	public function messages($name)
	{
		if ($this->hasMessagesFor($name))
		{
			foreach ($this->getMessagesFor($name) as $message)
			{
				$this->flash->error($message);
			}
		}
	}

}
