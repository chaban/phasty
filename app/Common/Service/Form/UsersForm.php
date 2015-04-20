<?php namespace Phasty\Common\Service\Form;

use Phalcon\Validation,
    Phalcon\Validation\Validator\PresenceOf;
use Phalcon\Validation\Validator\Email;
use Phalcon\Validation\Validator\InclusionIn;
use Phalcon\Validation\Validator\Regex as RegexValidator;

class UsersForm extends Validation {

    public function initialize()
    {
        $this->add('name', new PresenceOf([
            'message' => 'The name is required'
        ]));

        $this->add('email', new PresenceOf([
            'message' => 'The email is required'
        ]));

        $this->add('email', new Email([
            'message' => 'The email is not valid'
        ]));

        $this->add('password', new PresenceOf([
            'message' => 'The password is required'
        ]));

        $this->add('mustChangePassword', new InclusionIn(array(
            'message' => 'The mustChangePassword must be Y or N',
            'domain' => array('Y', 'N'),
            'allowEmpty' => false
        )));

        $this->add('banned', new InclusionIn(array(
            'message' => 'The banned must be Y or N',
            'domain' => array('Y', 'N'),
            'allowEmpty' => false
        )));

        $this->add('confirmed', new InclusionIn(array(
            'message' => 'The confirmed must be Y or N',
            'domain' => array('Y', 'N'),
            'allowEmpty' => false
        )));

        $this->add('address', new PresenceOf([
            'message' => 'The position is required'
        ]));

        $this->add('phone', new PresenceOf([
            'message' => 'The position is required'
        ]));

        $this->add('phone', new RegexValidator(array(
            'pattern' => '/^[\d\+ -]+$/',
            //'pattern' => '/^(?:(?:\+?1\s*(?:[.-]\s*)?)?(?:\(\s*([2-9]1[02-9]|[2-9][02-8]1|[2-9][02-8][02-9])\s*\)|([2-9]1[02-9]|[2-9][02-8]1|[2-9][02-8][02-9]))\s*(?:[.-]\s*)?)?([2-9]1[02-9]|[2-9][02-9]1|[2-9][02-9]{2})\s*(?:[.-]\s*)?([0-9]{4})(?:\s*(?:#|x\.?|ext\.?|extension)\s*(\d+))?$/',
            'message' => 'The phone number is invalid'
        )));

        $this->setFilters('name', ['string', 'trim']);
        $this->setFilters('address', 'string');
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
