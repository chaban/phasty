<?php namespace Phasty\Common\Service\Form;

use Phalcon\Validation,
    Phalcon\Validation\Validator\PresenceOf;
use Phalcon\Validation\Validator\Regex as RegexValidator;

class CurrencyForm extends Validation {

    public function initialize()
    {
        $this->add('name', new PresenceOf([
            'message' => 'The title is required'
        ]));
        $this->add('rate', new RegexValidator(array(
            //'pattern' => '/^[+-]?[0-9]{1,3}(?:,?[0-9]{3})*(?:\.[0-9]{2})?$/',
            'pattern' => '/^[0-9]+[\.]?[0-9]+$/',
            'message' => 'The rate must be decimal'
        )));
        $this->setFilters('name', 'string');
        $this->setFilters('rate', 'float');
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
