<?php namespace Phasty\Common\Service\Behaviors;
/**
 * Tree behavior class
 *
 *
 * PHP 5
 *
 * Licensed under The MIT License
 * Redistributions of files must retain the above copyright notice
 *
 * @category   Behavior
 * @package    Model.Behavior.Tree
 * @author     Benny Leonard Enrico Panggabean <bendo01@gmail.com>
 * @license    http://www.opensource.org/licenses/mit-license.php MIT License
 */

use Phalcon\Mvc\Model\Behavior;
use Phalcon\Mvc\Model\BehaviorInterface;

/**
 * Tree Behavior
 * Enables a model object to act as a node-based tree. Using Modified Preorder Tree Traversal
 * @see     http://en.wikipedia.org/wiki/Tree_traversal
 * @author  Benny L.E.P <bendo01@gmail.com>
 * @package Model.Behavior.Tree
 * @see     http://mikehillyer.com/articles/managing-hierarchical-data-in-mysql/
 * @see     http://www.phpclasses.org/package/5169-PHP-Manipulate-a-tree-node-structure-stored-in-MySQL.html
 * @see     http://www.sitepoint.com/hierarchical-data-database/
 * @see     http://www.phpclasses.org/browse/file/26163.html
 * @license http://opensource.org/licenses/gpl-3.0.html GNU General Public License version 3 (GPLv3)
 * @license http://www.opensource.org/licenses/mit-license.php MIT License
 */
class TreeBehavior extends Behavior implements BehaviorInterface
{
    /**
     * Temporary node holder
     * @var mixed
     */
    protected $node;

    /**
     * Temporary $properties holder
     * @var array
     */
    protected $properties;

    /*
    public function __construct($options = array())
    {
        
    }
    */
    public function notify($eventType, $model)
    {
        switch ($eventType) {
            case 'afterDelete':
                $this->removeNodeWithoutChildren($model);
                break;
            case 'beforeCreate':
                $this->setNodeProperties($model);
                break;
            default:
        }
    }

    public function missingMethod($model, $method, $arguments = array())
    {
        if ($method == 'startUp') {
            return $this->startUp($model);
        }

        if ($method == 'getChildren') {
            return $this->getChildren($model, $arguments[0], $arguments[1], $arguments[2]);
        }

        if ($method == 'getChildrenCount') {
            return $this->getChildrenCount($model, $arguments[0]);
        }

        if ($method == 'getDescendantsCount') {
            return $this->getDescendantsCount($model, $arguments[0]);
        }

        if ($method == 'getParent') {
            return $this->getParent($model, $arguments[0]);
        }

        if ($method == 'getSelectables') {
            if (empty($arguments[0])) {
                $arguments[0] = '-';
            }
            return $this->getSelectables($model, $arguments[0]);
        }

        if ($method == 'getAllRoot') {
            return $this->getAllRoot($model, $arguments[0]);
        }

        if ($method == 'getTree') {
            return $this->getTree($model);
        }

        if ($method == 'rebuildTree') {
            return $this->rebuildTree($model, 1, null);
        }

        if ($method == 'getSubtree') {
            return $this->getSubtree($model, $arguments[0]);
        }
        if ($method == 'moveLeft') {
            return $this->moveLeft($model, $arguments[0]);
        }
        if ($method == 'moveRight') {
            return $this->moveRight($model, $arguments[0]);
        }
    }

    /**
     *
     * @param string $stringSetterGetter
     * @param string $key
     * @return string
     */
    public function changeToFunctionString($stringSetterGetter = 'get', $key = 'id')
    {
        return $stringSetterGetter . ucfirst($key);
    }

    /**
     * initialiase variable node from table data
     * @param \Phalcon\Mvc\Model
     * @return boolean
     */
    public function startUp($model)
    {
        $results = null;
        if (!isset($this->properties)) {
            $this->properties = $model->columnMap();
        }

        if (!isset($this->node)) {
            $results = $model->find(
                array(
                    'order' => 'lft ASC'
                )
            );
            $this->node = array();
        }

        if (!empty($results)) {
            foreach ($results as $result) {
                $this->node[$result->id] = $result;
            }
        }
        return true;
    }

