<?php namespace Phalcon\Iwi;

use Exception;
use Imagecow\Image;
use Phalcon\Mvc\Model\Resultset;
use Phalcon\Mvc\Url;
use Phalcon\Mvc\User\Plugin;
use Phasty\Common\Models\Iwi as IwiModel;

class Iwi extends Plugin
{
    protected $_dir;
    protected $image;
    protected $model;
    protected $realPath;
    protected $useDummyImages = false;
    /**
     * @throws Exception
     */
    // changed exception
    public function __construct()
    {
        $this->model = new IwiModel();
        $this->_dir = BASE_DIR . DIRECTORY_SEPARATOR . 'public' . DIRECTORY_SEPARATOR . 'img' . DIRECTORY_SEPARATOR;
    }

    /**
     * @param $imagePath string image path
     * @param $useDummyImage bool use dummy images
     * @throws Exception
     * @return Iwi|object
     */

    public function load($imagePath = '', $useDummyImage = true){
        if(file_exists($imagePath)){
            $this->realPath = $imagePath;
        }elseif($useDummyImage) {
            $this->useDummyImages = true;
            return $this;
        }else{
            return $this;
        }
        $this->image = Image::create($this->realPath);
        if (!$this->image)
            throw new Exception('Image could not be loaded');
        return $this;

    }

    /**
     * @param $width
     * @param $height
     * @param bool $upscale
     * @return Iwi|object
     */
    public function showAs($width, $height, $upscale = false)
    {
        if($this->useDummyImages){
            return "http://lorempixel.com/$width/$height/fashion/1";
        }

        if ($this->image) {

            if (!$upscale) {
                if ($width > $this->image->getWidth())
                    $width = $this->image->getWidth();

                if ($height > $this->image->getHeight())
                    $height = $this->image->getHeight();
            }

            $width = intval($width);
            $height = intval($height);

            $widthProportion = $width / $this->image->getWidth();
            $heightProportion = $height / $this->image->getHeight();

            if ($widthProportion > $heightProportion) {
                $newWidth = $width;
                $newHeight = round($newWidth / $this->image->getWidth() * $this->image->getHeight());
            } else {
                $newHeight = $height;
                $newWidth = round($newHeight / $this->image->getHeight() * $this->image->getWidth());
            }

            $this->image->resize($newWidth, $newHeight)->crop($width, $height);
            return $this->cache();
            
        }
        return '';
    }

    /**
     * @return mixed
     */
    public function cache()
    {
        $path = $this->buildPath();
        if ($path) {
            if ($this->createOrNone() || !file_exists($path)) {
                $this->image->save($path);
            }
        }
        $url = new Url();
        return DIRECTORY_SEPARATOR . $url->path($path);
    }

    /**
     * @return bool|string
     */
    public function buildPath()
    {
        $realPath = $this->getRealPath();
        if (!($realPath)) {
            return false;
        }
        $path = array();
        $info = pathinfo($realPath);
        $path[] = $this->buildDir();
        $path[] = $this->hash() . "." . $info['extension'];
        return implode(DIRECTORY_SEPARATOR, $path);
    }

    /**
     * @return string
     */
    public function buildDir()
    {
        $folder[] = $this->_dir . 'cache';
        $folder[] = substr($this->hash(), 0, 2);
        $folder[] = substr($this->hash(), 2, 2);

        $path[] = 'img/cache';
        $path[] = substr($this->hash(), 0, 2);
        $path[] = substr($this->hash(), 2, 2);

        $path = implode(DIRECTORY_SEPARATOR, $path);
        $folder = implode(DIRECTORY_SEPARATOR, $folder);

        if (!is_dir($folder)) {
            mkdir($folder, 0755, true);
        }

        return $path;
    }

    /**
     * @return string
     */
    public function hash()
    {
        return md5($this->generateBrief());
    }

    /**
     * @return string
     */
    protected function generateBrief()
    {
        $needle = [];
        $realPath = $this->getRealPath();
        array_unshift($needle, $realPath);
        array_unshift($needle, $this->image->getWidth());
        array_unshift($needle, $this->image->getHeight());
        if (is_file($realPath))
            array_unshift($needle, filemtime($realPath));
        return json_encode($needle);
    }

    /**
     * @return bool
     */
    public function createOrNone()
    {
        //$this->verifyTable();
        $hash = $this->hash();
        $result = $this->model->findFirst(array(
            "conditions" => "key = ?1",
            "bind" => [1 => "$hash"],
            "hydration" => Resultset::HYDRATE_OBJECTS));
        if (!$result) {
            $storage = new IwiModel();
            $storage->key = $this->hash();
            $storage->value = $this->generateBrief();
            return $storage->save();
        }
        return false;
    }

    /**
     * Verify table
     */
    public function verifyTable()
    {
        /*if (!Yii::app()->getDb()->schema->getTable('{{storage}}'))
        {
          Yii::app()->getDb()->createCommand()->createTable("{{storage}}", array(
            'key' => 'string',
            'value' => 'text',
            ));
        }*/
        return true;
    }

    public function getRealPath(){
        return $this->realPath;
    }
}
