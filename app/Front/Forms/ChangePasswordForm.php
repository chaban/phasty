<?php namespace Phasty\Front\Forms;

use Phalcon\Forms\Form,
	Phalcon\Forms\Element\Password,
	Phalcon\Forms\Element\Submit,
	Phalcon\Forms\Element\Hidden,
	Phalcon\Validation\Validator\Identical,
	Phalcon\Validation\Validator\PresenceOf,
	Phalcon\Validation\Validator\StringLength,
	Phalcon\Validation\Validator\Confirmation;

class ChangePasswordForm extends Form
{

	public function initialize()
	{
		//Password
		$password = new Password('password');

		$password->addValidators(array(
			new PresenceOf(array(
				'message' => 'Пароль обязателен'
			)),
			new StringLength(array(
				'min' => 8,
				'messageMinimum' => 'Пароль слишком короткий. Минимум 8 знаков'
			)),
			new Confirmation(array(
				'message' => 'Не совпадение в поле подтверждения',
				'with' => 'confirmPassword'
			))
		));

		$this->add($password);

		//Confirm Password
		$confirmPassword = new Password('confirmPassword');

		$confirmPassword->addValidators(array(
			new PresenceOf(array(
				'message' => 'Подтверждение пароля обязательно'
			))
		));

		$this->add($confirmPassword);

		//CSRF
		$csrf = new Hidden('csrf');

		$csrf->addValidator(
			new Identical(array(
				'value' => $this->security->getSessionToken(),
				'message' => '<div class="alert alert-danger">Please reload this page</div>'
			))
		);

		$this->add($csrf);

		$this->add(new Submit('Save', [
			'class' => 'btn btn-success'
		]));

	}

}