<?php namespace Phalcon\Baum;

//use Phalcon\Baum\Extensions\Eloquent\Collection;
//use Phalcon\Baum\Extensions\Eloquent\Model;
use Phalcon\Db\RawValue;
use Phalcon\Mvc\Model;
use utilphp\util;

/**
 * Node
 *
 * This abstract class implements Nested Set functionality. A Nested Set is a
 * smart way to implement an ordered tree with the added benefit that you can
 * select all of their descendants with a single query. Drawbacks are that
 * insertion or move operations need more complex sql queries.
 *
 * Nested sets are appropiate when you want either an ordered tree (menus,
 * commercial categories, etc.) or an efficient way of querying big trees.
 */
abstract class PhalconNode extends Model
{
    /**
     * primary key
     * @var string
     */
    protected $primaryKey = 'id';

    /**
     * Column name to store the reference to parent's node.
     *
     * @var string
     */
    protected $parentColumn = 'parent_id';

    /**
     * Column name for left index.
     *
     * @var string
     */
    protected $leftColumn = 'lft';

    /**
     * Column name for right index.
     *
     * @var string
     */
    protected $rightColumn = 'rgt';

    /**
     * Column name for depth field.
     *
     * @var string
     */
    protected $depthColumn = 'depth';

    /**
     * Column to perform the default sorting
     *
     * @var string
     */
    protected $orderColumn = null;

    /**
     * Indicates whether we should move to a new parent.
     *
     * @var int
     */
    protected static $moveToNewParentId = null;

    /**
     * Columns which restrict what we consider our Nested Set list
     *
     * @var array
     */
    protected $scoped = array();

    /**
     * Get the column names which define our scope
     *
     * @return array
     */
    public function getScopedColumns()
    {
        return (array)$this->scoped;
    }


    /**
     * get primary key name
     */
    public function getKeyName()
    {
        return $this->primaryKey;
    }

    /**
     * get Depth key name
     */
    public function getDepthColumnName()
    {
        return $this->depthColumn;
    }

    /**
     * Get the "left" field column name.
     *
     * @return string
     */
    public function getLeftColumnName()
    {
        return $this->leftColumn;
    }

    public function getLeft()
    {
        return $this->readAttribute($this->getLeftColumnName());
    }

    public function getRight()
    {
        return $this->readAttribute($this->getRightColumnName());
    }

    public function getKey()
    {
        return $this->readAttribute($this->getKeyName());
    }

    /**
     * Get the "right" field column name.
     *
     * @return string
     */
    public function getRightColumnName()
    {
        return $this->rightColumn;
    }

    /**
     * Indicates if the model exists
     */
    public function exists()
    {
        return $this->getDirtyState() == Model::DIRTY_STATE_PERSISTENT;
    }

    /**
     * Indicates if the model modified
     */
    protected function isDirty()
    {
        return $this->getDirtyState() == Model::DIRTY_STATE_TRANSIENT;
    }

    /**
     * Get the model's "depth" value.
     *
     * @return int
     */
    public function getDepth()
    {
        return $this->readAttribute($this->getDepthColumnName());
    }

    /**
     * Get the "order" field column name.
     *
     * @return string
     */
    public function getOrderColumnName()
    {
        return is_null($this->orderColumn) ? $this->getLeftColumnName() : $this->orderColumn;
    }

    /**
     * Get the model's "order" value.
     *
     * @return mixed
     */
    public function getOrder()
    {
        return $this->readAttribute($this->getOrderColumnName());
    }

    /**
     * Phalcon model events
     */
    public function beforeCreate()
    {
        $this->setDefaultLeftAndRight();
    }

    public function beforeSave()
    {
        $this->storeNewParent();
    }

    public function afterSave()
    {
        $this->moveToNewParent();
        //$this->setDepth();
        if (!($level = $this->getLevel())) {
            $this->{$this->getDepthColumnName()} = new RawValue('default');
        } else {
            $this->{$this->getDepthColumnName()} = $level;
        }
    }

