<?php namespace Phasty\Common\Service\Form;

use Phalcon\Validation,
    Phalcon\Validation\Validator\PresenceOf;
use Phalcon\Validation\Validator\InclusionIn;

class PagesForm extends Validation
{

    public function initialize()
    {
        $this->add('name', new PresenceOf([
            'message' => 'The name is required'
        ]));

        $this->add('content', new PresenceOf([
            'message' => 'The content is required'
        ]));

        $this->add('active', new InclusionIn(array(
            'message' => 'The status must be Y or N',
            'domain' => array('Y', 'N'),
            'allowEmpty' => false  //if true, validation will pass when value is empty
        )));
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