    /**
     * comparing two node based on left properties
     * @param object $a
     * @param object $b
     * @return object
     */
    public static function cmpLeft($a, $b)
    {
        return $a->lft > $b->lft;
    }

    /**
     * sorting node based on left properties
     * @return boolean
     */
    public function reOrderLookUpArray()
    {
        usort($this->node, array($this, "cmpLeft"));
        return true;
    }

    /**
     * get node children, it can be returning array of node object, or array of array nodes
     * @param \Phalcon\Mvc\Model
     * @param int $id
     * @param boolean $childrenOnly
     * @param boolean $typeObject
     * @return array
     */
    public function getChildren($model, $id = null, $childrenOnly = false, $typeObject = true)
    {
        $this->startUp($model);
        $parentHasIn = false;
        $arrKeys = $model->columnMap();
        $returnArray = array();
        $children = array();
        // if parent node exists in the lookup array OR we're looking for the topmost nodes
        if (!empty($id) && !empty($this->node[$id])) {
            foreach ($this->node as $node) {
                // node's "left" is higher than parent node's "left"
                // node's "left" is smaller than parent node's "right"
                if (($node->parent_id == $id) && ($this->node[$id]->lft < $node->lft) && ($node->lft < $this->node[$id]->rgt)) {
                    if (!$parentHasIn && !$childrenOnly) {
                        $children[] = $this->node[$id];
                        $parentHasIn = true;
                    }
                    $children[] = $node;
                }
            }
        }
        $returnArray = $children;
        if (!$typeObject) {
            $returnArray = array();
            if (!empty($children)) {
                $i = 0;
                foreach ($children as $child) {
                    foreach ($arrKeys as $key => $value) {
                        $returnArray[$i][$value] = $child->{$this->changeToFunctionString('get', $value)}();
                    }
                    $i++;
                }
            }
        }
        return $returnArray;
    }

    /**
     * get children node based on parentId
     * @param object $model
     * @param int $parentId
     * @return object
     */
    public function getChildrenBasedOnParentId($model, $parentId = null)
    {
        $arrKeys = $this->columnMap();
        $returnArray = array();
        $children = array();
        $this->node = $model->find(
            array(
                'conditions' => 'parentId is null'
            )
        );

        if (!empty($parentId)) {
            $this->node = $model->find(
                array(
                    'conditions' => 'parentId = ' . $parentId
                )
            );
        }

        if (!empty($this->node)) {
            foreach ($this->node as $node) {
                // node's "left" is higher than parent node's "left"
                // node's "left" is smaller than parent node's "right"
                if (($node->parentId == $parentId)) {
                    $children[] = $node;
                }
            }
        }

        if (!empty($children)) {
            $i = 0;
            foreach ($children as $child) {
                foreach ($arrKeys as $key => $value) {
                    $returnArray[$i][$value] = $child->{$value};
                }
                $i++;
            }
        }
        return $returnArray;
    }

    /**
     * count children of node
     * @param \Phalcon\Mvc\Model
     * @param int $id
     * @return int
     */
    public function getChildrenCount($model, $id = null)
    {
        $result = 0;
        $this->startUp($model);
        if (!empty($this->node) && !empty($id)) {
            $result = 0;
            foreach ($this->node as $node) {
                if ($node->parent_id == $id) {
                    $result++;
                }
            }
        }
        return $result;
    }

    /**
     * count descendants of node
     * @param \Phalcon\Mvc\Model
     * @param int $id
     * @return int
     */
    public function getDescendantsCount($model, $id = null)
    {
        $result = 0;
        $this->startUp($model);
        if (!empty($this->node) && !empty($id) && !empty($this->node[$id])) {
            $result = ($this->node[$id]->rgt - $this->node[$id]->lft - 1) / 2;
        }
        return $result;
    }

    /**
     * get parent node
     * @param object $model
     * @param int $id
     * @return object
     */
    public function getParent($model, $id = null)
    {
        $node = null;
        if (!empty($id)) {
            $result = $model->findFirst($id);
            $node = $model->findFirst($result->parent_id);
        }
        return $node;
    }

