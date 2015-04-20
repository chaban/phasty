<?php
namespace Phasty\Front\Forms;

use Phalcon\Forms\Form, Phalcon\Forms\Element\Text, Phalcon\Forms\Element\TextArea, Phalcon\Forms\Element\Date,
    Phalcon\Forms\Element\Hidden, Phalcon\Forms\Element\Password, Phalcon\Forms\Element\Submit, Phalcon\Forms\Element\Check,
    Phalcon\Validation\Validator\PresenceOf, Phalcon\Validation\Validator\Email, Phalcon\Validation\Validator\Identical,
    Phalcon\Validation\Validator\StringLength, Phalcon\Validation\Validator\Confirmation;

class OrderForm extends Form
{

    public function initialize($entity = null, $options = null)
    {

        $name = new Text('name', array('class' => 'form-control'));

        $name->setLabel('Имя');

        $name->addValidators(array(new PresenceOf(array('message' => '<div class="alert alert-danger">Поле Имя обязательно для заполнения</div>'))));

        $this->add($name);

        //Email
        $email = new Text('email', array('class' => 'form-control'));

        $email->setLabel('E-Mail');

        $email->addValidators(array(new PresenceOf(array('message' => '<div class="alert alert-danger">Адрес электронной почты обязателен</div>')), new Email(array
        ('message' => '<div class="alert alert-danger">Не верный адрес электронной почты</div>'))));

        $this->add($email);

        //Phone
        $phone = new Text('phone', array('class' => 'form-control'));
        $phone->setLabel('Телефон');
        $phone->addValidators(array(
            new PresenceOf(array('message' => '<div class="alert alert-danger">Необходимо указать номер телефона для связи</div>'))
        ));
        $this->add($phone);

        //Address
        $address = new TextArea('address');

        $address->setLabel('Адрес доставки');

        $address->addValidators(array(new PresenceOf(array('message' => '<div class="alert alert-danger">Поле адресс обязательно</div>')), new StringLength(array
        ('min' => 5, 'messageMinimum' => '<div class="alert alert-danger">Введите адрес доставки</div>'))));

        $this->add($address);

        //Time
        $time = new Text('time', array('class' => 'form-control'));

        $time->setLabel('Дата и время');

        $this->add($time);

        //Comment
        $comment = new TextArea('comment');

        $comment->setLabel('Примечание');

        $this->add($comment);

        //CSRF
        $csrf = new Hidden('csrf');

        $csrf->addValidator(new Identical(array('value' => $this->security->getSessionToken(), 'message' =>
            'сработала защита от CSRF')));

        $this->add($csrf);

        //Sign Up
        $this->add(new Submit('Отправить заказ', array('class' => 'btn btn-success')));

    }

    /**
     * Prints messages for a specific element
     */
    public function messages($name)
    {
        if ($this->hasMessagesFor($name)) {
            foreach ($this->getMessagesFor($name) as $message) {
                $this->flash->error($message);
            }
        }
    }

}
