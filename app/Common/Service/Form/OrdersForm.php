<?php namespace Phasty\Common\Service\Form;

use Phalcon\Validation,
    Phalcon\Validation\Validator\PresenceOf;
//use Phalcon\Validation\Validator\InclusionIn;
use Phalcon\Validation\Validator\Email as EmailValidator;
use Phalcon\Validation\Validator\Regex as RegexValidator;

class OrdersForm extends Validation {

    public function initialize()
    {
        $this->add('userId', new PresenceOf([
            'message' => 'The userId is required'
        ]));

        $this->add('userName', new PresenceOf([
            'message' => 'The user name is required'
        ]));

        $this->add('email', new PresenceOf([
            'message' => 'The email is required'
        ]));

        $this->add('email', new EmailValidator(array(
            'message' => 'The e-mail is not valid'
        )));

        $this->add('phone', new PresenceOf([
            'message' => 'The user phone is required'
        ]));

        $this->add('phone', new RegexValidator(array(
            'pattern' => '/^[\d\+ -]+$/',
            //'pattern' => '/^(?:(?:\+?1\s*(?:[.-]\s*)?)?(?:\(\s*([2-9]1[02-9]|[2-9][02-8]1|[2-9][02-8][02-9])\s*\)|([2-9]1[02-9]|[2-9][02-8]1|[2-9][02-8][02-9]))\s*(?:[.-]\s*)?)?([2-9]1[02-9]|[2-9][02-9]1|[2-9][02-9]{2})\s*(?:[.-]\s*)?([0-9]{4})(?:\s*(?:#|x\.?|ext\.?|extension)\s*(\d+))?$/',
            'message' => 'The phone number is invalid'
        )));

        $this->add('address', new PresenceOf([
            'message' => 'The user address is required'
        ]));

        $this->add('deliveryId', new PresenceOf([
            'message' => 'The deliveryId is required'
        ]));

        $this->add('statusId', new PresenceOf([
            'message' => 'The statusId is required'
        ]));

        $this->setFilters('userName', ['string', 'trim']);
        $this->setFilters('address', ['string', 'trim']);
        $this->setFilters('statusId', 'int');
        $this->setFilters('deliveryId', 'int');
        $this->setFilters('userId', 'int');

    }

    public function isNotValid(array $data)
    {
        $messages = $this->validate($data);
        $output = '';
        if (count($messages)) {
            foreach ($messages as $message) {
                $output .= '<p>' . $message . '</p>';
            }
        } else {
            return false;
        }
        return $output;
    }

}
