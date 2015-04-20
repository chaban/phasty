<?php namespace Phalcon\Baum;

use Phalcon\Baum\PhalconNode;
use utilphp\util;
use Phalcon\Db\RawValue;

/**
 * Move
 */
class Move
{

    /**
     * Node on which the move operation will be performed
     *
     * @var \Phalcon\Baum\PhalconNode
     */
    protected $node = NULL;

    /**
     * Destination node
     *
     * @var \Phalcon\Baum\PhalconNode | int
     */
    protected $target = NULL;

    /**
     * Move target position, one of: child, left, right, root
     *
     * @var string
     */
    protected $position = NULL;

    /**
     * Memoized 1st boundary.
     *
     * @var int
     */
    protected $_bound1 = NULL;

    /**
     * Memoized 2nd boundary.
     *
     * @var int
     */
    protected $_bound2 = NULL;

    /**
     * Memoized boundaries array.
     *
     * @var array
     */
    protected $_boundaries = NULL;

    /**
     * Create a new Move class instance.
     *
     * @param   PhalconNode $node
     * @param   PhalconNode|int $target
     * @param   string $position
     */
    public function __construct($node, $target, $position)
    {
        $this->node = $node;
        //$this->target = $this->resolveNode($target);
        $this->target = $this->resolveNode($target);
        $this->target->refresh();
        $this->position = $position;
    }

    /**
     * Resolves suplied node. Basically returns the node unchanged if
     * supplied parameter is an instance of \Baum\Node. Otherwise it will try
     * to find the node in the database.
     *
     * @param   \Phalcon\Baum\PhalconNode|int
     * @return  \Phalcon\Baum\PhalconNode
     */
    protected function resolveNode($node) {
        if ( $node instanceof PhalconNode ) return $node;
        return $this->node->query()->orderBy($this->node->getOrderColumnName())->execute()->getFirst();
    }

    /**
     * Easy static accessor for performing a move operation.
     *
     * @param   \Phalcon\Baum\PhalconNode $node
     * @param   \Phalcon\Baum\PhalconNode|int $target
     * @param   string $position
     * @return \Phalcon\Baum\PhalconNode
     */
    public static function to($node, $target, $position)
    {
        $instance = new static($node, $target, $position);

        return $instance->perform();
    }

    /**
     * Perform the move operation.
     *
     * @return \Phalcon\Baum\PhalconNode
     */
    public function perform()
    {
        $this->guardAgainstImpossibleMove();

        if ($this->hasChange()) {

            $this->updateStructure();

            //$this->target->refresh();

            //$this->node->setDepthWithSubtree();

            //$this->node->refresh();
        }

        return $this->node;
    }

    /**
     * Runs the SQL query associated with the update of the indexes affected
     * by the move operation.
     *
     * @return int
     */
    public function updateStructure()
    {
        list($a, $b, $c, $d) = $this->boundaries();

        // select the rows between the leftmost & the rightmost boundaries and apply a lock
        //$this->applyLockBetween($a, $d);

        $currentId = $this->node->readAttribute($this->node->getKeyName());
        $parentId = $this->node->readAttribute($this->node->getParentColumnName());
        $leftColumn = $this->node->getLeftColumnName();
        $rightColumn = $this->node->getRightColumnName();
        $parentColumn = $this->node->getParentColumnName();
        $wrappedId = $this->node->getKeyName();

        $lftSql = "CASE
      WHEN '$leftColumn' BETWEEN $a AND $b THEN '$leftColumn' + $d - $b
      WHEN '$leftColumn' BETWEEN $c AND $d THEN '$leftColumn' + $a - $c
      ELSE '$leftColumn' END";
        $lftSql = preg_replace('/\s+/', ' ', $lftSql);

        $rgtSql = "CASE
      WHEN '$rightColumn' BETWEEN $a AND $b THEN '$rightColumn' + $d - $b
      WHEN '$rightColumn' BETWEEN $c AND $d THEN '$rightColumn' + $a - $c
      ELSE '$rightColumn' END";
        $rgtSql = preg_replace('/\s+/', ' ', $rgtSql);

        $parentSql = "CASE
      WHEN '$wrappedId' = $currentId THEN $parentId
      ELSE '$parentColumn' END";
        $parentSql = preg_replace('/\s+/', ' ', $parentSql);

        $updateConditions = array(
            $leftColumn => new RawValue($lftSql),
            $rightColumn => new RawValue($rgtSql),
            $parentColumn => new RawValue($parentSql)
        );

        $models = $this->node->query()->orderBy($this->node->getOrderColumnName())
            ->where("(".$leftColumn . " between $a and $d) or (" . $rightColumn . " between $a and $d)")
            ->execute();
            foreach($models as $model){
                $model->update($updateConditions);
            }
        return true;
    }

