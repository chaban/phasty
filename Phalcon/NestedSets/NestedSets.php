<?php
/**
 * This is port to phalcon of this  wonderful library:
 * @link https://github.com/creocoder/yii2-nested-sets
 * @copyright Copyright (c) 2015 Alexander Kochetov
 * @license http://opensource.org/licenses/BSD-3-Clause
 */

namespace Phalcon\NestedSets;
use Phalcon\Db\RawValue;
use Phalcon\Mvc\Model;
use Phalcon\Mvc\Model\Behavior;
use Exception;


/**
 * NestedSets
 */
abstract class NestedSets extends Model
{
    const OPERATION_MAKE_ROOT = 'makeRoot';
    const OPERATION_PREPEND_TO = 'prependTo';
    const OPERATION_APPEND_TO = 'appendTo';
    const OPERATION_INSERT_BEFORE = 'insertBefore';
    const OPERATION_INSERT_AFTER = 'insertAfter';
    const OPERATION_DELETE_WITH_CHILDREN = 'deleteWithChildren';

    /**
     * primary key
     * @var string
     */
    protected $primaryKey = 'id';

    /**
     * @var string|false
     */
    public $treeAttribute = false;
    /**
     * @var string
     */
    public $leftAttribute = 'lft';
    /**
     * @var string
     */
    public $rightAttribute = 'rgt';
    /**
     * @var string
     */
    public $depthAttribute = 'depth';
    /**
     * @var string|null
     */
    protected $operation;
    /**
     * @var Model|null
     */
    protected $node;

    /**
     * get primary key name
     * @return string
     */
    public function getKeyName()
    {
        return $this->primaryKey;
    }

    /**
     * @return string
     */
    public function getLeftName()
    {
        return $this->leftAttribute;
    }

    /**
     * @return string
     */
    public function getRightName()
    {
        return $this->rightAttribute;
    }

    /**
     * @return string
     */
    public function getDepthName()
    {
        return $this->depthAttribute;
    }

    /**
     * @return string
     */
    public function getRootName()
    {
        return $this->treeAttribute;
    }

    /**
     * @return int
     */
    public function getLeft()
    {
        return $this->readAttribute($this->getLeftName());
    }

    /**
     * @return int
     */
    public function getRight()
    {
        return $this->readAttribute($this->getRightName());
    }

    /**
     * @return int
     */
    public function getKey()
    {
        return $this->readAttribute($this->getKeyName());
    }

    /**
     * @return int
     */
    public function getRoot()
    {
        return $this->readAttribute($this->getRootName());
    }

    /**
     * @return int
     */
    public function getDepth()
    {
        return $this->readAttribute($this->getDepthName());
    }

    /**
     * @return int
     */
    public function getIsNewRecord()
    {
        return $this->getDirtyState() == Model::DIRTY_STATE_TRANSIENT;
    }

    /**
     * Indicates if the model exists
     * @return int
     */
    public function exists()
    {
        return $this->getDirtyState() == Model::DIRTY_STATE_PERSISTENT;
    }

    /**
     * initialize new model criteria
     */
    protected function initQuery()
    {
        return $this->query()->orderBy($this->getLeftName());
    }

    /**
     * Equals?
     *
     * @param Model
     * @return boolean
     */
    public function equals($node)
    {
        return ($this == $node);
    }

    /**
     * @param string $value
     */
    public function setOperation($value = null){
        $this->operation = $value;
    }

    /**
     * Maps the provided tree structure into the database.
     *
     * @param   array
     * @return  boolean
     */
    public static function buildTree($nodeList) {
        return (new static)->makeTree($nodeList);
    }

    /**
     * Maps the provided tree structure into the database using the current node
     * as the parent. The provided tree structure will be inserted/updated as the
     * descendancy subtree of the current node instance.
     *
     * @param   array
     * @return  boolean
     */
    public function makeTree($nodeList) {
        $mapper = new SetMapper($this);
        return $mapper->map($nodeList);
    }

    /**
     * Creates the root node if the active record is new or moves it
     * as the root node.
     * @param array $attributes
     * @return boolean
     */
    public function makeRoot($attributes = null)
    {
        $this->operation = self::OPERATION_MAKE_ROOT;

        return $this->save($attributes);
    }

