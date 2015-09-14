<?php namespace Phasty\Common\Service\Form;

use Phalcon\Validation,
    Phalcon\Validation\Validator\PresenceOf;
use Phalcon\Validation\Validator\InclusionIn;
//use Phalcon\Validation\Validator\Regex as RegexValidator;

class ProductsForm extends Validation {

    public function initialize()
    {
        $this->add('name', new PresenceOf([
            'message' => 'The name is required'
        ]));

        $this->add('categoryId', new PresenceOf([
            'message' => 'The categoryId is required'
        ]));

        $this->add('brandId', new PresenceOf([
            'message' => 'The brandId is required'
        ]));

        $this->add('price', new PresenceOf([
            'message' => 'The price is required'
        ]));

        $this->add('availability', new InclusionIn(array(
            'message' => 'The availability must be Y or N',
            'domain' => array('Y', 'N'),
            'allowEmpty' => false
        )));

        $this->add('active', new InclusionIn(array(
            'message' => 'The active must be Y or N',
            'domain' => array('Y', 'N'),
            'allowEmpty' => false
        )));

        $this->add('autoDecreaseQuantity', new InclusionIn(array(
            'message' => 'The autoDecreaseQuantity must be Y or N',
            'domain' => array('Y', 'N'),
            'allowEmpty' => false
        )));

        $this->add('fullDescription', new PresenceOf([
            'message' => 'The full description is required'
        ]));

        $this->add('quantity', new PresenceOf([
            'message' => 'The quantity is required'
        ]));

        $this->setFilters('name', ['string', 'trim']);
        $this->setFilters('categoryId', 'int');
        $this->setFilters('brandId', 'int');
        $this->setFilters('price', 'int');
        $this->setFilters('maxPrice', 'int');
        $this->setFilters('quantity', 'int');

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
