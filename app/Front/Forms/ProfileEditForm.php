<?php
namespace Phasty\Front\Forms;

use Phalcon\Forms\Form,
    Phalcon\Forms\Element\Text,
    Phalcon\Forms\Element\Hidden,
    Phalcon\Validation\Validator\Identical,
    Phalcon\Forms\Element\Submit,
    Phalcon\Validation\Validator\PresenceOf,
    Phalcon\Validation\Validator\Email;

class ProfileEditForm extends Form
{

    public function initialize()
    {

        $name = new Text('name', array('class' => 'form-control'));

        $name->setLabel('Name');

        $name->addValidators(array(new PresenceOf(array('message' =>
            '<div class="alert alert-danger"> Поле Имя обязательно для заполнения</div>'))));

        $this->add($name);

        //Email
        $email = new Text('email', array('class' => 'form-control'));

        $email->setLabel('E-Mail');

        $email->addValidators(array(new PresenceOf(array('message' =>
            '<div class="alert alert-danger">Адрес электронной почты обязателен</div>')),
            new Email(array('message' =>
            '<div class="alert alert-danger">Не верный адрес электронной почты</div>'))
        ));

        $this->add($email);

        //Address
        $address = new Text('address', array('class' => 'form-control'));

        $address->setLabel('Address');

        $address->addValidators(array(new PresenceOf(array('message' =>
            '<div class="alert alert-danger">Адрес обязателен</div>'))));

        $this->add($address);

        //Phone
        $phone = new Text('phone', array('class' => 'form-control'));
        $phone->setLabel('Phone number');
        $phone->addValidators(array(
            new PresenceOf(array('message' =>
                '<div class="alert alert-danger">Необходимо указать номер телефона для связи</div>'))
        ));
        $this->add($phone);


        //CSRF
        $csrf = new Hidden('csrf');

        $csrf->addValidator(new Identical(array('value' => $this->security->getSessionToken(), 'message' =>
            '<div class="alert alert-danger">Please reload the page</div>')));

        $this->add($csrf);

        //Sign Up
        $this->add(new Submit('Save', array('class' => 'btn btn-success')));

    }

    /**
     * @param string name of field
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