    /**
     * Creates a node as the first child of the target node if the active
     * record is new or moves it as the first child of the target node.
     * @param Model $node
     * @param array $attributes
     * @return boolean
     */
    public function prependTo($node, $attributes = null)
    {
        $this->operation = self::OPERATION_PREPEND_TO;
        $this->node = $node;

        return $this->save($attributes);
    }

    /**
     * Creates a node as the last child of the target node if the active
     * record is new or moves it as the last child of the target node.
     * @param Model $node
     * @param array $attributes
     * @return boolean
     */
    public function appendTo($node, $attributes = null)
    {
        $this->operation = self::OPERATION_APPEND_TO;
        $this->node = $node;

        return $this->save($attributes);
    }

    /**
     * Creates a node as the previous sibling of the target node if the active
     * record is new or moves it as the previous sibling of the target node.
     * @param Model $node
     * @param array $attributes
     * @return boolean
     */
    public function insertBefore($node, $attributes = null)
    {
        $this->operation = self::OPERATION_INSERT_BEFORE;
        $this->node = $node;

        return $this->save($attributes);
    }

    /**
     * Creates a node as the next sibling of the target node if the active
     * record is new or moves it as the next sibling of the target node.
     * @param Model $node
     * @param array $attributes
     * @return boolean
     */
    public function insertAfter($node, $attributes = null)
    {
        $this->operation = self::OPERATION_INSERT_AFTER;
        $this->node = $node;

        return $this->save($attributes);
    }

    /**
     * Gets the parents of the node.
     * @param integer|null $depth the depth
     * @return Model
     */
    public function parents($depth = null)
    {
        $query = $this->initQuery()
            ->where($this->getLeftName() . ' < ' . $this->getLeft() . ' and ' . $this->getRightName() . ' > ' . $this->getRight());

        if ($depth) {
            $query->andWhere($this->getDepthName() . ' >= ' . $this->getDepth() - $depth);
        }

        $query = $this->applyTreeAttributeCondition($query);
        $query->orderBy($this->getLeftName() . ' asc');

        return $this->find($query->getParams());
    }

    /**
     * Gets the children of the node.
     * @param integer|null $depth the depth
     * @return Model
     */
    public function children($depth = null)
    {
        $query = $this->initQuery()
            ->where($this->getLeftName() . ' > ' . $this->getLeft() . ' and ' . $this->getRightName() . ' < ' . $this->getRight());

        if ($depth !== null) {
            $query->andWhere($this->getDepthName() . " <= '$depth' + " . $this->getDepth());
        }

        $query = $this->applyTreeAttributeCondition($query);

        return $this->find($query->getParams());
    }

    /**
     * Gets the leaves of the node.
     * @return Model
     */
    public function leaves()
    {
        $query = $this->initQuery()
            ->where($this->getLeftName() . ' > ' . $this->getLeft() . ' and ' . $this->getRightName() . ' < ' . $this->getRight());

        $query = $this->applyTreeAttributeCondition($query);
        return $query->execute();
    }

    /**
     * Gets the previous sibling of the node.
     * @return Model
     */
    public function prev()
    {
        $query = $this->initQuery()
            ->where($this->getRightName() . ' = ' . $this->getLeft() - 1);
        $query = $this->applyTreeAttributeCondition($query);

        return $query->execute();
    }

    /**
     * Gets the next sibling of the node.
     * @return Model
     */
    public function next()
    {
        $query = $this->initQuery()
            ->where($this->getLeftName() . ' = ' . $this->getRight() + 1);
        $query = $this->applyTreeAttributeCondition($query);

        return $this->find($query->getParams());
    }

    /**
     * Determines whether the node is root.
     * @return boolean whether the node is root
     */
    public function isRoot()
    {
        return $this->getLeft() == 1;
    }

    /**
     * Gets the root nodes.
     * @return Model
     */
    public function roots()
    {
        return $this->query()
            ->where($this->getLeftName() . ' = 1')
            ->orderBy($this->getKeyName())->execute()->getFirst();
    }

    /**
     * Determines whether the node is child of the parent node.
     * @param Model $node the parent node
     * @return boolean whether the node is child of the parent node
     */
    public function isChildOf($node)
    {
        $result = $this->getLeft() > $node->getLeft()
            && $this->getRight() < $node->getRight();

        if ($result && $this->treeAttribute !== false) {
            $result = $this->getRoot() === $node->getRoot();
        }

        return $result;
    }

