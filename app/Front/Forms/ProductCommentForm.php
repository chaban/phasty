<?php
namespace Phasty\Front\Forms;

use Phalcon\Forms\Form, Phalcon\Forms\Element\TextArea, Phalcon\Forms\Element\Hidden, Phalcon\Forms\Element\Password,
	Phalcon\Forms\Element\Submit, Phalcon\Forms\Element\Check, Phalcon\Validation\Validator\PresenceOf, Phalcon\Validation\Validator\Email,
	Phalcon\Validation\Validator\Identical, Phalcon\Validation\Validator\StringLength, Phalcon\Validation\Validator\Confirmation;

class ProductCommentForm extends Form
{

	public function initialize($entity = null, $options = null)
	{

		$positive = new TextArea('positive', array('rows' => 3, 'cols' => 50, 'class'=>"redactor", 'placeholder'=>'Что понравилось в товаре'));

		$positive->setLabel('Что понравилось в товаре');

		$this->add($positive);

		//Negative
		$negative = new TextArea('negative', array('rows' => 3, 'cols' => 50, 'class'=>"redactor", 'placeholder'=>'Что не понравилось в товаре'));

		$negative->setLabel('Что не понравилось в товаре');

		$this->add($negative);

		//Description
    $description = new TextArea('description', array('rows' => 3, 'cols' => 50, 'class'=>"redactor", 'placeholder'=>'Какой делаем вывод'));

		$description->setLabel('Какой делаем вывод');

		$this->add($description);


		//CSRF
		$csrf = new Hidden('csrf');

		$csrf->addValidator(new Identical(array('value' => $this->security->getSessionToken(), 'message' =>
				'<div class="alert alert-danger">сработала защита от CSRF</div>')));

		$this->add($csrf);

		//Sign Up
		$this->add(new Submit('Отправить', array('class' => 'button send')));

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
