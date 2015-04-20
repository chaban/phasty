<?php namespace Phasty\Front\Forms;

use Phalcon\Forms\Element\Check;
use Phalcon\Forms\Element\Hidden;
use Phalcon\Forms\Element\Password;
use Phalcon\Forms\Element\Submit;
use Phalcon\Forms\Element\Text;
use Phalcon\Forms\Form;
use Phalcon\Validation\Validator\Confirmation;
use Phalcon\Validation\Validator\Email;
use Phalcon\Validation\Validator\Identical;
use Phalcon\Validation\Validator\PresenceOf;

class SignUpForm extends Form {

	public function initialize() {

		$name = new Text('name', ['class' => 'form-control', 'placeholder' => 'Name']);

		$name->addValidators(array(new PresenceOf(array('message' =>
			'<div class="alert alert-danger"> Поле Имя обязательно для заполнения</div>'))));

		$this->add($name);

		//Email
		$email = new Text('email', ['class' => 'form-control', 'placeholder' => 'Email']);

		$email->addValidators(array(new PresenceOf(array('message' =>
			'<div class="alert alert-danger">Адрес электронной почты обязателен</div>')),
			new Email(['message' =>
				'<div class="alert alert-danger">Не верный адрес электронной почты</div>']),

		));

		$this->add($email);

		//Password
		$password = new Password('password', ['placeholder' => 'Password at least 6 simbols', 'class' => 'form-control']);

		$password->addValidators(array(
			new PresenceOf(array('message' => '<div class="alert alert-danger">Поле пароль обязательно</div>')),
			/*new StringLength(array('min' => 6, 'messageMinimum' =>
			'<div class="alert alert-danger">Пароль слишком короткий. Минимум 6 знаков</div>')),*/
			new Confirmation(array('message' => '<div class="alert alert-danger">Не совпадение в полях паролей</div>', 'with' =>
				'confirmPassword'))));

		$this->add($password);

		//Confirm Password
		$confirmPassword = new Password('confirmPassword', ['class' => 'form-control', 'placeholder' => 'Confirm Password']);

		$confirmPassword->addValidators(array(new PresenceOf(array('message' =>
			'<div class="alert alert-danger">Поле подтверждения пароля обязательно</div>'))));

		$this->add($confirmPassword);

		//Remember
		$terms = new Check('terms', array('value' => 'yes', 'class' => 'checkbox'));

		$terms->setLabel('You accept <a href="/pages/show/rules">rules?</a>');

		$terms->addValidator(new Identical(array('value' => 'yes', 'message' =>
			'<div class="alert alert-danger">Условия соглашения должны быть приняты</div>')));

		$this->add($terms);

		//CSRF
		$csrf = new Hidden('csrf');

		$csrf->addValidator(new Identical(array('value' => $this->security->getSessionToken(), 'message' =>
			'<div class="alert alert-danger">Please reload the page</div>')));

		$this->add($csrf);

		//Sign Up
		$this->add(new Submit('Sign Up', array('class' => 'btn btn-success')));

	}

	/**
	 * @param string
	 * Prints messages for a specific element
	 */
	public function messages($name) {
		if ($this->hasMessagesFor($name)) {
			foreach ($this->getMessagesFor($name) as $message) {
				$this->flash->error($message);
			}
		}
	}

}
