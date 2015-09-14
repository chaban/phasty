<?php namespace Phasty\Common\Service\Behaviors;

use Phalcon\Mvc\Model;
use Phalcon\Mvc\Model\Behavior;
use Phalcon\Mvc\Model\BehaviorInterface;
use Phalcon\Mvc\ModelInterface;
use utilphp\util;

class SlugBehavior extends Behavior implements BehaviorInterface
{
    /**
     * The column name for the unqiue url
     */
    public $slug_col = 'slug';

    /**
     * The column name for the unqiue url
     */
    public $title_col = 'title';

    /**
     * Primary key column name needs to be an id
     * @var string
     */
    protected $pk_col = 'id';

    /**
     * Overwrite slug when updating
     */
    public $overwrite = false;

    /**
     * Decode url only usefull if you want to support high unicode characters in url
     */
    public $url_decode = false;

    public function __construct($options = null)
    {
        if (isset($options['slug_col'])) {
            $this->slug_col = $options['slug_col'];
        }

        if (isset($options['title_col'])) {
            $this->title_col = $options['title_col'];
        }

        if (isset($options['pk_col'])) {
            $this->pk_col = $options['pk_col'];
        }

        if (isset($options['overwrite'])) {
            $this->overwrite = $options['overwrite'];
        }

        if (isset($options['url_decode'])) {
            $this->url_decode = $options['url_decode'];
        }
    }

    /**
     * Receive notifications from the Models Manager
     *
     * @param string $eventType
     * @param ModelInterface $model
     */
    public function notify($eventType, ModelInterface $model)
    {
        switch ($eventType) {
            case 'beforeValidationOnCreate':
                return $this->createSlug($model);
                break;
            case 'beforeValidationOnUpdate':
                if ($this->overwrite && !$this->getIsNewRecord($model)) {
                    return $this->createSlug($model);
                }
                break;
        }
    }

    /**
     * @param ModelInterface $model
     *
     * Before saving to database
     */
    public function createSlug(ModelInterface $model)
    {
        $slug = $this->getUniqueSlug($model);
        return $model->writeAttribute($this->slug_col, $slug);
    }

    /**
     * Checks the database to return the unique slug from the database
     * @param ModelInterface $model
     * @return string
     */
    public function getUniqueSlug(ModelInterface $model)
    {
        //$slug = $this->getSlug($model->{$this->title_col});
        $slug = util::slugify($model->{$this->title_col});
        $matches = $this->getMatches($slug, $model);

        if ($matches) {
            $ar_matches = array();
            foreach ($matches as $match) {
                if ($match->{$this->pk_col} == $model->{$this->pk_col} && $match->{$this->slug_col} == $model->{$this->slug_col}) {
                } else {
                    $ar_matches[] = $match->{$this->slug_col};
                }
            }

            $new_slug = $slug;
            $index = 1;
            while ($index > 0) {
                if (in_array($new_slug, $ar_matches)) {
                    $new_slug = $slug . '-' . $index;
                    $index++;
                } else {
                    $slug = $new_slug;
                    $index = -1;
                }
            }
        }
        return $slug;
    }

    /**
     * Lookup if string already exists in database
     *
     * @param string $slug
     * @param ModelInterface $model
     * @return array
     */
    public function getMatches($slug, $model)
    {
        //return $model::find(["'$this->slug_col' = '$slug'"]);
        return $model::find($this->slug_col . " LIKE '%" . $slug . "%'");
    }

    public function getIsNewRecord(ModelInterface $model)
    {
        return $model->getDirtyState() == Model::DIRTY_STATE_TRANSIENT;
    }

    public function missingMethod(ModelInterface $model, $method, $arguments = null)
    {
        if (method_exists($this, $method)) {
            //$this->setOwner($model);
            $result = call_user_func_array(array($this, $method), $arguments);
            if ($result === null) {
                return '';
            }
            return $result;
        }
        return null;
    }

}
