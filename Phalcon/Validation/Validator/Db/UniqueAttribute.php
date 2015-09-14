<?php namespace Phalcon\Validation\Validator\Db;

use Phalcon\Db;
use Phalcon\DI;
use Phalcon\Validation;
use Phalcon\Validation\Validator;
use Phalcon\Validation\ValidatorInterface;
use Phalcon\Validation\Message;
use Phalcon\Db\Adapter\Pdo as DbConnection;
use Phalcon\Validation\Exception as ValidationException;
use Phasty\Common\Models\Categories;
use Phasty\Common\Models\Attributes;

/**
 * Phalcon\Validation\Validator\Db\UniqueAttribute
 *
 * Validator for checking uniqueness of product attribute in database
 */

class UniqueAttribute extends Validator implements ValidatorInterface
{

    /**
     * Class constructor.
     *
     * @param  array               $options
     * @throws ValidationException
     */
    public function __construct(array $options = array())
    {
        parent::__construct($options);
    }

    /**
     * Executes the uniqueness validation
     *
     * @param  Validation $validator
     * @param  string              $attribute
     * @return boolean
     */
    public function validate(Validation $validator, $attribute)
    {
        $categoryId =  $validator->getValue('categoryId');
        $category = Categories::findFirst("id = '$categoryId'");
        $parents = $category->parents()->toArray();
        $categoryIds = [];
        //get all attributes from parent categories
        foreach ($parents as $parent) {
            $categoryIds[] = $parent['id'];
        }
        $categoryIds[] = $categoryId;
        $name = $validator->getValue('name');
        $attributes = Attributes::query()->where("name = '$name'")->inWhere('categoryId', $categoryIds)
            ->execute()->toArray();

        if ($attributes && count($attributes)) {
            $message = $this->getOption('message');

            if (null === $message) {
                $message = 'Already taken. Choose another!';
            }

            $validator->appendMessage(new Message($message, $attribute, 'UniqueAttribute'));

            return false;
        }

        return true;
    }
}
