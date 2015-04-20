<?php namespace Phasty\Common\Repo\Products;

use Phalcon\Mvc\User\Plugin;
use Phasty\Common\Service\Helpers\CFileHelper;

class ProductImagesRepo extends Plugin
{
    protected $fileSystem;
    protected $_dir; //directory with product images

    // Class expects an model
    public function __construct()
    {
        $this->fileSystem = new CFileHelper();
        $this->_dir = $this->config->app->productImagesPath;
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
        $result = new \stdClass();
        $result->productImage = new \stdClass();
        $result->meta = new \stdClass();
        $result->meta->maxImagesCount = $this->config->app->productImagesNumber;
        $result->productImage->images = $this->getImages($id);
        return $result;
    }

    /**
     * Update an existing product images
     *
     * @param int id of the product
     * @param file  Data to update an product images
     * @return boolean
     */
    public function save($id, $data)
    {
        if (!in_array($data->getType(), (array)$this->config->app->productImagesTypes)) {
            return false;
        }
        $imagesFolder = BASE_DIR . DIRECTORY_SEPARATOR . 'public' . $this->_dir . $id . DIRECTORY_SEPARATOR;
        if (!is_dir($imagesFolder)) {
            mkdir($imagesFolder);
        }
        return $data->moveTo($imagesFolder . $data->getName());
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
        if(file_exists($imagePath) && unlink($imagePath)){
            return true;
        }
        return false;
    }

    protected function getImages($id)
    {
        $imagesFolder = BASE_DIR . DIRECTORY_SEPARATOR . 'public' . $this->_dir . $id;
        $result = [];
        if (is_dir($imagesFolder)) {
            $images = $this->fileSystem->findFiles($imagesFolder, [
                'fileTypes' => ['jpeg', 'jpg', 'gif', 'png'],
                'level' => 0,
            ]);
            foreach ($images as $key => $image) {
                if($key >= $this->config->app->productImagesNumber) break;
                $nameData = explode('/', str_replace("\\", "/", $image));
                $imageData['name'] = $nameData[count($nameData) - 1];
                $imageData['path'] = $this->_dir . $id . DIRECTORY_SEPARATOR . $imageData['name'];
                $result[] = $imageData;
            }
        }
        return $result;
    }

}
