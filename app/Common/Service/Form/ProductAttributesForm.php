<?php namespace Phasty\Common\Service\Form;

use Phalcon\Mvc\User\Plugin;

class ProductAttributesForm extends Plugin {

    /**
     * Sanitize values of attributes
     * @param $data array
     * @return array
     */
    public function sanitize($data)
    {
        foreach($data as $attribute){
            if(!empty($attribute->value)){
                if($attribute->type == 'int'){
                    $attribute->value = $this->filter->sanitize($attribute->value, 'int');
                }else{
                    $attribute->value = $this->filter->sanitize($attribute->value, 'string');
                }
            }
        }
        return $data;
    }

}