    /**
     * Check wether the current move is possible and if not, rais an exception.
     *
     * @return void
     */
    protected function guardAgainstImpossibleMove()
    {
        if (!$this->node->exists())
            throw new MoveNotPossibleException('A new node cannot be moved.');

        if (array_search($this->position, array('child', 'left', 'right', 'root')) === FALSE)
            throw new MoveNotPossibleException("Position should be one of ['child', 'left', 'right'] but is {$this->position}.");

        if (!$this->promotingToRoot()) {
            if (!$this->target) {
                if ($this->position === 'left' || $this->position === 'right')
                    throw new MoveNotPossibleException("Could not resolve target node. This node cannot move any further to the {$this->position}.");
                else
                    throw new MoveNotPossibleException('Could not resolve target node.');
            }

            if ($this->node->equals($this->target))
                throw new MoveNotPossibleException('A node cannot be moved to itself.');

            if ($this->target->insideSubtree($this->node))
                throw new MoveNotPossibleException('A node cannot be moved to a descendant of itself (inside moved tree).');

            if (!$this->node->inSameScope($this->target))
                throw new MoveNotPossibleException('A node cannot be moved to a different scope.');
        }
    }

    /**
     * Computes the boundary.
     *
     * @return int
     */
    protected function bound1()
    {
        if (!is_null($this->_bound1)) return $this->_bound1;

        switch ($this->position) {
            case 'child':
                $this->_bound1 = $this->target->readAttribute($this->target->getRightColumnName());
                break;

            case 'left':
                $this->_bound1 = $this->target->readAttribute($this->target->getLeftColumnName());
                break;

            case 'right':
                $this->_bound1 = $this->target->readAttribute($this->target->getRightColumnName()) + 1;
                break;

            case 'root':
                $this->_bound1 = $this->node->maximum(['column' => $this->node->getRightColumnName()]) + 1;
                break;
        }

        $this->_bound1 = (($this->_bound1 > $this->node->getRight()) ? $this->_bound1 - 1 : $this->_bound1);
        return $this->_bound1;
    }

    /**
     * Computes the other boundary.
     * TODO: Maybe find a better name for this... ¿?
     *
     * @return int
     */
    protected function bound2()
    {
        if (!is_null($this->_bound2)) return $this->_bound2;

        $this->_bound2 = (($this->bound1() > $this->node->getRight()) ? $this->node->getRight() + 1 : $this->node->getLeft() - 1);
        return $this->_bound2;
    }

    /**
     * Computes the boundaries array.
     *
     * @return array
     */
    protected function boundaries()
    {
        if (!is_null($this->_boundaries)) return $this->_boundaries;

        // we have defined the boundaries of two non-overlapping intervals,
        // so sorting puts both the intervals and their boundaries in order
        $this->_boundaries = array(
            $this->node->readAttribute($this->node->getLeftColumnName()),
            $this->node->readAttribute($this->node->getRightColumnName()),
            $this->bound1(),
            $this->bound2()
        );
        sort($this->_boundaries);

        return $this->_boundaries;
    }

    /**
     * Computes the new parent id for the node being moved.
     *
     * @return int
     */
    protected function parentId()
    {
        switch ($this->position) {
            case 'root':
                return 0;

            case 'child':
                return $this->target->readAttribute($this->target->getKeyName());

            default:
                return $this->target->getParentId();
        }
    }

    /**
     * Check wether there should be changes in the downward tree structure.
     *
     * @return boolean
     */
    protected function hasChange()
    {
        return !($this->bound1() == $this->node->getRight() || $this->bound1() == $this->node->getLeft());
    }

    /**
     * Check if we are promoting the provided instance to a root node.
     *
     * @return boolean
     */
    protected function promotingToRoot()
    {
        return ($this->position == 'root');
    }

    /**
     * Applies a lock to the rows between the supplied index boundaries.
     *
     * @param   int $lft
     * @param   int $rgt
     * @return  void
     */
    protected function applyLockBetween($lft, $rgt)
    {
        $this->node->query()
            ->where($this->node->getLeftColumnName() . ' >= ' . $lft)
            ->andWhere($this->node->getRightColumnName() . ' <= ' . $rgt)
            ->columns($this->node->getKeyName())
            ->forUpdate()
            ->execute();
    }
}