    /**
     * get node path
     * @param \Phalcon\Mvc\Model
     * @param int $id
     * @return array object
     */
    public function getPath($model, $id = null)
    {
        $parents = array();
        $this->startUp($model);
        if (!empty($id) && !empty($this->node[$id])) {
            foreach ($this->node as $node) {
                if (($node->lft < $this->node[$id]->lft) && ($node->rgt > $this->node[$id]->rgt)) {
                    $parents[] = $node;
                }
            }
        }
        return $parents;
    }

    /**
     * generate separator for getSelectables function
     * @param string $name
     * @param int $count
     * @param string $separator
     * @return string
     */
    public function generateSeparator($name = null, $count = 0, $separator = '-')
    {
        $returnStr = $name;
        if (!empty($name) && $count > 0) {
            $tempStr = '';
            for ($i = 0; $i < $count; $i++) {
                $tempStr .= $separator;
            }
            $returnStr = $tempStr . $name;
        }
        return $returnStr;
    }

    /**
     * generate list for input form
     * @param object $model
     * @param string $separator
     * @return array
     */
    public function getSelectables($model, $separator = '-')
    {
        $this->startUp($model);
        $returnArray = array();
        foreach ($this->node as $node) {
            $returnArray[$node->id] = $this->generateSeparator($node->getName(), count($this->getPath($model, $node->id)), $separator);
        }
        return $returnArray;
    }

    /**
     * get all root type node in table data
     * @param object $model
     * @param boolean $typeObject
     * @return array object
     */
    public function getAllRoot($model, $typeObject = true)
    {
        $returnArray = null;
        $arrKeys = $model->columnMap();
        $rootArr = $model->find(
            array(
                'conditions' => 'parentId is null',
                'order' => 'lft ASC'
            )
        );

        $returnArray = $rootArr;

        if (!$typeObject) {
            $returnArray = null;
            if (!empty($rootArr)) {
                $i = 0;
                foreach ($rootArr as $root) {
                    foreach ($arrKeys as $key => $value) {
                        $returnArray[$i][$value] = $root->{$this->changeToFunctionString('get', $value)}();
                    }
                    $i++;
                }
            }
        }
        return $returnArray;
    }


    public function getLastRoot($model, $typeObject = true)
    {
        $node = null;
        $rootArr = $this->getAllRoot($model, $typeObject);
        if (!empty($rootArr[count($rootArr) - 1])) {
            $node = $rootArr[count($rootArr) - 1];
        }
        return $node;
    }

    /**
     * get subtree of node
     * @param object $model
     * @param array $childrenData
     * @return array
     */
    public function getSubtree($model, $childrenData = array())
    {
        if (!empty($childrenData)) {
            $i = 0;
            foreach ($childrenData as $childData) {
                if ($this->getChildrenCount($model, $childData['id']) > 0) {
                    $childrenData[$i]['children'] = $this->getChildren($model, $childData['id'], true, false);
                    $childrenData[$i]['children'] = $this->getSubtree($model, $childrenData[$i]['children']);
                }
                $i++;
            }
        }
        return $childrenData;
    }

    /**
     * get tree from table data
     * @param object $model
     * @return array
     */
    public function getTree($model)
    {
        $roots = null;
        if ($model->count() > 0) {
            $roots = $this->getAllRoot($model, false);
            if (!empty($roots)) {
                $i = 0;
                foreach ($roots as $root) {
                    if ($this->getChildrenCount($model, $root['id']) > 0) {
                        $roots[$i]['children'] = $this->getChildren($model, $root['id'], true, false);
                        $roots[$i]['children'] = $this->getSubtree($model, $roots[$i]['children']);
                    }
                    $i++;
                }
            }
        }
        return $roots;
    }

