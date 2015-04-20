<?php namespace Phasty\Common\Repo\Products;

use Phalcon\Mvc\User\Plugin;
use Phasty\Common\Models\AttributeValues;

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
        $category = $attributeValues->product->category;
        $temp = [];
        $temp['category'] = $category->toArray();
        $temp['attributes'] = $category->attributes->toArray();
        $row = $this->db->fetchOne(
            'SELECT COLUMN_JSON(attributeValues) as attributeValues FROM product_attribute_values WHERE productId = ?', null, [$id]
        );
        $values = json_decode($row['attributeValues']);
        foreach($temp['attributes'] as $key => $attribute){
            $temp['attributes'][$key]['value'] = $values->$attribute['name'];
        }
        $result = new \stdClass();
        $result->productAttribute = $temp;
        return $result;
    }

    /**
     * Update an existing product attribute values
     *
     * @param int id of the product
     * @param file  Data to update an product attribute values
     * @return boolean
     */
    public function update($id, $data)
    {
        $sql = "UPDATE product_attribute_values SET attributeValues=COLUMN_ADD(attributeValues,";
        $params = [];
        $last_key = count($data['attributes']);
        foreach($data['attributes'] as $key => $value){
            if($key < $last_key - 1) {
                $sql .= " ?, ?,";
            }else{
                $sql .= " ?, ?";
            }
            $params[] = $value->name;
            $params[] = $value->value;
        }
        $sql .= ") WHERE id='$id'";
        return $this->db->execute($sql, $params);
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