    /**
     * Sets default values for left and right fields.
     *
     * @return void
     */
    public function setDefaultLeftAndRight()
    {
        $criteria = $this->query()->orderBy($this->getRightColumnName() . ' desc')->limit(1)->sharedLock();
        $withHighestRight = $this->findFirst($criteria->getParams());

        $maxRgt = 0;
        if ($withHighestRight) $maxRgt = $withHighestRight->readAttribute($this->getRightColumnName());

        $this->writeAttribute($this->getLeftColumnName(), $maxRgt + 1);
        $this->writeAttribute($this->getRightColumnName(), $maxRgt + 2);
    }

    /**
     * Store the parent_id if the attribute is modified so as we are able to move
     * the node to this new parent after saving.
     *
     * @return void
     */
    public function storeNewParent()
    {
        if ($this->isDirty() && ($this->exists() || !$this->isRoot())) {
            static::$moveToNewParentId = $this->getParentId();
        } else {
            static::$moveToNewParentId = FALSE;
        }
    }

    /**
     * Move to the new parent if appropiate.
     *
     * @return void
     */
    public function moveToNewParent()
    {
        $pid = static::$moveToNewParentId;

        if (!$pid)
            $this->makeRoot();
        else if ($pid)
            $this->makeChildOf($pid);
    }

    /**
     * Make current node a root node.
     *
     * @return \Phalcon\Baum\PhalconNode
     */
    public function makeRoot()
    {
        return $this->moveTo($this, 'root');
    }

    /**
     * Make the node a child of ...
     * @param \Phalcon\Baum\PhalconNode
     *
     * @return \Phalcon\Baum\PhalconNode
     */
    public function makeChildOf($node)
    {
        return $this->moveTo($node, 'child');
    }

    /**
     * Main move method. Here we handle all node movements with the corresponding
     * lft/rgt index updates.
     *
     * @param \Phalcon\Baum\PhalconNode|int $target
     * @param string $position
     * @return \Phalcon\Baum\PhalconNode
     */
    protected function moveTo($target, $position)
    {
        return Move::to($this, $target, $position);
    }

    /**
     * Sets the depth attribute
     *
     * @return \Phalcon\Baum\PhalconNode
     */
    public function setDepth()
    {
        $self = $this;
        $this->getDI()->getDb()->begin();
        $self->refresh();

        $level = $self->getLevel();

        $self->findFirst($self->getKeyName() . ' = ' . $self->getKey())
            ->update([$this->getDepthColumnName() => $level]);
        $self->{$this->getDepthColumnName()} = $level;
        $this->getDI()->getDb()->commit();

        return $this;
    }

    /**
     * Sets the depth attribute for the current node and all of its descendants.
     *
     * @return \Phalcon\Baum\PhalconNode
     */
    public function setDepthWithSubtree()
    {
        $this->getDI()->getDb()->begin();
        $this->refresh();

        $oldDepth = ($this->readAttribute($this->getDepthColumnName())) ? $this->readAttribute($this->getDepthColumnName()) : 0;

        $newDepth = $this->getLevel();

        $this->writeAttribute($this->getDepthColumnName(), $newDepth);

        $diff = $newDepth - $oldDepth;
        if (!$this->isLeaf() && $diff != 0)
            $this->descendants()->increment($this->getDepthColumnName(), $diff);
        $this->getDI()->getDb()->commit();

        return $this;
    }

    /**
     * Get the parent column name.
     *
     * @return string
     */
    public function getParentColumnName()
    {
        return $this->parentColumn;
    }

    /**
     * Get the value of the models "parent_id" field.
     *
     * @return int
     */
    public function getParentId()
    {
        return $this->readAttribute($this->getParentColumnName());
    }