    /**
     * get children of node for rebuildTree function
     * @param object $model
     * @param int $parentId
     * @return object
     */
    public function getChildForRebuildTree($model, $parentId = null)
    {
        $returnArray = null;
        $arrKeys = $model->columnMap();
        $returnArray = array();
        $children = null;

        if (!empty($parentId)) {
            $children = $model->find(
                array(
                    'conditions' => 'parentId = ' . $parentId,
                    'order' => 'id'
                )
            );

            $returnArray = array();
            if (!empty($children)) {
                $i = 0;
                foreach ($children as $child) {
                    foreach ($arrKeys as $key => $value) {
                        $returnArray[$i][$value] = $child->{$this->changeToFunctionString('get', $value)}();
                    }
                    $i++;
                }
            }
        } else {
            $children = $model->find(
                array(
                    'conditions' => 'parentId is null',
                    'order' => 'id'
                )
            );

            $returnArray = array();
            if (!empty($children)) {
                $i = 0;
                foreach ($children as $child) {
                    foreach ($arrKeys as $key => $value) {
                        $returnArray[$i][$value] = $child->{$this->changeToFunctionString('get', $value)}();
                    }
                    $i++;
                }
            }
        }
        return $returnArray;
    }

    /**
     * check if node has children for function rebuildTree
     * @param \Phalcon\Mvc\Model
     * @param int $parentId
     * @return boolean
     */
    public function hasChildrenForRebuildTree($model, $parentId = null)
    {
        $returnBoolean = false;
        $count = count($this->getChildForRebuildTree($model, $parentId));
        if ($count > 0) {
            $returnBoolean = true;
        }
        return $returnBoolean;
    }

    /**
     * Rebuild Tree based on parentId
     * @param object $model
     * @param int $counter
     * @param int $parentId
     * @return int
     */
    public function rebuildTree($model, $counter = 1, $parentId = null)
    {
        $limit = 999;
        $children = $this->getChildForRebuildTree($model, $parentId);
        $hasChildren = (bool)$children;

        if ($parentId !== null) {
            if ($hasChildren) {
                $model->getDi()->get('db')->query('UPDATE ' . $model->getSchema() . '.' . $model->getSource() . ' SET lft = ' . $counter . ' WHERE id = ' . $parentId);
                $counter++;
            } else {
                $model->getDi()->get('db')->query('UPDATE ' . $model->getSchema() . '.' . $model->getSource() . ' SET lft = ' . $counter . ', rgt = ' . ($counter + 1) . ' WHERE id = ' . $parentId);
                $counter += 2;
            }
        }

        while ($children) {
            foreach ($children as $row) {
                $counter = $this->rebuildTree($model, $counter, $row['id']);
            }
            if (count($children) !== $limit) {
                break;
            }
            $children = $this->getChildForRebuildTree($model, $parentId);
        }

        if ($parentId !== null && $hasChildren) {
            $model->getDi()->get('db')->query('UPDATE ' . $model->getSchema() . '.' . $model->getSource() . ' SET rgt = ' . $counter . ' WHERE id = ' . $parentId);
            $counter++;
        }
        return $counter;
    }

