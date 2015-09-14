<?php namespace Phasty\Common\Service\Form;

use Phalcon\Validation,
    Phalcon\Validation\Validator\PresenceOf;
use Phalcon\Validation\Validator\InclusionIn;

class DiscountForm extends Validation {

    public function initialize()
    {
        $this->add('name', new PresenceOf([
            'message' => 'The name is required'
        ]));

        $this->add('sum', new PresenceOf([
            'message' => 'The sum is required'
        ]));

        $this->add('startDate', new PresenceOf([
            'message' => 'The startDate is required'
        ]));

        $this->add('endDate', new PresenceOf([
            'message' => 'The endDate is required'
        ]));

        $this->add('active', new InclusionIn(array(
            'message' => 'The status must be Y or N',
            'domain' => array('Y', 'N'),
            'allowEmpty' => false  //if true, validation will pass when value is empty
        )));

        $this->setFilters('name', ['string', 'trim', 'upper']);
        $this->setFilters('sum', ['string', 'trim']);
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
