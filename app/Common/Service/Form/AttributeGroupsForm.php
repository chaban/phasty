<?php namespace Phasty\Common\Service\Form;

use Phalcon\Validation,
    Phalcon\Validation\Validator\PresenceOf;

class AttributeGroupsForm extends Validation {

    public function initialize()
    {
        $this->add('name', new PresenceOf([
            'message' => 'The title is required'
        ]));
        $this->add('category_id', new PresenceOf([
            'message' => 'The category id is required'
        ]));
        $this->add('position', new PresenceOf([
            'message' => 'The position is required'
        ]));
        $this->setFilters('name', 'string');
        $this->setFilters('category_id', 'int');
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