    /**
     * set lft value and rgt value of node before admiting to table
     * @param object $model Model of the new node
     * @return boolean true
     */
    public function setNodeProperties($model)
    {
        $lft = 1;
        $rgt = 2;
        $rootType = true;

        // check if node if root type or not
        if (!empty($model->parent_id)) {
            $rootType = false;
        }

        $model->lft = $lft;
        $model->rgt = $rgt;

        //check if not table empty
        if ($model->count() > 0) {
            if ($rootType) {
                $lastNode = $model->findFirst(
                    array(
                        'conditions' => 'parentId is null',
                        'order' => 'rgt DESC',
                        'limit' => 1
                    )
                );
                if (!empty($lastNode)) {
                    $model->setLft($lastNode->rgt + 1);
                    $model->setRght($lastNode->rgt + 2);
                }
            } else {
                $parentHasChildren = false;
                if ($this->getChildrenCount($model, $model->parent_id) > 0) {
                    $parentHasChildren = true;
                }

                if ($parentHasChildren) {

                    $lastNode = $model->findFirst(
                        array(
                            'conditions' => 'parentId = ' . $model->parent_id,
                            'order' => 'lft ASC',
                            'limit' => 1
                        )
                    );
                    $model->setLft($lastNode->rgt + 1);
                    $model->setRght($lastNode->rgt + 2);
                    $model->getDi()->get('db')->query('UPDATE ' . $model->getSchema() . '.' . $model->getSource() . ' SET rgt = rgt+2 WHERE rgt > ' . $lastNode->rgt);
                    $model->getDi()->get('db')->query('UPDATE ' . $model->getSchema() . '.' . $model->getSource() . ' SET lft = lft+2 WHERE lft > ' . $lastNode->rgt);

                } else {

                    $lastNode = $model->findFirst(
                        array(
                            'conditions' => 'id = ' . $model->parent_id,
                            'order' => 'rgt DESC',
                            'limit' => 1
                        )
                    );
                    //set value node
                    $model->setLft($lastNode->lft + 1);
                    $model->setRght($lastNode->lft + 2);
                    $model->getDi()->get('db')->query('UPDATE ' . $model->getSchema() . '.' . $model->getSource() . ' SET rgt = rgt+2 WHERE rgt > ' . $lastNode->lft);
                    $model->getDi()->get('db')->query('UPDATE ' . $model->getSchema() . '.' . $model->getSource() . ' SET lft = lft+2 WHERE lft > ' . $lastNode->lft);
                }
            }
        }
        return true;
    }

    /**
     * Deletes a node an all it's children
     * @param \Phalcon\Mvc\Model
     * @return boolean true
     */
    public function removeNodeWithChildren($model)
    {
        if ($model->count() > 0) {
            //$node = $model->findFirst($model->id);
            $round = round((($model->rgt - $model->lft) + 1));
            $model->getDi()->get('db')->query('DELETE FROM ' . $model->getSchema() . '.' . $model->getSource() . ' WHERE lft BETWEEN ' . $model->lft . ' AND ' . $model->rgt);
            $model->getDi()->get('db')->query('UPDATE ' . $model->getSchema() . '.' . $model->getSource() . ' SET lft = lft - ' . $round . ' WHERE lft >' . $model->rgt);
            $model->getDi()->get('db')->query('UPDATE ' . $model->getSchema() . '.' . $model->getSource() . ' SET rgt = rgt - ' . $round . ' WHERE rgt >' . $model->rgt);
        }

        return true;
    }

    /**
     * Deletes a node and increases the level of all children by one
     * @param \Phalcon\Mvc\Model
     * @return boolean true
     */
    public function removeNodeWithoutChildren($model)
    {
        if ($model->count() > 0) {
            if (empty($model->parent_id)) {
                $model->getDi()->get('db')->query('UPDATE ' . $model->getSchema() . '.' . $model->getSource() . ' SET parent_id = NULL WHERE parent_id = ' . $model->id);
            } else {
                $model->getDi()->get('db')->query('UPDATE ' . $model->getSchema() . '.' . $model->getSource() . ' SET parent_id = ' . $model->parent_id . ' WHERE parent_id = ' . $model->id);
            }
            $model->getDi()->get('db')->query('UPDATE ' . $model->getSchema() . '.' . $model->getSource() . ' SET lft = lft - 1, rgt = rgt - 1 WHERE lft BETWEEN ' . $model->lft . ' AND ' . $model->rgt);
            $model->getDi()->get('db')->query('UPDATE ' . $model->getSchema() . '.' . $model->getSource() . ' SET lft = lft - 2 WHERE lft > ' . $model->lft);
            $model->getDi()->get('db')->query('UPDATE ' . $model->getSchema() . '.' . $model->getSource() . ' SET rgt = rgt - 2 WHERE rgt > ' . $model->rgt);
        }
        return true;
    }

    /**
     * Gets the id of a node depending on it's lft value
     * @param \Phalcon\Mvc\Model
     * @param integer $lft lft value of the node
     * @return integer id of the node
     */
    public function getIdLeft($model, $lft)
    {
        $returnedId = null;
        $node = $model->findFirst(
            array(
                'conditions' => 'lft = ' . $lft
            )
        );

        if (!empty($node)) {
            $returnedId = $node->id;
        }
        return $returnedId;
    }

