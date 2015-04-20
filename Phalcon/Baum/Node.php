<?php namespace Phalcon\Baum;

//use Phalcon\Baum\Extensions\Eloquent\Collection;
//use Phalcon\Baum\Extensions\Eloquent\Model;
use Phalcon\Mvc\Model;

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
abstract class Node extends Model
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
     * Guard NestedSet fields from mass-assignment.
     *
     * @var array
     */
    protected $guarded = array('id', 'parent_id', 'lft', 'rgt', 'depth');

    /**
     * Indicates whether we should move to a new parent.
     *
     * @var int
     */
    protected static $moveToNewParentId = NULL;

    /**
     * Columns which restrict what we consider our Nested Set list
     *
     * @var array
     */
    protected $scoped = array();

    /**
     * The "booting" method of the model.
     *
     * We'll use this method to register event listeners on a Node instance as
     * suggested in the beta documentation...
     *
     * TODO:
     *
     *    - Find a way to avoid needing to declare the called methods "public"
     *    as registering the event listeners *inside* this methods does not give
     *    us an object context.
     *
     * Events:
     *
     *    1. "creating": Before creating a new Node we'll assign a default value
     *    for the left and right indexes.
     *
     *    2. "saving": Before saving, we'll perform a check to see if we have to
     *    move to another parent.
     *
     *    3. "saved": Move to the new parent after saving if needed and re-set
     *    depth.
     *
     *    4. "deleting": Before delete we should prune all children and update
     *    the left and right indexes for the remaining nodes.
     *
     *    5. (optional) "restoring": Before a soft-delete node restore operation,
     *    shift its siblings.
     *
     *    6. (optional) "restore": After having restored a soft-deleted node,
     *    restore all of its descendants.
     *
     * @return void
     */
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($node) {
            $node->setDefaultLeftAndRight();
        });

        static::saving(function ($node) {
            $node->storeNewParent();
        });

        static::saved(function ($node) {
            $node->moveToNewParent();
            $node->setDepth();
        });

        static::deleting(function ($node) {
            $node->destroyDescendants();
        });

        if (static::softDeletesEnabled()) {
            static::restoring(function ($node) {
                $node->shiftSiblingsForRestore();
            });

            static::restored(function ($node) {
                $node->restoreDescendants();
            });
        }
    }

    /**
     * get primary key name
     */
    public function getKeyName(){
        return $this->primaryKey;
    }

    /**
     * Indicates if the model exists
     */
   protected function exists(){
       return $this->getDirtyState() == Model::DIRTY_STATE_PERSISTENT;
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
     * Get the table qualified parent column name.
     *
     * @return string
     */
    public function getQualifiedParentColumnName()
    {
        return $this->getSource() . '.' . $this->getParentColumnName();
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
     * Get the "left" field column name.
     *
     * @return string
     */
    public function getLeftColumnName()
    {
        return $this->leftColumn;
    }

    /**
     * Get the table qualified "left" field column name.
     *
     * @return string
     */
    public function getQualifiedLeftColumnName()
    {
        return $this->getSource() . '.' . $this->getLeftColumnName();
    }

    /**
     * Get the value of the model's "left" field.
     *
     * @return int
     */
    public function getLeft()
    {
        return $this->readAttribute($this->getLeftColumnName());
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
     * Get the table qualified "right" field column name.
     *
     * @return string
     */
    public function getQualifiedRightColumnName()
    {
        return $this->getSource() . '.' . $this->getRightColumnName();
    }

    /**
     * Get the value of the model's "right" field.
     *
     * @return int
     */
    public function getRight()
    {
        return $this->readAttribute($this->getRightColumnName());
    }

    /**
     * Get the "depth" field column name.
     *
     * @return string
     */
    public function getDepthColumnName()
    {
        return $this->depthColumn;
    }

    /**
     * Get the table qualified "depth" field column name.
     *
     * @return string
     */
    public function getQualifiedDepthColumnName()
    {
        return $this->getSource() . '.' . $this->getDepthColumnName();
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
     * Get the table qualified "order" field column name.
     *
     * @return string
     */
    public function getQualifiedOrderColumnName()
    {
        return $this->getSource() . '.' . $this->getOrderColumnName();
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
     * Get the column names which define our scope
     *
     * @return array
     */
    public function getScopedColumns()
    {
        return (array)$this->scoped;
    }

    /**
     * Get the qualified column names which define our scope
     *
     * @return array
     */
    public function getQualifiedScopedColumns()
    {
        if (!$this->isScoped())
            return $this->getScopedColumns();

        $prefix = $this->getSource() . '.';

        return array_map(function ($c) use ($prefix) {
            return $prefix . $c;
        }, $this->getScopedColumns());
    }

    /**
     * Returns wether this particular node instance is scoped by certain fields
     * or not.
     *
     * @return boolean
     */
    public function isScoped()
    {
        return !!(count($this->getScopedColumns()) > 0);
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
        $builder = $this->getDI()->getModelsManager->createBuilder()->orderBy($this->getOrderColumnName());

        if ($this->isScoped()) {
            foreach ($this->scoped as $scopeFld)
                $builder->andWhere($scopeFld . ' = ' . $this->$scopeFld);
        }

        return $builder;
    }

    /**
     * Overload new Collection
     *
     * @param array $models
     * @return \Baum\Extensions\Eloquent\Collection
     */
    public function newCollection(array $models = array())
    {
        return new Collection($models);
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

        return $instance->getDI()->getModelsManager->createBuilder()
            ->orderBy($instance->getOrderColumnName())
            ->getQuery()
            ->execute();
    }

    /**
     * Returns the first root node.
     *
     * @return NestedSet
     */
    public static function root()
    {
        return static::roots()->first();
    }

    /**
     * Static query scope. Returns a query scope with all root nodes.
     *
     * @return \Phalcon\Mvc\Model\Query\Builder
     */
    public static function roots()
    {
        $instance = new static;

        return $instance->getDI()->getModelsManager->createBuilder()
            ->where($instance->getParentColumnName() . ' = NULL')
            ->orderBy($instance->getOrderColumnName());
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
        $lftCol = $instance->getQualifiedLeftColumnName();

        return $instance->getDI()->getModelsManager->createBuilder()
            ->where($rgtCol . ' - ' . $lftCol . ' = 1')
            ->orderBy($instance->getOrderColumnName());
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

        return $instance->getDI()->getModelsManager->createBuilder()
            ->where($instance->getParentColumnName() . ' != NULL')
            ->andWhere($rgtCol . ' - ' . $lftCol . ' != 1')
            ->orderBy($instance->getOrderColumnName());
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
     * Maps the provided tree structure into the database.
     *
     * @param   array|\Illuminate\Support\Contracts\ArrayableInterface
     * @return  boolean
     */
    public static function buildTree($nodeList)
    {
        return with(new static)->makeTree($nodeList);
    }

    /**
     * Query scope which extracts a certain node object from the current query
     * expression.
     * @param   query   \Phalcon\Mvc\Model\Query\Builder
     * @param   node    \Phalcon\Node
     *
     * @return \Phalcon\Mvc\Model\Query\Builder
     */
    public function withoutNode($query, $node)
    {
        return $query->andWhere($node->getKeyName() . ' != ' . $node->{$this->primaryKey});
    }

    /**
     * Extracts current node (self) from current query expression.
     * @param   query   \Phalcon\Mvc\Model\Query\Builder
     *
     * @return \Phalcon\Mvc\Model\Query\Builder
     */
    public function withoutSelf($query)
    {
        return $this->withoutNode($query, $this);
    }

    /**
     * Extracts first root (from the current node p-o-v) from current query
     * expression.
     * @param   query   \Phalcon\Mvc\Model\Query\Builder
     *
     * @return \Phalcon\Mvc\Model\Query\Builder
     */
    public function withoutRoot($query)
    {
        return $this->withoutNode($query, $this->getRoot());
    }

    /**
     * Provides a depth level limit for the query.
     *
     * @param   query   \Phalcon\Mvc\Model\Query\Builder
     * @param   limit   integer
     * @return  \Phalcon\Mvc\Model\Query\Builder
     */
    public function limitDepth($query, $limit)
    {
        $depth = $this->exists() ? $this->getDepth() : $this->getLevel();
        $max = $depth + $limit;

        return $query->betweenWhere($this->getDepthColumnName(), $depth, $max);
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
     * Returns true if this is a leaf node (end of a branch).
     *
     * @return boolean
     */
    public function isLeaf()
    {
        return $this->exists() && ($this->getRight() - $this->getLeft() == 1);
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
            return $this->ancestorsAndSelf()->andWhere($this->getParentColumnName())->first();
        } else {
            $parentId = $this->getParentId();

            if (!is_null($parentId) && $currentParent = static::findFirst($this->getParentColumnName() . ' = ' . $parentId)) {
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
        return $this->newNestedSetQuery()
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
        return $this->ancestorsAndSelf()->columns($columns)->getQuery()->execute();
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
        return $this->ancestorsAndSelf()->withoutRoot()->columns($columns)->getQuery()->execute();
    }

    /**
     * Instance scope which targets all the ancestor chain nodes excluding
     * the current one.
     *
     * @return \Phalcon\Mvc\Model\Query\Builder
     */
    public function ancestors()
    {
        return $this->ancestorsAndSelf()->withoutSelf();
    }

    /**
     * Get all the ancestor chain from the database excluding the current node.
     *
     * @param  array $columns
     * @return Model
     */
    public function getAncestors($columns = array('*'))
    {
        return $this->ancestors()->columns($columns)->getQuery()->execute();
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
        return $this->ancestors()->withoutRoot()->columns($columns)->getQuery()->execute();
    }

    /**
     * Instance scope which targets all children of the parent, including self.
     *
     * @return \Phalcon\Mvc\Model\Query\Builder
     */
    public function siblingsAndSelf()
    {
        return $this->newNestedSetQuery()
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
        return $this->siblingsAndSelf()->columns($columns)->getQuery()->execute();
    }

    /**
     * Instance scope targeting all children of the parent, except self.
     *
     * @return \Phalcon\Mvc\Model\Query\Builder
     */
    public function siblings()
    {
        return $this->siblingsAndSelf()->withoutSelf();
    }

    /**
     * Return all children of the parent, except self.
     *
     * @param  array $columns
     * @return Model
     */
    public function getSiblings($columns = array('*'))
    {
        return $this->siblings()->columns($columns)->getQuery()->execute();
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
            ->where($rgtCol . ' - ' . $lftCol . ' = 1');
    }

    /**
     * Return all of its nested children which do not have children.
     *
     * @param  array $columns
     * @return Model
     */
    public function getLeaves($columns = array('*'))
    {
        return $this->leaves()->columns($columns)->getQuery()->execute();
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
            ->where($this->getQualifiedParentColumnName() . ' != NULL')
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
        return $this->trunks()->columns($columns)->getQuery()->execute();
    }

    /**
     * Scope targeting itself and all of its nested children.
     *
     * @return \Phalcon\Mvc\Model\Query\Builder
     */
    public function descendantsAndSelf()
    {
        return $this->newNestedSetQuery()
            ->where($this->getLeftColumnName(). '>='. $this->getLeft())
            ->adnWhere($this->getLeftColumnName() . '<' . $this->getRight());
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
            return $this->descendantsAndSelf()->columns($columns)->getQuery()->execute();

        $arguments = func_get_args();

        $limit = intval(array_shift($arguments));
        $columns = array_shift($arguments) ?: array('*');

        return $this->descendantsAndSelf()->limitDepth($limit)->columns($columns)->getQuery()->execute();
    }

    /**
     * Set of all children & nested children.
     *
     * @return \Phalcon\Mvc\Model\Query\Builder
     */
    public function descendants()
    {
        return $this->descendantsAndSelf()->withoutSelf();
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
            return $this->descendants()->columns($columns)->getQuery()->execute();

        $arguments = func_get_args();

        $limit = intval(array_shift($arguments));
        $columns = array_shift($arguments) ?: array('*');

        return $this->descendants()->limitDepth($limit)->columns($columns)->getQuery()->execute();
    }

    /**
     * Set of "immediate" descendants (aka children), alias for the children relation.
     *
     * @return \Phalcon\Mvc\Model\Query\Builder
     */
    public function immediateDescendants()
    {
        return $this->children();
    }

    /**
     * Retrive all of its "immediate" descendants.
     *
     * @param array $columns
     * @return Model
     */
    public function getImmediateDescendants($columns = array('*'))
    {
        return $this->children()->columns($columns)->getQuery()->execute();
    }

    /**
     * Returns the level of this node in the tree.
     * Root level is 0.
     *
     * @return int
     */
    public function getLevel()
    {
        if (is_null($this->getParentId()))
            return 0;

        return $this->computeLevel();
    }

    /**
     * Returns true if node is a descendant.
     *
     * @param NestedSet
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
     * @param NestedSet
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
     * @param NestedSet
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
     * @param NestedSet
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
     * Returns the first sibling to the left.
     *
     * @return NestedSet
     */
    public function getLeftSibling()
    {
        return $this->siblings()
            ->where($this->getLeftColumnName() . ' < ' . $this->getLeft())
            ->orderBy($this->getOrderColumnName() . ' asc')
            ->limit(1)
            ->getQuery()->execute();
    }

    /**
     * Returns the first sibling to the right.
     *
     * @return NestedSet
     */
    public function getRightSibling()
    {
        return $this->siblings()
            ->where($this->getLeftColumnName() . '>' . $this->getLeft())
            ->findFirst();
    }

    /**
     * Find the left sibling and move to left of it.
     *
     * @return \Phalcon\Baum\Node
     */
    public function moveLeft()
    {
        return $this->moveToLeftOf($this->getLeftSibling());
    }

    /**
     * Find the right sibling and move to the right of it.
     *
     * @return \Phalcon\Baum\Node
     */
    public function moveRight()
    {
        return $this->moveToRightOf($this->getRightSibling());
    }

    /**
     * Move to the node to the left of ...
     * @param \Phalcon\Mvc\Model
     * @return \Phalcon\Baum\Node
     */
    public function moveToLeftOf($node)
    {
        return $this->moveTo($node, 'left');
    }

    /**
     * Move to the node to the right of ...
     * @param \Phalcon\Mvc\Model
     *
     * @return \PHalcon\Baum\Node
     */
    public function moveToRightOf($node)
    {
        return $this->moveTo($node, 'right');
    }

    /**
     * Alias for moveToRightOf
     *
     * @return \Phalcon\Baum\Node
     */
    public function makeNextSiblingOf($node)
    {
        return $this->moveToRightOf($node);
    }

    /**
     * Alias for moveToRightOf
     *
     * @return \Phalcon\Baum\Node
     */
    public function makeSiblingOf($node)
    {
        return $this->moveToRightOf($node);
    }

    /**
     * Alias for moveToLeftOf
     *
     * @return \Phalcon\Baum\Node
     */
    public function makePreviousSiblingOf($node)
    {
        return $this->moveToLeftOf($node);
    }

    /**
     * Make the node a child of ...
     *
     * @return \Phalcon\Baum\Node
     */
    public function makeChildOf($node)
    {
        return $this->moveTo($node, 'child');
    }

    /**
     * Make the node the first child of ...
     *
     * @return \Baum\Node
     */
    public function makeFirstChildOf($node)
    {
        if ($node->children()->count() == 0)
            return $this->makeChildOf($node);

        return $this->moveToLeftOf($node->children()->first());
    }

    /**
     * Make the node the last child of ...
     *
     * @return \Phalcon\Baum\Node
     */
    public function makeLastChildOf($node)
    {
        return $this->makeChildOf($node);
    }

    /**
     * Make current node a root node.
     *
     * @return \Phalcon\Baum\Node
     */
    public function makeRoot()
    {
        return $this->moveTo($this, 'root');
    }

    /**
     * Equals?
     *
     * @param \Phalcon\Baum\Node
     * @return boolean
     */
    public function equals($node)
    {
        return ($this == $node);
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
     * Checks wether the given node is a descendant of itself. Basically, whether
     * its in the subtree defined by the left and right indices.
     *
     * @param \Phalcon\Baum\Node
     * @return boolean
     */
    public function insideSubtree($node)
    {
        return (
            $this->getLeft() >= $node->getLeft() &&
            $this->getLeft() <= $node->getRight() &&
            $this->getRight() >= $node->getLeft() &&
            $this->getRight() <= $node->getRight()
        );
    }

    /**
     * Sets default values for left and right fields.
     *
     * @return void
     */
    public function setDefaultLeftAndRight()
    {
        $withHighestRight = $this->newNestedSetQuery()->reOrderBy($this->getRightColumnName(), 'desc')->take(1)->sharedLock()->first();

        $maxRgt = 0;
        if (!is_null($withHighestRight)) $maxRgt = $withHighestRight->getRight();

        $this->setAttribute($this->getLeftColumnName(), $maxRgt + 1);
        $this->setAttribute($this->getRightColumnName(), $maxRgt + 2);
    }

    /**
     * Store the parent_id if the attribute is modified so as we are able to move
     * the node to this new parent after saving.
     *
     * @return void
     */
    public function storeNewParent()
    {
        if ($this->isDirty($this->getParentColumnName()) && ($this->exists || !$this->isRoot()))
            static::$moveToNewParentId = $this->getParentId();
        else
            static::$moveToNewParentId = FALSE;
    }

    /**
     * Move to the new parent if appropiate.
     *
     * @return void
     */
    public function moveToNewParent()
    {
        $pid = static::$moveToNewParentId;

        if (is_null($pid))
            $this->makeRoot();
        else if ($pid !== FALSE)
            $this->makeChildOf($pid);
    }

    /**
     * Sets the depth attribute
     *
     * @return \Baum\Node
     */
    public function setDepth()
    {
        $self = $this;

        $this->getConnection()->transaction(function () use ($self) {
            $self->reload();

            $level = $self->getLevel();

            $self->newNestedSetQuery()->where($self->getKeyName(), '=', $self->getKey())->update(array($self->getDepthColumnName() => $level));
            $self->setAttribute($self->getDepthColumnName(), $level);
        });

        return $this;
    }

    /**
     * Sets the depth attribute for the current node and all of its descendants.
     *
     * @return \Baum\Node
     */
    public function setDepthWithSubtree()
    {
        $self = $this;

        $this->getConnection()->transaction(function () use ($self) {
            $self->reload();

            $self->descendantsAndSelf()->select($self->getKeyName())->lockForUpdate()->get();

            $oldDepth = !is_null($self->getDepth()) ? $self->getDepth() : 0;

            $newDepth = $self->getLevel();

            $self->newNestedSetQuery()->where($self->getKeyName(), '=', $self->getKey())->update(array($self->getDepthColumnName() => $newDepth));
            $self->setAttribute($self->getDepthColumnName(), $newDepth);

            $diff = $newDepth - $oldDepth;
            if (!$self->isLeaf() && $diff != 0)
                $self->descendants()->increment($self->getDepthColumnName(), $diff);
        });

        return $this;
    }

    /**
     * Prunes a branch off the tree, shifting all the elements on the right
     * back to the left so the counts work.
     *
     * @return void;
     */
    public function destroyDescendants()
    {
        if (is_null($this->getRight()) || is_null($this->getLeft())) return;

        $self = $this;

        $this->getConnection()->transaction(function () use ($self) {
            $self->reload();

            $lftCol = $self->getLeftColumnName();
            $rgtCol = $self->getRightColumnName();
            $lft = $self->getLeft();
            $rgt = $self->getRight();

            // Apply a lock to the rows which fall past the deletion point
            $self->newNestedSetQuery()->where($lftCol, '>=', $lft)->select($self->getKeyName())->lockForUpdate()->get();

            // Prune children
            $self->newNestedSetQuery()->where($lftCol, '>', $lft)->where($rgtCol, '<', $rgt)->delete();

            // Update left and right indexes for the remaining nodes
            $diff = $rgt - $lft + 1;

            $self->newNestedSetQuery()->where($lftCol, '>', $rgt)->decrement($lftCol, $diff);
            $self->newNestedSetQuery()->where($rgtCol, '>', $rgt)->decrement($rgtCol, $diff);
        });
    }

    /**
     * "Makes room" for the the current node between its siblings.
     *
     * @return void
     */
    public function shiftSiblingsForRestore()
    {
        if (is_null($this->getRight()) || is_null($this->getLeft())) return;

        $self = $this;

        $this->getConnection()->transaction(function () use ($self) {
            $lftCol = $self->getLeftColumnName();
            $rgtCol = $self->getRightColumnName();
            $lft = $self->getLeft();
            $rgt = $self->getRight();

            $diff = $rgt - $lft + 1;

            $self->newNestedSetQuery()->where($lftCol, '>=', $lft)->increment($lftCol, $diff);
            $self->newNestedSetQuery()->where($rgtCol, '>=', $lft)->increment($rgtCol, $diff);
        });
    }

    /**
     * Restores all of the current node's descendants.
     *
     * @return void
     */
    public function restoreDescendants()
    {
        if (is_null($this->getRight()) || is_null($this->getLeft())) return;

        $self = $this;

        $this->getConnection()->transaction(function () use ($self) {
            $self->newNestedSetQuery()
                ->withTrashed()
                ->where($self->getLeftColumnName(), '>', $self->getLeft())
                ->where($self->getRightColumnName(), '<', $self->getRight())
                ->update(array(
                    $self->getDeletedAtColumn() => null,
                    $self->getUpdatedAtColumn() => $self->{$self->getUpdatedAtColumn()}
                ));
        });
    }

    /**
     * Return an key-value array indicating the node's depth with $seperator
     *
     * @return Array
     */
    public static function getNestedList($column, $key = null, $seperator = ' ')
    {
        $instance = new static;

        $key = $key ?: $instance->getKeyName();
        $depthColumn = $instance->getDepthColumnName();

        $nodes = $instance->newNestedSetQuery()->get()->toArray();

        return array_combine(array_map(function ($node) use ($key) {
            return $node[$key];
        }, $nodes), array_map(function ($node) use ($seperator, $depthColumn, $column) {
            return str_repeat($seperator, $node[$depthColumn]) . $node[$column];
        }, $nodes));
    }

    /**
     * Maps the provided tree structure into the database using the current node
     * as the parent. The provided tree structure will be inserted/updated as the
     * descendancy subtree of the current node instance.
     *
     * @param   array|\Illuminate\Support\Contracts\ArrayableInterface
     * @return  boolean
     */
    public function tee($nodeList)
    {
        $mapper = new SetMapper($this);

        return $mapper->map($nodeList);
    }

    /**
     * Main move method. Here we handle all node movements with the corresponding
     * lft/rgt index updates.
     *
     * @param \Phalcon\Baum\Node|int $target
     * @param string $position
     * @return \Phalcon\Baum\Node
     */
    protected function moveTo($target, $position)
    {
        return Move::to($this, $target, $position);
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

        if ($node->equals($this))
            return $this->ancestors()->count();

        return $node->getLevel() + $nesting;
    }

    /**
     * Return an array with the last node we could reach and its nesting level
     *
     * @param   \Phalcon\Baum\Node $node
     * @param   integer $nesting
     * @return  array
     */
    protected function determineDepth($node, $nesting = 0)
    {
        // Traverse back up the ancestry chain and add to the nesting level count
        while ($parent = $node->parent()->first()) {
            $nesting = $nesting + 1;

            $node = $parent;
        }

        return array($node, $nesting);
    }

}
