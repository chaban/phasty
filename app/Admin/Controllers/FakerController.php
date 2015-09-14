<?php namespace Phasty\Admin\Controllers;

use Phasty\Common\Models\AttributeGroups;
use Phasty\Common\Models\Attributes;
use Phasty\Common\Models\AttributeValues;
use Phasty\Common\Models\Products;
use Phasty\Common\Models\Categories;
use Phalcon\Mvc\Model\Resultset\Simple as Resultset;
use Faker;
use Phasty\Common\Models\Reviews;
use Phalcon\Mvc\Controller;

class FakerController extends Controller
{

    public function fakeAction()
    {
        /*$products = Products::find();
        foreach($products as $product){
            $attributeValues = $product->attributeValues;
            $temp = [];
            $temp = explode(",", $attributeValues);
            foreach($temp as $key => $elem){
                $elem = preg_replace("/[^a-zA-Z0-9]+/", "", $elem);
                $temp[$key] = $elem;
            }
            $attributeValues = implode(', ', $temp);
            $product->attributeValues = $attributeValues;
            $product->update();
        }
        echo 'fine';*/
        /*$products = Products::find(['order' => 'id desc']);
        foreach ($products as $product) {
            $temp = '';
            $attribute = Attributes::findFirst("categoryId = '$product->categoryId'");
            $row = $this->db->fetchOne(
                'SELECT COLUMN_JSON(attributeValues) as attributeValues FROM product_attribute_values WHERE productId = ?', null, [$product->id]
            );
            $values = json_decode($row['attributeValues']);
            //$this->utils->var_dump($values);die;
            foreach ($values as $key => $attributeValue) {
                $temp .= $key . ': ' . $attributeValue . ' ' . $attribute->template . ' , ';
            }
            $product->shortDescription = $temp;
            $product->update();
        }*/
        /*$faker = Faker\Factory::create();
        $categoryIds = [2, 4, 7, 11, 13, 14, 15, 16, 17];
        $attributes = [];
        foreach($categoryIds as $key){
            $temp = Attributes::find("categoryId = '$key'");
            foreach($temp as $t)
            {
                $attributes[$key][] = $t->name;
            }
        }
        $attributeValues = new AttributeValues();
        //$sql = "INSERT INTO 'product_attribute_values' ('null', 'productId', 'attributeValues') VALUES";
        $sql = "INSERT INTO product_attribute_values VALUES";
        foreach(range(1, 53) as $key){
            $sql .= "('null', '".$key."', COLUMN_CREATE(";//'colour','blue', 'size','XXL')),";
            $product = Products::findFirst("id = '$key'");
            //$group = AttributeGroups::findFirst("category_id = '$product->categoryId'");
            $tempArray = $attributes[$product->categoryId];
            //$this->utils->var_dump($tempArray);die;
            $last_key = end(array_keys($tempArray));
            foreach($tempArray as $index => $attribute){
                if($last_key == $index){
                    $sql .= "'".$attribute."', '". $faker->word."'";
                }else {
                    $sql .= "'".$attribute."', '".$faker->word."', ";
                }
            }
            if($key < 53) {
                $sql .= ")),";
            }else{
                $sql .= "))";
            }
        }

        $rows = new Resultset(null, $attributeValues, $attributeValues->getReadConnection()->query($sql));
        $this->utils->var_dump($rows);die;*/
        /*$attributes = [];
        foreach(range(1, 5) as $key){
            $temp = Attributes::find("group_id = '$key'");
            foreach($temp as $t)
            {
                $attributes[$key][] = $t->name;
            }
        }
        $attributeValues = new AttributeValues();
        //$sql = "INSERT INTO 'product_attribute_values' ('null', 'productId', 'attributeValues') VALUES";
        $sql = "INSERT INTO product_attribute_values VALUES";
        foreach(range(1, 50) as $key){
            $sql .= "('null', '".$key."', COLUMN_CREATE(";//'colour','blue', 'size','XXL')),";
            $tempArray = $attributes[rand(1, 5)];
            $last_key = end(array_keys($tempArray));
            foreach($tempArray as $index => $attribute){
                if($last_key == $index){
                    $sql .= "'".$attribute."', '". $faker->word."'";
                }else {
                    $sql .= "'".$attribute."', '".$faker->word."', ";
                }
            }
            if($key < 50) {
                $sql .= ")),";
            }else{
                $sql .= "))";
            }
        }

        $rows = new Resultset(null, $attributeValues, $attributeValues->getReadConnection()->query($sql));
        $this->utils->var_dump($rows);die;*/
        foreach (range(1, 50) as $key) {
            /*$group = new AttributeGroups();
            $group->name = $faker->word;
            $group->category_id = $key;
            $group->position = $faker->numberBetween($min = 1, $max = 50);
            $group->save();*/
            /*$group = new Attributes();
            $group->name = $faker->unique()->word;
            $group->group_id = $faker->numberBetween($min = 1, $max = 9);
            $group->filter = $faker->randomElement(['Y','N']);
            $group->position = $faker->numberBetween($min = 1, $max = 20);
            $group->template = $faker->suffix;
            $group->save();*/
        }
        /*foreach (range(1, 50) as $key) {
            $product = new Products();
            $product->categoryId = $faker->randomElement($categoryIds);
            $product->brandId = $faker->numberBetween($min = 1, $max = 50);
            $name = $faker->unique()->sentence($nbWords = 3);
            $product->name = $name;
            $product->slug = \utilphp\util::slugify($name);
            $product->price = $faker->numberBetween($min = 100, $max = 5000000);
            $product->maxPrice = $faker->numberBetween($min = 1000, $max = 50000000);
            $product->active = $faker->randomElement(['Y','N']);
            $product->sku = $faker->uuid;
            $product->quantity = $faker->numberBetween($min = 1, $max = 500000);
            $product->availability = $faker->randomElement(['Y','N']);
            $product->autoDecreaseQuantity = $faker->randomElement(['Y','N']);
            $product->viewsCount = $faker->numberBetween($min = 1, $max = 500000);
            $date = (array)$faker->dateTime();
            $product->createdAt = $date['date'];
            $date = (array)$faker->dateTime();
            $product->updatedAt = $date['date'];
            $product->addedToCartCount = $faker->numberBetween($min = 1, $max = 500000);
            $product->rating = $faker->numberBetween($min = 1, $max = 500000);
            $product->shortDescription = null;
            $product->fullDescription = $faker->text($maxNbChars = 700);
            $product->save();
        }*/
        echo 'success';
        //$laptops = Categories::findFirstById(4);
        //$pcLap = Categories::findFirstById(5);
        //$computers = Categories::findFirstById(7);
        //$categories = Categories::findFirstById(1);
        //$pants = Categories::findFirstById(13);
        //$pants->prependTo($computers);

        //$this->utils->var_dump($pants->toArray());die;
        /*$categories = [
            ['id' => 1, 'name' => 'TV & Home Theather'],
            ['id' => 2, 'name' => 'Tablets & E-Readers'],
            ['id' => 3, 'name' => 'Computers', 'children' => [
                ['id' => 4, 'name' => 'Laptops', 'children' => [
                    ['id' => 5, 'name' => 'PC Laptops'],
                    ['id' => 6, 'name' => 'Macbooks (Air/Pro)']
                ]],
                ['id' => 7, 'name' => 'Desktops'],
                ['id' => 8, 'name' => 'Monitors']
            ]],
            ['id' => 9, 'name' => 'Cell Phones']
        ];

        Categories::buildTree($categories);*/
    }
}