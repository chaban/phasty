<?php
namespace Shop\Forms;

use Phalcon\Forms\Form,
	Phalcon\Forms\Element\Password,
	Phalcon\Forms\Element\Submit,
	Phalcon\Validation\Validator\PresenceOf,
	Phalcon\Validation\Validator\StringLength,
	Phalcon\Validation\Validator\Confirmation;

use Models\Profiles;

class ChangePasswordForm extends Form
{

	public function initialize()
	{
		//Password
		$password = new Password('password', array('placeholder' => 'Не меньше 6 знаков', 'class' => 'form-control'));

		$password->addValidators(array(
			new PresenceOf(array(
				'message' => 'Пароль обязателен'
			)),
			new StringLength(array(
				'min' => 6,
				'messageMinimum' => 'Пароль слишком короткий. Минимум 6 знаков'
			)),
			new Confirmation(array(
				'message' => 'Не совпадение в поле подтверждения',
				'with' => 'confirmPassword'
			))
		));

		$this->add($password);

		//Confirm Password
		$confirmPassword = new Password('confirmPassword', array('class' => 'form-control'));

		$confirmPassword->addValidators(array(
			new PresenceOf(array(
				'message' => 'Подтверждение пароля обязательно'
			))
		));

		$this->add($confirmPassword);

	}

}