    /**
     * Determines whether the node is leaf.
     * @return boolean whether the node is leaf
     */
    public function isLeaf()
    {
        return $this->getRight() - $this->getLeft() === 1;
    }

    /**
     * @throws NotSupportedException
     */
    public function beforeCreate()
    {
        if ($this->node !== null && !$this->node->getIsNewRecord()) {
            $this->node->refresh();
        }

        switch ($this->operation) {
            case self::OPERATION_MAKE_ROOT:
                $this->beforeInsertRootNode();
                break;
            case self::OPERATION_PREPEND_TO:
                $this->beforeInsertNode($this->node->getLeft() + 1, 1);
                break;
            case self::OPERATION_APPEND_TO:
                $this->beforeInsertNode($this->node->getRight(), 1);
                break;
            case self::OPERATION_INSERT_BEFORE:
                $this->beforeInsertNode($this->node->getLeft(), 0);
                break;
            case self::OPERATION_INSERT_AFTER:
                $this->beforeInsertNode($this->node->getRight() + 1, 0);
                break;
            default:
                throw new NotSupportedException('Method "' . get_class($this) . '::insert" is not supported for inserting new nodes.');
        }
    }

    /**
     * @throws Exception
     */
    protected function beforeInsertRootNode()
    {
        if ($this->treeAttribute === false && $this->roots())
        {
            throw new Exception('Can not create more than one root when "treeAttribute" is false.');
        }

        $this->writeAttribute($this->getLeftName(), 1);
        $this->writeAttribute($this->getRightName(), 2);
        $this->writeAttribute($this->getDepthName(), 0);
    }

    /**
     * @param integer $value
     * @param integer $depth
     * @throws Exception
     */
    protected function beforeInsertNode($value, $depth)
    {
        if ($this->node->getIsNewRecord()) {
            throw new Exception('Can not create a node when the target node is new record.');
        }

        if ($depth === 0 && $this->node->isRoot()) {
            throw new Exception('Can not create a node when the target node is root.');
        }

        $this->writeAttribute($this->getLeftName(), $value);
        $this->writeAttribute($this->getRightName(), $value + 1);
        $this->writeAttribute($this->getDepthName(), $this->node->getDepth() + $depth);

        if ($this->getRootName() !== false) {
            $this->writeAttribute($this->getRootName(), $this->node->getRoot());
        }

        $this->shiftLeftRightAttribute($value, 2);
    }

    /**
     * @throws Exception
     */
    public function afterCreate()
    {
        if ($this->operation === self::OPERATION_MAKE_ROOT && $this->treeAttribute !== false) {
            $this->writeAttribute($this->getRootName(), $this->getKey());
            $primaryKey = $this->getKey();

            if (!isset($primaryKey)) {
                throw new Exception('"' . get_class($this) . '" must have a primary key.');
            }
            $nodes = $this->query()->where($this->getKeyName() . ' = ' . $this->getRoot())->execute();
            foreach($nodes as $node){
                $node->update([$this->getRootName() => $this->getRoot()]);
            }
        }

        $this->operation = null;
        $this->node = null;
    }

    /**
     * @throws Exception
     */
    public function beforeUpdate()
    {
        if ($this->node !== null && !$this->node->getIsNewRecord()) {
            $this->node->refresh();
        }

        switch ($this->operation) {
            case self::OPERATION_MAKE_ROOT:
                if ($this->treeAttribute === false) {
                    throw new Exception('Can not move a node as the root when "treeAttribute" is false.');
                }
                if ($this->isRoot()) {
                    throw new Exception('Can not move the root node as the root.');
                }
                break;
            case self::OPERATION_INSERT_BEFORE:
            case self::OPERATION_INSERT_AFTER:
                if ($this->node->isRoot()) {
                    throw new Exception('Can not move a node when the target node is root.');
                }
            case self::OPERATION_PREPEND_TO:
            case self::OPERATION_APPEND_TO:
                if ($this->node->getIsNewRecord()) {
                    throw new Exception('Can not move a node when the target node is new record.');
                }

                if ($this->equals($this->node)) {
                    throw new Exception('Can not move a node when the target node is same.');
                }

                if ($this->node->isChildOf($this)) {
                    throw new Exception('Can not move a node when the target node is child.');
                }
        }
    }

