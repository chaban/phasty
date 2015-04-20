<?php namespace Phasty\Common\Service\Form;

use Phalcon\Validation,
    Phalcon\Validation\Validator\PresenceOf;
use Phalcon\Validation\Validator\InclusionIn;
use Phalcon\Validation\Validator\Regex as RegexValidator;

class DeliveryForm extends Validation {

    public function initialize()
    {
        $this->add('name', new PresenceOf([
            'message' => 'The name is required'
        ]));

        $this->add('active', new InclusionIn(array(
            'message' => 'The status must be Y or N',
            'domain' => array('Y', 'N'),
            'allowEmpty' => false
        )));

        $this->add('price', new RegexValidator(array(
            //'pattern' => '/^[+-]?[0-9]{1,3}(?:,?[0-9]{3})*(?:\.[0-9]{2})?$/',
            'pattern' => '/^[0-9]+[\.]?[0-9]+$/',
            'message' => 'The price must be decimal'
        )));

        $this->add('freeFrom', new RegexValidator(array(
            'pattern' => '/^[0-9]+[\.]?[0-9]+$/',
            'message' => 'The free from must be decimal'
        )));

        $this->add('position', new PresenceOf([
            'message' => 'The position is required'
        ]));

        $this->setFilters('name', ['string', 'trim', 'upper']);
        $this->setFilters('price', 'float');
        $this->setFilters('freeFrom', 'float');
        $this->setFilters('position', 'int');
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