    /**
     * Maps the provided tree structure into the database.
     *
     * @param   array
     * @return  boolean
     */
    public static function buildTree($nodeList)
    {
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
    public function makeTree($nodeList)
    {
        $mapper = new SetMapper($this);

        return $mapper->map($nodeList);
    }

    /**
     * Get a new "scoped" query builder for the Node's model.
     *
     * @param  bool $excludeDeleted
     * @return \Phalcon\Mvc\Model\Query\Builder|static
     */
    public function newNestedSetQuery($excludeDeleted = true)
    {
        //$builder = $this->newQuery($excludeDeleted)->orderBy($this->getQualifiedOrderColumnName());
        $builder = $this->query();

        return $builder;
    }

    /**
     * Get all of the nodes from the database.
     *
     * @param  array $columns
     * @return Model|static[]
     */
    public static function all($columns = array('*'))
    {
        $instance = new static;

        return $instance->find(["order" => $instance->getOrderColumnName()]);
    }

    /**
     * Returns the first root node.
     *
     * @return NestedSet
     */
    public static function root()
    {
        $instance = new static;

        return $instance->findFirst(['conditions' => $instance->getParentColumnName() . ' = 0',
            'order' => $instance->getOrderColumnName()]);
    }

    /**
     * Static query scope. Returns a query scope with all root nodes.
     *
     * @return \Phalcon\Baum\PhalconNode
     */
    public static function roots()
    {
        $instance = new static;

        return $instance->find(['conditions' => $instance->getParentColumnName() . ' = 0',
            'order' => $instance->getOrderColumnName()]);
    }

    /**
     * Static query scope. Returns a query scope with all nodes which are at
     * the end of a branch.
     *
     * @return \Phalcon\Mvc\Model\Query\Builder
     */
    public static function allLeaves()
    {
        $instance = new static;

        $rgtCol = $instance->getRightColumnName();
        $lftCol = $instance->getLeftColumnName();

        return $instance->find(['conditions' => $rgtCol . ' - ' . $lftCol . ' = 1',
            'order' => $instance->getOrderColumnName()]);
    }

    /**
     * Static query scope. Returns a query scope with all nodes which are at
     * the middle of a branch (not root and not leaves).
     *
     * @return \Phalcon\Mvc\Model\Query\Builder
     */
    public static function allTrunks()
    {
        $instance = new static;

        $rgtCol = $instance->getRightColumnName();
        $lftCol = $instance->getLeftColumnName();

        return $instance->find(['conditions' => $instance->getParentColumnName() . ' != 0' . ' and ' . $rgtCol . ' - ' . $lftCol . ' != 1',
            'order' => $instance->getOrderColumnName()]);
    }

    /**
     * Checks wether the underlying Nested Set structure is valid.
     *
     * @return boolean
     */
    public static function isValidNestedSet()
    {
        $validator = new SetValidator(new static);

        return $validator->passes();
    }

    /**
     * Rebuilds the structure of the current Nested Set.
     *
     * @param  bool $force
     * @return void
     */
    public static function rebuild($force = false)
    {
        $builder = new SetBuilder(new static);

        $builder->rebuild($force);
    }

    /**
     * Returns true if this is a root node.
     *
     * @return boolean
     */
    public function isRoot()
    {
        return is_null($this->getParentId());
    }

    /**
     * Equals?
     *
     * @param \Phalcon\Baum\PhalconNode
     * @return boolean
     */
    public function equals($node)
    {
        return ($this == $node);
    }

    /**
     * Checks wether the given node is a descendant of itself. Basically, whether
     * its in the subtree defined by the left and right indices.
     *
     * @param \Phalcon\Baum\Node
     * @return boolean
     */
    public function insideSubtree($node)
    {
        return (
            $this->readAttribute($this->getLeftColumnName()) >= $node->readAttribute($node->getLeftColumnName()) &&
            $this->readAttribute($this->getLeftColumnName()) <= $node->readAttribute($node->getRightColumnName()) &&
            $this->readAttribute($this->getRightColumnName()) >= $node->readAttribute($node->getLeftColumnName()) &&
            $this->readAttribute($this->getRightColumnName()) <= $node->readAttribute($node->getRightColumnName())
        );
    }

    /**
     * Checkes if the given node is in the same scope as the current one.
     *
     * @param \Phalcon\Baum\Node
     * @return boolean
     */
    public function inSameScope($other)
    {
        foreach ($this->getScopedColumns() as $fld) {
            if ($this->$fld != $other->$fld) return false;
        }

        return true;
    }

    /**
     * Returns the level of this node in the tree.
     * Root level is 0.
     *
     * @return int
     */
    public function getLevel()
    {
        if (!$this->getParentId())
            return 0;

        return $this->computeLevel();
    }

    /**
     * Compute current node level. If could not move past ourseleves return
     * our ancestor count, otherwhise get the first parent level + the computed
     * nesting.
     *
     * @return integer
     */
    protected function computeLevel()
    {
        list($node, $nesting) = $this->determineDepth($this);

        if ($node->equals($this)) {
            return $this->getAncestors()->count();
        }

        return $node->getLevel() + $nesting;
    }

    /**
     * Return an array with the last node we could reach and its nesting level
     *
     * @param   \Phalcon\Baum\PhalconNode $node
     * @param   integer $nesting
     * @return  array
     */
    protected function determineDepth($node, $nesting = 0)
    {
        // Traverse back up the ancestry chain and add to the nesting level count
        while ($parent = $node->parent) {
            $nesting = $nesting + 1;

            $node = $parent;
        }

        return array($node, $nesting);
    }

    /**
     * Returns true if this is a leaf node (end of a branch).
     *
     * @return boolean
     */
    public function isLeaf()
    {
        return $this->exists() && ($this->getRight() - $this->getLeft() == 1);
    }

    /**
     * Returns true if node is a descendant.
     *
     * @param \Phalcon\Baum\PhalconNode
     * @return boolean
     */
    public function isDescendantOf($other)
    {
        return (
            $this->getLeft() > $other->getLeft() &&
            $this->getLeft() < $other->getRight() &&
            $this->inSameScope($other)
        );
    }

    /**
     * Returns true if node is self or a descendant.
     *
     * @param \Phalcon\Baum\PhalconNode
     * @return boolean
     */
    public function isSelfOrDescendantOf($other)
    {
        return (
            $this->getLeft() >= $other->getLeft() &&
            $this->getLeft() < $other->getRight() &&
            $this->inSameScope($other)
        );
    }

    /**
     * Returns true if node is an ancestor.
     *
     * @param \Phalcon\Baum\PhalconNode
     * @return boolean
     */
    public function isAncestorOf($other)
    {
        return (
            $this->getLeft() < $other->getLeft() &&
            $this->getRight() > $other->getLeft() &&
            $this->inSameScope($other)
        );
    }

    /**
     * Returns true if node is self or an ancestor.
     *
     * @param \Phalcon\Baum\PhalconNode
     * @return boolean
     */
    public function isSelfOrAncestorOf($other)
    {
        return (
            $this->getLeft() <= $other->getLeft() &&
            $this->getRight() > $other->getLeft() &&
            $this->inSameScope($other)
        );
    }

    /**
     * Returns true if this is a trunk node (not root or leaf).
     *
     * @return boolean
     */
    public function isTrunk()
    {
        return !$this->isRoot() && !$this->isLeaf();
    }

    /**
     * Returns true if this is a child node.
     *
     * @return boolean
     */
    public function isChild()
    {
        return !$this->isRoot();
    }

    /**
     * Returns the root node starting at the current node.
     *
     * @return NestedSet
     */
    public function getRoot()
    {
        if ($this->exists()) {
            $criteria = $this->query()->orderBy($this->getOrderColumnName())
                ->where($this->getLeftColumnName() . ' <= ' . $this->getLeft())
                ->andWhere($this->getRightColumnName() . ' >= ' . $this->getRight())
                ->andWhere($this->getParentColumnName() . ' = 0');
            return $this->findFirst($criteria->getParams());
        } else {
            $parentId = $this->getParentId();

            if ($parentId && $currentParent = static::findFirst($this->getParentColumnName() . ' = ' . $parentId)) {
                return $currentParent->getRoot();
            } else {
                return $this;
            }
        }
    }

    /**
     * Instance scope which targes all the ancestor chain nodes including
     * the current one.
     *
     * @return \Phalcon\Mvc\Model\Query\Builder
     */
    public function ancestorsAndSelf()
    {
        return $this->query()->orderBy($this->getOrderColumnName())
            ->where($this->getLeftColumnName() . ' <= ' . $this->getLeft())
            ->andWhere($this->getRightColumnName() . ' >= ' . $this->getRight());
    }

    /**
     * Get all the ancestor chain from the database including the current node.
     *
     * @param  array $columns
     * @return Model
     */
    public function getAncestorsAndSelf($columns = array('*'))
    {
        return $this->ancestorsAndSelf()->columns($columns)->execute();
    }

    /**
     * Get all the ancestor chain from the database including the current node
     * but without the root node.
     *
     * @param  array $columns
     * @return Model
     */
    public function getAncestorsAndSelfWithoutRoot($columns = array('*'))
    {
        return $this->ancestorsAndSelf()->andWhere($this->getKeyName() . ' != ' . $this->getRoot()->{$this->primaryKey})->columns($columns)->execute();
    }

    /**
     * Instance scope which targets all the ancestor chain nodes excluding
     * the current one.
     *
     * @return \Phalcon\Mvc\Model\Query\Builder
     */
    public function ancestors()
    {
        return $this->ancestorsAndSelf()->andWhere($this->getKeyName() . ' != ' . $this->{$this->primaryKey});
    }

    /**
     * Get all the ancestor chain from the database excluding the current node.
     *
     * @param  array $columns
     * @return Model
     */
    public function getAncestors($columns = array('*'))
    {
        return $this->ancestors()->columns($columns)->execute();
    }

    /**
     * Get all the ancestor chain from the database excluding the current node
     * and the root node (from the current node's perspective).
     *
     * @param  array $columns
     * @return Model
     */
    public function getAncestorsWithoutRoot($columns = array('*'))
    {
        return $this->ancestors()->andWhere($this->getKeyName() . ' != ' . $this->getRoot()->{$this->primaryKey})
            ->columns($columns)->execute();
    }

    /**
     * Instance scope which targets all children of the parent, including self.
     *
     * @return \Phalcon\Mvc\Model\Query\Builder
     */
    public function siblingsAndSelf()
    {
        return $this->query()->orderBy($this->getOrderColumnName())
            ->where($this->getParentColumnName() . ' = ' . $this->getParentId());
    }

    /**
     * Get all children of the parent, including self.
     *
     * @param  array $columns
     * @return Model
     */
    public function getSiblingsAndSelf($columns = array('*'))
    {
        return $this->siblingsAndSelf()->columns($columns)->execute();
    }

    /**
     * Instance scope targeting all children of the parent, except self.
     *
     * @return \Phalcon\Mvc\Model\Query\Builder
     */
    public function siblings()
    {
        return $this->siblingsAndSelf()->andWhere($this->getKeyName() . ' != ' . $this->{$this->primaryKey});
    }

    /**
     * Return all children of the parent, except self.
     *
     * @param  array $columns
     * @return Model
     */
    public function getSiblings($columns = array('*'))
    {
        return $this->siblings()->columns($columns)->execute();
    }

    /**
     * Instance scope targeting all of its nested children which do not have
     * children.
     *
     * @return \Phalcon\Mvc\Model\Query\Builder
     */
    public function leaves()
    {
        $rgtCol = $this->getRightColumnName();
        $lftCol = $this->getLeftColumnName();

        return $this->descendants()
            ->andWhere($rgtCol . ' - ' . $lftCol . ' = 1');
    }

    /**
     * Return all of its nested children which do not have children.
     *
     * @param  array $columns
     * @return Model
     */
    public function getLeaves($columns = array('*'))
    {
        return $this->leaves()->columns($columns)->execute();
    }

    /**
     * Instance scope targeting all of its nested children which are between the
     * root and the leaf nodes (middle branch).
     *
     * @return \Phalcon\Mvc\Model\Query\Builder
     */
    public function trunks()
    {
        $rgtCol = $this->getRightColumnName();
        $lftCol = $this->getLeftColumnName();

        return $this->descendants()
            ->andWhere($this->getParentColumnName() . ' != 0')
            ->andWhere($rgtCol . ' - ' . $lftCol . ' != 1');
    }

    /**
     * Return all of its nested children which are trunks.
     *
     * @param  array $columns
     * @return Model
     */
    public function getTrunks($columns = array('*'))
    {
        return $this->trunks()->columns($columns)->execute();
    }

    /**
     * Scope targeting itself and all of its nested children.
     *
     * @return \Phalcon\Mvc\Model\Query\Builder
     */
    public function descendantsAndSelf()
    {
        return $this->newNestedSetQuery()->orderBy($this->getOrderColumnName() . ' desc')
            ->where($this->getLeftColumnName() . '>=' . $this->getLeft())
            ->andWhere($this->getLeftColumnName() . '<' . $this->getRight());
    }

    /**
     * Retrieve all nested children an self.
     *
     * @param  array $columns
     * @return Model
     */
    public function getDescendantsAndSelf($columns = array('*'))
    {
        if (is_array($columns))
            return $this->descendantsAndSelf()->columns($columns)->execute();

        $arguments = func_get_args();

        $limit = intval(array_shift($arguments));
        $columns = array_shift($arguments) ?: array('*');

        return $this->descendantsAndSelf()->limitDepth($limit)->columns($columns)->execute();
    }

    /**
     * Set of all children & nested children.
     *
     * @return \Phalcon\Mvc\Model\Query\Builder
     */
    public function descendants()
    {
        return $this->descendantsAndSelf()->andWhere($this->getKeyName() . ' != ' . $this->{$this->primaryKey});
    }

    /**
     * Retrieve all of its children & nested children.
     *
     * @param  array $columns
     * @return Model
     */
    public function getDescendants($columns = array('*'))
    {
        if (is_array($columns))
            return $this->descendants()->columns($columns)->execute();

        $arguments = func_get_args();

        $limit = intval(array_shift($arguments));
        $columns = array_shift($arguments) ?: array('*');

        return $this->descendants()->limitDepth($limit)->columns($columns)->execute();
    }

    /**
     * Set of "immediate" descendants (aka children), alias for the children relation.
     *
     * @return \Phalcon\Mvc\Model\Query\Builder
     */
    public function immediateDescendants()
    {
        return $this->children;
    }

    /**
     * Retrive all of its "immediate" descendants.
     *
     * @param array $columns
     * @return Model
     */
    public function getImmediateDescendants($columns = array('*'))
    {
        return $this->children;
    }

    /**
     * Provides a depth level limit for the query.
     *
     * @param   query   \Phalcon\Mvc\Model\Query\Builder
     * @param   int     limit
     * @return  \Phalcon\Mvc\Model\Query\Builder
     */
    public function limitDepth($query, $limit)
    {
        $depth = $this->exists() ? $this->getDepth() : $this->getLevel();
        $max = $depth + $limit;

        return $query->betweenWhere($this->getDepthColumnName(), $depth, $max);
    }
}