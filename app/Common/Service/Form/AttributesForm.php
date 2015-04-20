<?php namespace Phasty\Common\Service\Form;

use Phalcon\Validation,
    Phalcon\Validation\Validator\InclusionIn,
    Phalcon\Validation\Validator\PresenceOf;

class AttributesForm extends Validation {

    public function initialize()
    {
        $this->add('name', new PresenceOf([
            'message' => 'The name is required'
        ]));
        $this->add('group_id', new PresenceOf([
            'message' => 'The group_id is required'
        ]));
        $this->add('filter', new InclusionIn(array(
            'message' => 'The filter must be Y or N',
            'domain' => array('Y', 'N'),
            'allowEmpty' => false  //if true, validation will pass when value is empty
        )));
        $this->add('position', new PresenceOf([
            'message' => 'The position is required'
        ]));
        $this->add('template', new PresenceOf([
            'message' => 'The template is required'
        ]));
        $this->setFilters('name', 'string');
        $this->setFilters('group_id', 'int');
        $this->setFilters('filter', 'striptags');
        $this->setFilters('position', 'int');
        $this->setFilters('template', 'string');
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
