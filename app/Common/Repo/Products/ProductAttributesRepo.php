<?php namespace Phasty\Common\Repo\Products;

use Phalcon\Mvc\User\Plugin;
use Phasty\Common\Models\Attributes;
use Phasty\Common\Models\AttributeValues;
use Phasty\Common\Models\Products;
use Phasty\Common\Models\Categories;

class ProductAttributesRepo extends Plugin
{
    protected $model;

    // Class expects an model
    public function __construct()
    {
        $this->model = new AttributeValues();
    }

    /**
     * Retrieve images by product id
     * regardless of status
     *
     * @param  int $id product ID
     * @return stdObject object of product information
     */
    public function byId($id)
    {
        $attributeValues = $this->model->findFirst("productId = '$id'");
        if (!$attributeValues) {
            return false;
        }
        //$product = Products::findFirst("id = '$id'");
        $categoryId = $attributeValues->product->categoryId;
        $category = Categories::findFirst("id = '$categoryId'");
        $parents = $category->parents()->toArray();
        $categoryIds = [];
        //get all attributes from parent categories
        foreach ($parents as $parent) {
            $categoryIds[] = $parent['id'];
        }
        $categoryIds[] = $category->id;
        $attributes = Attributes::query()->inWhere('categoryId', $categoryIds)->execute()->toArray();
        $temp = [];
        //$temp['category'] = $category->toArray();
        $temp['attributes'] = $attributes;
        $temp['product'] = $attributeValues->product->toArray();
        $row = $this->db->fetchOne(
            'SELECT COLUMN_JSON(attributeValues) as attributeValues FROM product_attribute_values WHERE productId = ?', null, [$id]
        );
        $values = json_decode($row['attributeValues']);
        foreach ($temp['attributes'] as $key => $attribute) {
            if(isset($values->$attribute['name'])){
                $temp['attributes'][$key]['value'] = $values->$attribute['name'];
            }else {
                $temp['attributes'][$key]['value'] = '';
            }
        }
        $result = new \stdClass();
        $result->productAttribute = $temp;
        return $result;
    }

    /**
     * Update an existing product attribute values
     *
     * @param int id of the product
     * @param array Data to update an product attribute values
     * @return boolean
     */
    public function update($id, $data)
    {
        $sql = "UPDATE product_attribute_values SET attributeValues=COLUMN_ADD(attributeValues,";
        $params = [];
        $attributeValues = '';
        $shortDescription = '';//not for production
        $last_key = count($data);
        foreach ($data as $key => $value) {
            $name = preg_replace("/[^a-zA-Z0-9]+/", "", $value->name);
            $val = preg_replace("/[^a-zA-Z0-9]+/", "", $value->value);
            if ($key < $last_key - 1) {
                $sql .= " ?, ?,";
                $attributeValues .= $name . $val . ', ';
                $shortDescription .= $value->name . ': ' . $value->value . ', ';
            } else {
                $sql .= " ?, ?";
                $attributeValues .= $name . $val;
                $shortDescription .= $value->name . ': ' . $value->value;
            }
            $params[] = $value->name;
            $params[] = $value->value;
        }
        $sql .= ") WHERE id='$id'";
        $product = Products::findFirst("id = '$id'");
        if (!$product) return false;
        $product->attributeValues = $attributeValues;
        $product->shortDescription = $shortDescription;// not for production
        return ($this->db->execute($sql, $params) && $product->update());
    }

    /**
     * Delete an existing product images
     *
     * @param int id of the product
     * @param array  Data to update an product images
     * @return boolean
     */
    public function delete($id, $data)
    {
        $imageName = $this->filter->sanitize($data['imageName'], 'striptags');
        $imagePath = BASE_DIR . DIRECTORY_SEPARATOR . 'public' . $this->_dir . $id . DIRECTORY_SEPARATOR . $imageName;
        if (file_exists($imagePath) && unlink($imagePath)) {
            return true;
        }
        return false;
    }

}