    /**
     * @return void
     */
    public function afterUpdate()
    {
        switch ($this->operation) {
            case self::OPERATION_MAKE_ROOT:
                $this->moveNodeAsRoot();
                break;
            case self::OPERATION_PREPEND_TO:
                $this->moveNode($this->node->getLeft() + 1, 1);
                break;
            case self::OPERATION_APPEND_TO:
                $this->moveNode($this->node->getRight(), 1);
                break;
            case self::OPERATION_INSERT_BEFORE:
                $this->moveNode($this->node->getLeft(), 0);
                break;
            case self::OPERATION_INSERT_AFTER:
                $this->moveNode($this->node->getRight() + 1, 0);
                break;
            default:
                return;
        }

        $this->operation = null;
        $this->node = null;
    }

    /**
     * @return void
     */
    protected function moveNodeAsRoot()
    {
        $rootValue = $this->getRoot();
        $rightValue = $this->getRight();
        $leftValue = $this->getLeft();
        $depthValue = $this->getDepth();

        $nodes = $this->query()
            ->where($this->getLeftName() . ' >= ' . $this->getLeft())
            ->andWhere($this->getRightName() . ' <= ' . $this->getRight())
            ->andWhere($this->getRootName() . ' =  ' . $rootValue)->execute();

        foreach ($nodes as $node) {
            $node->update(
                [
                    $this->getLeftName() => new RawValue($this->getLeftName() . sprintf('%+d', 1 - $leftValue)),
                    $this->getRightName() => new RawValue($this->getRightName() . sprintf('%+d', 1 - $leftValue)),
                    $this->getDepthName() => new RawValue($this->getDepthName() . sprintf('%+d', -$depthValue)),
                    $this->getRootName() => $this->getKey()
                ]
            );
        }

        $this->shiftLeftRightAttribute($rightValue + 1, $leftValue - $rightValue - 1);
    }

    /**
     * @param integer $value
     * @param integer $depth
     */
    protected function moveNode($value, $depth)
    {
        $leftValue = $this->getLeft();
        $rightValue = $this->getRight();
        $depthValue = $this->getDepth();
        $depthAttribute = $this->getDepthName();
        $depth = $this->node->getDepth() - $depthValue + $depth;

        if ($this->treeAttribute === false || $this->getRoot() === $this->node->getRoot()) {
            $delta = $rightValue - $leftValue + 1;
            $this->shiftLeftRightAttribute($value, $delta);

            if ($leftValue >= $value) {
                $leftValue += $delta;
                $rightValue += $delta;
            }

            $query = $this->initQuery()
                ->where($this->getLeftName() . ' >= ' . $leftValue . ' and ' . $this->getRightName() . ' <= ' . $rightValue);
            $query = $this->applyTreeAttributeCondition($query);

            $nodes = $query->execute();
            foreach ($nodes as $node) {
                $node->update([
                    $this->getDepthName() => new RawValue($depthAttribute . sprintf('%+d', $depth))
                ]);
            }

            foreach ([$this->getLeftName(), $this->getRightName()] as $attribute) {
                $query = $this->query()->where($attribute . ' >= ' . $leftValue . ' and ' . $attribute . ' <= ' . $rightValue);
                $query = $this->applyTreeAttributeCondition($query);

                $nodes = $query->execute();
                foreach ($nodes as $node) {
                    $node->update([$attribute => new RawValue($attribute . sprintf('%+d', $value - $leftValue))]);
                }
            }

            $this->shiftLeftRightAttribute($rightValue + 1, -$delta);
        } else {
            $leftAttribute = $this->getLeftName();
            $rightAttribute = $this->getRootName();
            $nodeRootValue = $this->node->getRoot();

            foreach ([$this->getLeftName(), $this->getRightName()] as $attribute) {
                $nodes = $this->query()->where($attribute . ' >= ' . $value . ' and ' . $this->getRootName() . ' = ' . $nodeRootValue);
                foreach ($nodes as $node) {
                    $node->update([$attribute => new RawValue($attribute . sprintf('%+d', $rightValue - $leftValue + 1))]);
                }
            }

            $delta = $value - $leftValue;

            $nodes = $this->query()->where($leftAttribute . ' >= ' . $leftValue . ' and ' . $rightAttribute . ' <= ' . $rightValue . ' and ' . $this->getRootName() . ' = ' . $this->getRoot());
            foreach ($nodes as $node) {
                $node->update([
                    $leftAttribute => new RawValue($leftAttribute . sprintf('%+d', $delta)),
                    $rightAttribute => new RawValue($rightAttribute . sprintf('%+d', $delta)),
                    $depthAttribute => new RawValue($depthAttribute . sprintf('%+d', $depth)),
                    $this->getRootName() => $nodeRootValue,
                ]);
            }

            $this->shiftLeftRightAttribute($rightValue + 1, $leftValue - $rightValue - 1);
        }
    }

