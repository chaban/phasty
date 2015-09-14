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
    protected $model;
    protected $realPath;
    protected $useDummyImages = false;
    protected $width;
    protected $height;
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
     * @param  string $imagePath image path
     * @param  bool $useDummyImages use dummy images
     * @throws Exception
     * @return Iwi|object
     */

    public function load($imagePath = '', $useDummyImages = true)
    {
        $this->useDummyImages = $useDummyImages;
        if ($imagePath && file_exists($imagePath)) {
            $this->realPath = $imagePath;
            return $this;
        }
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
        $this->width = $width;
        $this->height = $height;
        if (!$this->realPath && $this->useDummyImages) {
            return "http://lorempixel.com/$width/$height/fashion/1";
        }

        if (!$this->realPath) {
            return '';
        }
        return $this->cache($width, $height, $upscale);
    }

    /**
     * @param string $path to resized and cached image
     * @param int $width
     * @param int $height
     * @param bool $upscale
     * @throws Exception
     * @return $this
     */
    private function scaleAndSaveImage($path, $width, $height, $upscale )
    {
        $image = Image::create($this->realPath);
        if (!$image)
            throw new \Exception('Image could not be loaded');
        
        if (!$upscale) {
            if ($width > $image->getWidth())
                $width = $image->getWidth();

            if ($height > $image->getHeight())
                $height = $image->getHeight();
        }

        $width = intval($width);
        $height = intval($height);

        $widthProportion = $width / $image->getWidth();
        $heightProportion = $height / $image->getHeight();

        if ($widthProportion > $heightProportion) {
            $newWidth = $width;
            $newHeight = round($newWidth / $image->getWidth() * $image->getHeight());
        } else {
            $newHeight = $height;
            $newWidth = round($newHeight / $image->getHeight() * $image->getWidth());
        }

        $image->resize($newWidth, $newHeight)->crop($width, $height);
        $image->save($path);
        return $this;
    }

    /**
     * @param int $width
     * @param int $height
     * @param bool $upscale
     * @return string path to cached image
     */
    private function cache($width, $height, $upscale)
    {
        $path = $this->buildPath();
        if (!$path) {
            return '';
        }
        if ($this->needToCreate() || !file_exists($path)) {
            $this->scaleAndSaveImage($path, $width, $height, $upscale);
        }
        $url = new Url();
        return DIRECTORY_SEPARATOR . $url->path($path);
    }

    /**
     * @return bool|string
     */
    private function buildPath()
    {
        $realPath = $this->getRealPath();
        if (!$realPath) {
            return false;
        }
        $path = [];
        $info = pathinfo($realPath);
        $path[] = $this->buildDir();
        $path[] = $this->hash() . "." . $info['extension'];
        return implode(DIRECTORY_SEPARATOR, $path);
    }

    /**
     * @return string
     */
    private function buildDir()
    {
        $hash = $this->hash();
        $folder[] = $this->_dir . 'cache';
        $folder[] = substr($hash, 0, 2);
        $folder[] = substr($hash, 2, 2);

        $path[] = 'img/cache';
        $path[] = substr($hash, 0, 2);
        $path[] = substr($hash, 2, 2);

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
    private function hash()
    {
        return md5($this->generateBrief());
    }

    /**
     * @return string
     */
    private function generateBrief()
    {
        $needle = [];
        $realPath = $this->getRealPath();
        array_unshift($needle, $realPath);
        array_unshift($needle, filemtime($realPath));
        array_unshift($needle, filesize($realPath));
        array_unshift($needle, $this->width);
        array_unshift($needle, $this->height);
        return json_encode($needle);
    }

    /**
     * @return bool
     */
    private function needToCreate()
    {
        $hash = $this->hash();
        $result = $this->model->findFirst([
            "conditions" => "key = ?1",
            "bind" => [1 => "$hash"]]);
        if (!$result) {
            return $this->model->save(['key' => $hash, 'value' => $this->generateBrief()]);
        }
        return false;
    }

    private function getRealPath()
    {
        return $this->realPath;
    }
}