    /**
     * Gets the id of a node depending on it's rgt value
     * @param \Phalcon\Mvc\Model
     * @param integer $rgt rgt value of the node
     * @return integer id of the node
     */
    public function getIdRight($model, $rgt)
    {
        $returnedId = null;
        $node = $model->findFirst(
            array(
                'conditions' => 'rgt = ' . $rgt
            )
        );
        if (!empty($node)) {
            $returnedId = $node->id;
        }
        return $returnedId;
    }

    /**
     * Moves a node one position to the left staying in the same level
     * @param \Phalcon\Mvc\Model
     * @param int id of the node to move
     * @return boolean true
     */
    public function moveLeft($model, $id)
    {
        $node = $model->findFirst($id);
        if (!empty($node->id)) {
            $brotherId = $this->getIdRight($model, $node->lft - 1);
            if (!empty($brotherId)) {
                $idsNotToMove = array();
                $strSQL = '';
                $brotherNode = $model->findFirst($brotherId);
                $nodeSize = $node->rgt - $node->lft + 1;
                $brotherSize = $brotherNode->rgt - $brotherNode->lft + 1;
                $resultNotToMove = $model->find(
                    array(
                        'conditions' => 'lft BETWEEN ' . $node->lft . ' AND ' . $node->rgt
                    )
                );
                foreach ($resultNotToMove as $result) {
                    $idsNotToMove[] = $result->id;
                }
                $model->getDi()->get('db')->query('UPDATE ' . $model->getSchema() . '.' . $model->getSource() . ' SET lft = lft - ' . $brotherSize . ', rgt = rgt - ' . $brotherSize . ' WHERE lft BETWEEN ' . $node->lft . ' AND ' . $node->rgt);
                $strSQL = 'UPDATE ' . $model->getSchema() . '.' . $model->getSource() . ' SET lft = lft + ' . $nodeSize . ', rgt = rgt + ' . $nodeSize . ' WHERE lft BETWEEN ' . $brotherNode->lft . ' AND ' . $brotherNode->rgt;
                foreach ($idsNotToMove as $idNotToMove) {
                    $strSQL .= ' AND id !=' . $idNotToMove;
                }
                $model->getDi()->get('db')->query($strSQL);
            }
        }
        return true;
    }

    /**
     * Moves a node one position to the right staying in the same level
     * @param \Phalcon\Mvc\Model
     * @param int id of the node to move
     * @return boolean true
     */
    public function moveRight($model, $id)
    {
        $node = $model->findFirst($id);
        if (!empty($node->id)) {
            $brotherId = $this->getIdLeft($model, $node->rgt + 1);

            if (!empty($brotherId)) {
                $idsNotToMove = array();
                $strSQL = '';
                $brotherNode = $model->findFirst($brotherId);
                $nodeSize = $node->rgt - $node->lft + 1;
                $brotherSize = $brotherNode->rgt - $brotherNode->lft + 1;
                $resultNotToMove = $model->find(
                    array(
                        'conditions' => 'lft BETWEEN ' . $node->lft . ' AND ' . $node->rgt
                    )
                );
                foreach ($resultNotToMove as $result) {
                    $idsNotToMove[] = $result->id;
                }
                $model->getDi()->get('db')->query('UPDATE ' . $model->getSchema() . '.' . $model->getSource() . ' SET lft = lft + ' . $brotherSize . ', rgt = rgt + ' . $brotherSize . ' WHERE lft BETWEEN ' . $node->lft . ' AND ' . $node->rgt);
                $strSQL = 'UPDATE ' . $model->getSchema() . '.' . $model->getSource() . ' SET lft = lft - ' . $nodeSize . ', rgt = rgt - ' . $nodeSize . ' WHERE lft BETWEEN ' . $brotherNode->lft . ' AND ' . $brotherNode->rgt;
                foreach ($idsNotToMove as $idNotToMove) {
                    $strSQL .= ' AND id !=' . $idNotToMove;
                }
                $model->getDi()->get('db')->query($strSQL);
            }
        }
        return true;
    }
}

?>