    /**
     * Deletes a node and its children.
     * @return integer|false the number of rows deleted or false if
     * the deletion is unsuccessful for some reason.
     * @throws \Exception
     */
    public function deleteWithChildren()
    {
        $this->operation = self::OPERATION_DELETE_WITH_CHILDREN;
        return $this->deleteWithChildrenInternal();
    }

    /**
     * @return integer|false the number of rows deleted or false if
     * the deletion is unsuccessful for some reason.
     */
    protected function deleteWithChildrenInternal()
    {
        if (!$this->customBeforeDelete()) {
            return false;
        }

        $query = $this->query()->where($this->getLeftName() . ' >= ' . $this->getLeft() . ' and ' . $this->getRightName() . ' <= ' . $this->getRight());

        $query = $this->applyTreeAttributeCondition($query);
        $nodes = $query->execute();
        foreach($nodes as $node){
            $result = $node->delete();
        }
        $this->setDirtyState(Model::DIRTY_STATE_TRANSIENT);
        $this->customAfterDelete();

        return $result;
    }

    /**
     * @throws Exception
     * @throws NotSupportedException
     */
    public function customBeforeDelete()
    {
        if ($this->getIsNewRecord()) {
            throw new Exception('Can not delete a node when it is new record.');
        }

        if ($this->isRoot() && $this->operation !== self::OPERATION_DELETE_WITH_CHILDREN) {
            throw new NotSupportedException('Method "' . get_class($this) . '::delete" is not supported for deleting root nodes.');
        }

        $this->refresh();
        return true;
    }

    /**
     * @return void
     */
    public function customAfterDelete()
    {
        $leftValue = $this->getLeft();
        $rightValue = $this->getRight();

        if ($this->isLeaf() || $this->operation === self::OPERATION_DELETE_WITH_CHILDREN) {
            $this->shiftLeftRightAttribute($rightValue + 1, $leftValue - $rightValue - 1);
        } else {
            $query = $this->query()->where($this->getLeftName() . ' >= ' . $leftValue . ' and ' . $this->getRightName() . ' <= ' . $rightValue);

            $query = $this->applyTreeAttributeCondition($query);
            $nodes = $query->execute();
            foreach ($nodes as $node) {
                $node->update([
                    $this->getLeftName() => new RawValue($this->getLeftName() . sprintf('%+d', -1)),
                    $this->getRightName() => new RawValue($this->getRightName() . sprintf('%+d', -1)),
                    $this->getDepthName() => new RawValue($this->getDepthName() . sprintf('%+d', -1)),
                ]);
            }

            $this->shiftLeftRightAttribute($rightValue + 1, -2);
        }

        $this->operation = null;
        $this->node = null;
    }

    /**
     * @param integer $value
     * @param integer $delta
     */
    protected function shiftLeftRightAttribute($value, $delta)
    {
        foreach ([$this->getLeftName(), $this->getRightName()] as $attribute) {
            $nodes = $this->query()->where($attribute . ' >= ' . $value)
                ->execute();

            foreach ($nodes as $node) {
                $node->update(
                    [$attribute => new RawValue($attribute . sprintf('%+d', $delta))]
                );
            }
        }
    }

    /**
     * @param \Phalcon\Mvc\Model\Criteria
     */
    protected function applyTreeAttributeCondition($query)
    {
        if ($this->treeAttribute !== false) {
            return $query->andWhere($this->getRootName() . ' = ' . $this->getRoot());
        }
        return $query;
    }
}
