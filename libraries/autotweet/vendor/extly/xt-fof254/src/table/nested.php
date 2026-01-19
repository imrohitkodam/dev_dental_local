<?php

/*
 * @package     XT Transitional Package from FrameworkOnFramework
 *
 * @author      Extly, CB. <team@extly.com>
 * @copyright   Copyright (c)2012-2024 Extly, CB. All rights reserved.
 *              Based on Akeeba's FrameworkOnFramework
 * @license     https://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
 *
 * @see         https://www.extly.com
 */

// Protect from unauthorized access
defined('XTF0F_INCLUDED') || exit;

/**
 * A class to manage tables holding nested sets (hierarchical data)
 *
 * @property int    $lft   Left value (for nested set implementation)
 * @property int    $rgt   Right value (for nested set implementation)
 * @property string $hash  Slug hash (optional; for faster searching)
 * @property string $slug  Node's slug (optional)
 * @property string $title Title of the node (optional)
 */
class XTF0FTableNested extends XTF0FTable
{
    public $slug;

    /**
     * @var string
     */
    public $hash;

    public $lft;

    public $rgt;

    /** @var int The level (depth) of this node in the tree */
    protected $treeDepth = null;

    /** @var XTF0FTableNested The root node in the tree */
    protected $treeRoot = null;

    /** @var XTF0FTableNested The parent node of ourselves */
    protected $treeParent = null;

    /** @var bool Should I perform a nested get (used to query ascendants/descendants) */
    protected $treeNestedGet = false;

    /** @var array A collection of custom, additional where clauses to apply during buildQuery */
    protected $whereClauses = [];

    /**
     * Public constructor. Overrides the parent constructor, making sure there are lft/rgt columns which make it
     * compatible with nested sets.
     *
     * @param string              $table  name of the database table to model
     * @param string              $key    name of the primary key field in the table
     * @param XTF0FDatabaseDriver &$db    Database driver
     * @param array               $config The configuration parameters array
     *
     * @throws \RuntimeException When lft/rgt columns are not found
     */
    public function __construct($table, $key, &$db, $config = [])
    {
        parent::__construct($table, $key, $db, $config);

        if (!$this->hasField('lft') || !$this->hasField('rgt')) {
            throw new \RuntimeException('Table '.$this->getTableName().' is not compatible with XTF0FTableNested: it does not have lft/rgt columns');
        }
    }

    /**
     * Overrides the automated table checks to handle the 'hash' column for faster searching
     *
     * @return bool
     */
    public function check()
    {
        // Create a slug if there is a title and an empty slug
        if ($this->hasField('title') && $this->hasField('slug') && empty($this->slug)) {
            $this->slug = XTF0FStringUtils::toSlug($this->title);
        }

        // Create the SHA-1 hash of the slug for faster searching (make sure the hash column is CHAR(64) to take
        // advantage of MySQL's optimised searching for fixed size CHAR columns)
        if ($this->hasField('hash') && $this->hasField('slug')) {
            $this->hash = sha1($this->slug);
        }

        // Reset cached values
        $this->resetTreeCache();

        return parent::check();
    }

    /**
     * Delete a node, either the currently loaded one or the one specified in $id. If an $id is specified that node
     * is loaded before trying to delete it. In the end the data model is reset. If the node has any children nodes
     * they will be removed before the node itself is deleted.
     *
     * @param int $oid The primary key value of the item to delete
     *
     * @return bool True on success
     *
     * @throws UnexpectedValueException
     */
    public function delete($oid = null)
    {
        // Load the specified record (if necessary)
        if (!empty($oid)) {
            $this->load($oid);
        }

        $k = $this->_tbl_key;
        $pk = $oid ?: $this->$k;

        // If no primary key is given, return false.
        if (!$pk) {
            throw new UnexpectedValueException('Null primary key not allowed.');
        }

        // Execute the logic only if I have a primary key, otherwise I could have weird results
        // Perform the checks on the current node *BEFORE* starting to delete the children
        if (!$this->onBeforeDelete($oid)) {
            return false;
        }

        $result = true;

        // Recursively delete all children nodes as long as we are not a leaf node and $recursive is enabled
        if (!$this->isLeaf()) {
            // Get all sub-nodes
            $table = $this->getClone();
            $table->bind($this->getData());
            $subNodes = $table->getDescendants();

            // Delete all subnodes (goes through the model to trigger the observers)
            if (!empty($subNodes)) {
                /** @var XTF0FTableNested $item */
                foreach ($subNodes as $subNode) {
                    // We have to pass the id, so we are getting it again from the database.
                    // We have to do in this way, since a previous child could have changed our lft and rgt values
                    if (!$subNode->delete($subNode->$k)) {
                        // A subnode failed or prevents the delete, continue deleting other nodes,
                        // but preserve the current node (ie the parent)
                        $result = false;
                    }
                }

                // Load it again, since while deleting a children we could have updated ourselves, too
                $this->load($pk);
            }
        }

        if ($result) {
            // Delete the row by primary key.
            $query = $this->_db->getQuery(true);
            $query->delete();
            $query->from($this->_tbl);
            $query->where($this->_tbl_key.' = '.$this->_db->q($pk));

            $this->_db->setQuery($query)->execute();

            $result = $this->onAfterDelete($oid);
        }

        return $result;
    }

    /**
     * Not supported in nested sets
     *
     * @param string $where Ignored
     *
     * @return void
     *
     * @throws RuntimeException
     */
    public function reorder($where = '')
    {
        throw new RuntimeException('reorder() is not supported by XTF0FTableNested');
    }

    /**
     * Not supported in nested sets
     *
     * @param int    $delta Ignored
     * @param string $where Ignored
     *
     * @return void
     *
     * @throws RuntimeException
     */
    public function move($delta, $where = '')
    {
        throw new RuntimeException('move() is not supported by XTF0FTableNested');
    }

    /**
     * Create a new record with the provided data. It is inserted as the last child of the current node's parent
     *
     * @param array $data The data to use in the new record
     *
     * @return static The new node
     */
    public function create($data)
    {
        $clone = $this->getClone();
        $clone->reset();
        $clone->bind($data);

        if ($this->isRoot()) {
            return $clone->insertAsChildOf($this);
        } else {
            return $clone->insertAsChildOf($this->getParent());
        }
    }

    /**
     * Makes a copy of the record, inserting it as the last child of the given node's parent.
     *
     * @param int|array $cid The primary key value (or values) or the record(s) to copy.
     *                       If null, the current record will be copied
     *
     * @return self|XTF0FTableNested The last copied node
     */
    public function copy($cid = null)
    {
        // We have to cast the id as array, or the helper function will return an empty set
        if ($cid) {
            $cid = (array) $cid;
        }

        XTF0FUtilsArray::toInteger($cid);
        $k = $this->_tbl_key;

        if (count($cid) < 1) {
            if ($this->$k) {
                $cid = [$this->$k];
            } else {
                // Even if it's null, let's still create the record
                $this->create($this->getData());

                return $this;
            }
        }

        foreach ($cid as $item) {
            // Prevent load with id = 0

            if (!$item) {
                continue;
            }

            $this->load($item);

            $this->create($this->getData());
        }

        return $this;
    }

    /**
     * Method to reset class properties to the defaults set in the class
     * definition. It will ignore the primary key as well as any private class
     * properties.
     *
     * @return void
     */
    public function reset()
    {
        $this->resetTreeCache();

        parent::reset();
    }

    /**
     * Insert the current node as a tree root. It is a good idea to never use this method, instead providing a root node
     * in your schema installation and then sticking to only one root.
     *
     * @return self
     */
    public function insertAsRoot()
    {
        // You can't insert a node that is already saved i.e. the table has an id
        if ($this->getId()) {
            throw new RuntimeException(__METHOD__.' can be only used with new nodes');
        }

        // First we need to find the right value of the last parent, a.k.a. the max(rgt) of the table
        $xtf0FDatabaseDriver = $this->getDbo();

        // Get the lft/rgt names
        $fldRgt = $xtf0FDatabaseDriver->qn($this->getColumnAlias('rgt'));

        $xtf0FDatabaseQuery = $xtf0FDatabaseDriver->getQuery(true)
            ->select('MAX('.$fldRgt.')')
            ->from($xtf0FDatabaseDriver->qn($this->getTableName()));
        $maxRgt = $xtf0FDatabaseDriver->setQuery($xtf0FDatabaseQuery, 0, 1)->loadResult();

        if (empty($maxRgt)) {
            $maxRgt = 0;
        }

        $this->lft = ++$maxRgt;
        $this->rgt = ++$maxRgt;

        $this->store();

        return $this;
    }

    /**
     * Insert the current node as the first (leftmost) child of a parent node.
     *
     * WARNING: If it's an existing node it will be COPIED, not moved.
     *
     * @param XTF0FTableNested $parentNode The node which will become our parent
     *
     * @return $this for chaining
     *
     * @throws Exception
     * @throws RuntimeException
     */
    public function insertAsFirstChildOf(self &$parentNode)
    {
        if ($parentNode->lft >= $parentNode->rgt) {
            throw new RuntimeException('Invalid position values for the parent node');
        }

        // Get a reference to the database
        $xtf0FDatabaseDriver = $this->getDbo();

        // Get the field names
        $fldRgt = $xtf0FDatabaseDriver->qn($this->getColumnAlias('rgt'));
        $fldLft = $xtf0FDatabaseDriver->qn($this->getColumnAlias('lft'));

        // Nullify the PK, so a new record will be created
        $pk = $this->getKeyName();
        $this->$pk = null;

        // Get the value of the parent node's rgt
        $myLeft = $parentNode->lft;

        // Update my lft/rgt values
        $this->lft = $myLeft + 1;
        $this->rgt = $myLeft + 2;

        // Update parent node's right (we added two elements in there, remember?)
        $parentNode->rgt += 2;

        // Wrap everything in a transaction
        $xtf0FDatabaseDriver->transactionStart();

        try {
            // Make a hole (2 queries)
            $query = $xtf0FDatabaseDriver->getQuery(true)
                ->update($xtf0FDatabaseDriver->qn($this->getTableName()))
                ->set($fldLft.' = '.$fldLft.'+2')
                ->where($fldLft.' > '.$xtf0FDatabaseDriver->q($myLeft));
            $xtf0FDatabaseDriver->setQuery($query)->execute();

            $query = $xtf0FDatabaseDriver->getQuery(true)
                ->update($xtf0FDatabaseDriver->qn($this->getTableName()))
                ->set($fldRgt.' = '.$fldRgt.'+ 2')
                ->where($fldRgt.'>'.$xtf0FDatabaseDriver->q($myLeft));
            $xtf0FDatabaseDriver->setQuery($query)->execute();

            // Insert the new node
            $this->store();

            // Commit the transaction
            $xtf0FDatabaseDriver->transactionCommit();
        } catch (\Exception $exception) {
            // Roll back the transaction on error
            $xtf0FDatabaseDriver->transactionRollback();

            throw $exception;
        }

        return $this;
    }

    /**
     * Insert the current node as the last (rightmost) child of a parent node.
     *
     * WARNING: If it's an existing node it will be COPIED, not moved.
     *
     * @param XTF0FTableNested $parentNode The node which will become our parent
     *
     * @return $this for chaining
     *
     * @throws Exception
     * @throws RuntimeException
     */
    public function insertAsLastChildOf(self &$parentNode)
    {
        if ($parentNode->lft >= $parentNode->rgt) {
            throw new RuntimeException('Invalid position values for the parent node');
        }

        // Get a reference to the database
        $xtf0FDatabaseDriver = $this->getDbo();

        // Get the field names
        $fldRgt = $xtf0FDatabaseDriver->qn($this->getColumnAlias('rgt'));
        $fldLft = $xtf0FDatabaseDriver->qn($this->getColumnAlias('lft'));

        // Nullify the PK, so a new record will be created
        $pk = $this->getKeyName();
        $this->$pk = null;

        // Get the value of the parent node's lft
        $myRight = $parentNode->rgt;

        // Update my lft/rgt values
        $this->lft = $myRight;
        $this->rgt = $myRight + 1;

        // Update parent node's right (we added two elements in there, remember?)
        $parentNode->rgt += 2;

        // Wrap everything in a transaction
        $xtf0FDatabaseDriver->transactionStart();

        try {
            // Make a hole (2 queries)
            $query = $xtf0FDatabaseDriver->getQuery(true)
                ->update($xtf0FDatabaseDriver->qn($this->getTableName()))
                ->set($fldRgt.' = '.$fldRgt.'+2')
                ->where($fldRgt.'>='.$xtf0FDatabaseDriver->q($myRight));
            $xtf0FDatabaseDriver->setQuery($query)->execute();

            $query = $xtf0FDatabaseDriver->getQuery(true)
                ->update($xtf0FDatabaseDriver->qn($this->getTableName()))
                ->set($fldLft.' = '.$fldLft.'+2')
                ->where($fldLft.'>'.$xtf0FDatabaseDriver->q($myRight));
            $xtf0FDatabaseDriver->setQuery($query)->execute();

            // Insert the new node
            $this->store();

            // Commit the transaction
            $xtf0FDatabaseDriver->transactionCommit();
        } catch (\Exception $exception) {
            // Roll back the transaction on error
            $xtf0FDatabaseDriver->transactionRollback();

            throw $exception;
        }

        return $this;
    }

    /**
     * Alias for insertAsLastchildOf
     *
     * @codeCoverageIgnore
     *
     * @return $this for chaining
     *
     * @throws Exception
     */
    public function insertAsChildOf(self &$parentNode)
    {
        return $this->insertAsLastChildOf($parentNode);
    }

    /**
     * Insert the current node to the left of (before) a sibling node
     *
     * WARNING: If it's an existing node it will be COPIED, not moved.
     *
     * @param XTF0FTableNested $siblingNode We will be inserted before this node
     *
     * @return $this for chaining
     *
     * @throws Exception
     * @throws RuntimeException
     */
    public function insertLeftOf(self &$siblingNode)
    {
        if ($siblingNode->lft >= $siblingNode->rgt) {
            throw new RuntimeException('Invalid position values for the sibling node');
        }

        // Get a reference to the database
        $xtf0FDatabaseDriver = $this->getDbo();

        // Get the field names
        $fldRgt = $xtf0FDatabaseDriver->qn($this->getColumnAlias('rgt'));
        $fldLft = $xtf0FDatabaseDriver->qn($this->getColumnAlias('lft'));

        // Nullify the PK, so a new record will be created
        $pk = $this->getKeyName();
        $this->$pk = null;

        // Get the value of the parent node's rgt
        $myLeft = $siblingNode->lft;

        // Update my lft/rgt values
        $this->lft = $myLeft;
        $this->rgt = $myLeft + 1;

        // Update sibling's lft/rgt values
        $siblingNode->lft += 2;
        $siblingNode->rgt += 2;

        $xtf0FDatabaseDriver->transactionStart();

        try {
            $xtf0FDatabaseDriver->setQuery(
                $xtf0FDatabaseDriver->getQuery(true)
                    ->update($xtf0FDatabaseDriver->qn($this->getTableName()))
                    ->set($fldLft.' = '.$fldLft.'+2')
                    ->where($fldLft.' >= '.$xtf0FDatabaseDriver->q($myLeft))
            )->execute();

            $xtf0FDatabaseDriver->setQuery(
                $xtf0FDatabaseDriver->getQuery(true)
                    ->update($xtf0FDatabaseDriver->qn($this->getTableName()))
                    ->set($fldRgt.' = '.$fldRgt.'+2')
                    ->where($fldRgt.' > '.$xtf0FDatabaseDriver->q($myLeft))
            )->execute();

            $this->store();

            // Commit the transaction
            $xtf0FDatabaseDriver->transactionCommit();
        } catch (\Exception $exception) {
            $xtf0FDatabaseDriver->transactionRollback();

            throw $exception;
        }

        return $this;
    }

    /**
     * Insert the current node to the right of (after) a sibling node
     *
     * WARNING: If it's an existing node it will be COPIED, not moved.
     *
     * @param XTF0FTableNested $siblingNode We will be inserted after this node
     *
     * @return $this for chaining
     *
     * @throws Exception
     * @throws RuntimeException
     */
    public function insertRightOf(self &$siblingNode)
    {
        if ($siblingNode->lft >= $siblingNode->rgt) {
            throw new RuntimeException('Invalid position values for the sibling node');
        }

        // Get a reference to the database
        $xtf0FDatabaseDriver = $this->getDbo();

        // Get the field names
        $fldRgt = $xtf0FDatabaseDriver->qn($this->getColumnAlias('rgt'));
        $fldLft = $xtf0FDatabaseDriver->qn($this->getColumnAlias('lft'));

        // Nullify the PK, so a new record will be created
        $pk = $this->getKeyName();
        $this->$pk = null;

        // Get the value of the parent node's lft
        $myRight = $siblingNode->rgt;

        // Update my lft/rgt values
        $this->lft = $myRight + 1;
        $this->rgt = $myRight + 2;

        $xtf0FDatabaseDriver->transactionStart();

        try {
            $xtf0FDatabaseDriver->setQuery(
                $xtf0FDatabaseDriver->getQuery(true)
                    ->update($xtf0FDatabaseDriver->qn($this->getTableName()))
                    ->set($fldRgt.' = '.$fldRgt.'+2')
                    ->where($fldRgt.' > '.$xtf0FDatabaseDriver->q($myRight))
            )->execute();

            $xtf0FDatabaseDriver->setQuery(
                $xtf0FDatabaseDriver->getQuery(true)
                    ->update($xtf0FDatabaseDriver->qn($this->getTableName()))
                    ->set($fldLft.' = '.$fldLft.'+2')
                    ->where($fldLft.' > '.$xtf0FDatabaseDriver->q($myRight))
            )->execute();

            $this->store();

            // Commit the transaction
            $xtf0FDatabaseDriver->transactionCommit();
        } catch (\Exception $exception) {
            $xtf0FDatabaseDriver->transactionRollback();

            throw $exception;
        }

        return $this;
    }

    /**
     * Alias for insertRightOf
     *
     * @codeCoverageIgnore
     *
     * @return $this for chaining
     */
    public function insertAsSiblingOf(self &$siblingNode)
    {
        return $this->insertRightOf($siblingNode);
    }

    /**
     * Move the current node (and its subtree) one position to the left in the tree, i.e. before its left-hand sibling
     *
     * @return $this
     *
     * @throws RuntimeException
     */
    public function moveLeft()
    {
        // Sanity checks on current node position
        if ($this->lft >= $this->rgt) {
            throw new RuntimeException('Invalid position values for the current node');
        }

        // If it is a root node we will not move the node (roots don't participate in tree ordering)
        if ($this->isRoot()) {
            return $this;
        }

        // Are we already the leftmost node?
        $xtf0FTableNested = $this->getParent();

        if ($xtf0FTableNested->lft == ($this->lft - 1)) {
            return $this;
        }

        // Get the sibling to the left
        $xtf0FDatabaseDriver = $this->getDbo();
        $clone = $this->getClone();
        $clone->reset();

        $leftSibling = $clone->whereRaw($xtf0FDatabaseDriver->qn($this->getColumnAlias('rgt')).' = '.$xtf0FDatabaseDriver->q($this->lft - 1))
            ->get(0, 1)->current();

        // Move the node
        if ($leftSibling instanceof self) {
            return $this->moveToLeftOf($leftSibling);
        }

        return false;
    }

    /**
     * Move the current node (and its subtree) one position to the right in the tree, i.e. after its right-hand sibling
     *
     * @return $this
     *
     * @throws RuntimeException
     */
    public function moveRight()
    {
        // Sanity checks on current node position
        if ($this->lft >= $this->rgt) {
            throw new RuntimeException('Invalid position values for the current node');
        }

        // If it is a root node we will not move the node (roots don't participate in tree ordering)
        if ($this->isRoot()) {
            return $this;
        }

        // Are we already the rightmost node?
        $xtf0FTableNested = $this->getParent();

        if ($xtf0FTableNested->rgt == ($this->rgt + 1)) {
            return $this;
        }

        // Get the sibling to the right
        $xtf0FDatabaseDriver = $this->getDbo();

        $clone = $this->getClone();
        $clone->reset();

        $rightSibling = $clone->whereRaw($xtf0FDatabaseDriver->qn($this->getColumnAlias('lft')).' = '.$xtf0FDatabaseDriver->q($this->rgt + 1))
            ->get(0, 1)->current();

        // Move the node
        if ($rightSibling instanceof self) {
            return $this->moveToRightOf($rightSibling);
        }

        return false;
    }

    /**
     * Moves the current node (and its subtree) to the left of another node. The other node can be in a different
     * position in the tree or even under a different root.
     *
     * @return $this for chaining
     *
     * @throws Exception
     * @throws RuntimeException
     */
    public function moveToLeftOf(self $siblingNode)
    {
        // Sanity checks on current and sibling node position
        if ($this->lft >= $this->rgt) {
            throw new RuntimeException('Invalid position values for the current node');
        }

        if ($siblingNode->lft >= $siblingNode->rgt) {
            throw new RuntimeException('Invalid position values for the sibling node');
        }

        $xtf0FDatabaseDriver = $this->getDbo();
        $left = $xtf0FDatabaseDriver->qn($this->getColumnAlias('lft'));
        $right = $xtf0FDatabaseDriver->qn($this->getColumnAlias('rgt'));

        // Get node metrics
        $myLeft = $this->lft;
        $myRight = $this->rgt;
        $myWidth = $myRight - $myLeft + 1;

        // Get sibling metrics
        $sibLeft = $siblingNode->lft;

        // Start the transaction
        $xtf0FDatabaseDriver->transactionStart();

        try {
            // Temporary remove subtree being moved
            $query = $xtf0FDatabaseDriver->getQuery(true)
                ->update($xtf0FDatabaseDriver->qn($this->getTableName()))
                ->set($left . ' = '.$xtf0FDatabaseDriver->q(0).(' - ' . $left))
                ->set($right . ' = '.$xtf0FDatabaseDriver->q(0).(' - ' . $right))
                ->where($left.' >= '.$xtf0FDatabaseDriver->q($myLeft))
                ->where($right.' <= '.$xtf0FDatabaseDriver->q($myRight));
            $xtf0FDatabaseDriver->setQuery($query)->execute();

            // Close hole left behind
            $query = $xtf0FDatabaseDriver->getQuery(true)
                ->update($xtf0FDatabaseDriver->qn($this->getTableName()))
                ->set($left.' = '.$left.' - '.$xtf0FDatabaseDriver->q($myWidth))
                ->where($left.' > '.$xtf0FDatabaseDriver->q($myRight));
            $xtf0FDatabaseDriver->setQuery($query)->execute();

            $query = $xtf0FDatabaseDriver->getQuery(true)
                ->update($xtf0FDatabaseDriver->qn($this->getTableName()))
                ->set($right.' = '.$right.' - '.$xtf0FDatabaseDriver->q($myWidth))
                ->where($right.' > '.$xtf0FDatabaseDriver->q($myRight));
            $xtf0FDatabaseDriver->setQuery($query)->execute();

            // Make a hole for the new items
            $newSibLeft = ($sibLeft > $myRight) ? $sibLeft - $myWidth : $sibLeft;

            $query = $xtf0FDatabaseDriver->getQuery(true)
                ->update($xtf0FDatabaseDriver->qn($this->getTableName()))
                ->set($right.' = '.$right.' + '.$xtf0FDatabaseDriver->q($myWidth))
                ->where($right.' >= '.$xtf0FDatabaseDriver->q($newSibLeft));
            $xtf0FDatabaseDriver->setQuery($query)->execute();

            $query = $xtf0FDatabaseDriver->getQuery(true)
                ->update($xtf0FDatabaseDriver->qn($this->getTableName()))
                ->set($left.' = '.$left.' + '.$xtf0FDatabaseDriver->q($myWidth))
                ->where($left.' >= '.$xtf0FDatabaseDriver->q($newSibLeft));
            $xtf0FDatabaseDriver->setQuery($query)->execute();

            // Move node and subnodes
            $moveRight = $newSibLeft - $myLeft;

            $query = $xtf0FDatabaseDriver->getQuery(true)
                ->update($xtf0FDatabaseDriver->qn($this->getTableName()))
                ->set($left.' = '.$xtf0FDatabaseDriver->q(0).' - '.$left.' + '.$xtf0FDatabaseDriver->q($moveRight))
                ->set($right.' = '.$xtf0FDatabaseDriver->q(0).' - '.$right.' + '.$xtf0FDatabaseDriver->q($moveRight))
                ->where($left.' <= 0 - '.$xtf0FDatabaseDriver->q($myLeft))
                ->where($right.' >= 0 - '.$xtf0FDatabaseDriver->q($myRight));
            $xtf0FDatabaseDriver->setQuery($query)->execute();

            // Commit the transaction
            $xtf0FDatabaseDriver->transactionCommit();
        } catch (\Exception $exception) {
            $xtf0FDatabaseDriver->transactionRollback();

            throw $exception;
        }

        // Let's load the record again to fetch the new values for lft and rgt
        $this->load();

        return $this;
    }

    /**
     * Moves the current node (and its subtree) to the right of another node. The other node can be in a different
     * position in the tree or even under a different root.
     *
     * @return $this for chaining
     *
     * @throws Exception
     * @throws RuntimeException
     */
    public function moveToRightOf(self $siblingNode)
    {
        // Sanity checks on current and sibling node position
        if ($this->lft >= $this->rgt) {
            throw new RuntimeException('Invalid position values for the current node');
        }

        if ($siblingNode->lft >= $siblingNode->rgt) {
            throw new RuntimeException('Invalid position values for the sibling node');
        }

        $xtf0FDatabaseDriver = $this->getDbo();
        $left = $xtf0FDatabaseDriver->qn($this->getColumnAlias('lft'));
        $right = $xtf0FDatabaseDriver->qn($this->getColumnAlias('rgt'));

        // Get node metrics
        $myLeft = $this->lft;
        $myRight = $this->rgt;
        $myWidth = $myRight - $myLeft + 1;

        // Get parent metrics
        $sibRight = $siblingNode->rgt;

        // Start the transaction
        $xtf0FDatabaseDriver->transactionStart();

        try {
            // Temporary remove subtree being moved
            $query = $xtf0FDatabaseDriver->getQuery(true)
                ->update($xtf0FDatabaseDriver->qn($this->getTableName()))
                ->set($left . ' = '.$xtf0FDatabaseDriver->q(0).(' - ' . $left))
                ->set($right . ' = '.$xtf0FDatabaseDriver->q(0).(' - ' . $right))
                ->where($left.' >= '.$xtf0FDatabaseDriver->q($myLeft))
                ->where($right.' <= '.$xtf0FDatabaseDriver->q($myRight));
            $xtf0FDatabaseDriver->setQuery($query)->execute();

            // Close hole left behind
            $query = $xtf0FDatabaseDriver->getQuery(true)
                ->update($xtf0FDatabaseDriver->qn($this->getTableName()))
                ->set($left.' = '.$left.' - '.$xtf0FDatabaseDriver->q($myWidth))
                ->where($left.' > '.$xtf0FDatabaseDriver->q($myRight));
            $xtf0FDatabaseDriver->setQuery($query)->execute();

            $query = $xtf0FDatabaseDriver->getQuery(true)
                ->update($xtf0FDatabaseDriver->qn($this->getTableName()))
                ->set($right.' = '.$right.' - '.$xtf0FDatabaseDriver->q($myWidth))
                ->where($right.' > '.$xtf0FDatabaseDriver->q($myRight));
            $xtf0FDatabaseDriver->setQuery($query)->execute();

            // Make a hole for the new items
            $newSibRight = ($sibRight > $myRight) ? $sibRight - $myWidth : $sibRight;

            $query = $xtf0FDatabaseDriver->getQuery(true)
                ->update($xtf0FDatabaseDriver->qn($this->getTableName()))
                ->set($left.' = '.$left.' + '.$xtf0FDatabaseDriver->q($myWidth))
                ->where($left.' > '.$xtf0FDatabaseDriver->q($newSibRight));
            $xtf0FDatabaseDriver->setQuery($query)->execute();

            $query = $xtf0FDatabaseDriver->getQuery(true)
                ->update($xtf0FDatabaseDriver->qn($this->getTableName()))
                ->set($right.' = '.$right.' + '.$xtf0FDatabaseDriver->q($myWidth))
                ->where($right.' > '.$xtf0FDatabaseDriver->q($newSibRight));
            $xtf0FDatabaseDriver->setQuery($query)->execute();

            // Move node and subnodes
            $moveRight = ($sibRight > $myRight) ? $sibRight - $myRight : $sibRight - $myRight + $myWidth;

            $query = $xtf0FDatabaseDriver->getQuery(true)
                ->update($xtf0FDatabaseDriver->qn($this->getTableName()))
                ->set($left.' = '.$xtf0FDatabaseDriver->q(0).' - '.$left.' + '.$xtf0FDatabaseDriver->q($moveRight))
                ->set($right.' = '.$xtf0FDatabaseDriver->q(0).' - '.$right.' + '.$xtf0FDatabaseDriver->q($moveRight))
                ->where($left.' <= 0 - '.$xtf0FDatabaseDriver->q($myLeft))
                ->where($right.' >= 0 - '.$xtf0FDatabaseDriver->q($myRight));
            $xtf0FDatabaseDriver->setQuery($query)->execute();

            // Commit the transaction
            $xtf0FDatabaseDriver->transactionCommit();
        } catch (\Exception $exception) {
            $xtf0FDatabaseDriver->transactionRollback();

            throw $exception;
        }

        // Let's load the record again to fetch the new values for lft and rgt
        $this->load();

        return $this;
    }

    /**
     * Alias for moveToRightOf
     *
     * @codeCoverageIgnore
     *
     * @return $this for chaining
     */
    public function makeNextSiblingOf(self $siblingNode)
    {
        return $this->moveToRightOf($siblingNode);
    }

    /**
     * Alias for makeNextSiblingOf
     *
     * @codeCoverageIgnore
     *
     * @return $this for chaining
     */
    public function makeSiblingOf(self $siblingNode)
    {
        return $this->makeNextSiblingOf($siblingNode);
    }

    /**
     * Alias for moveToLeftOf
     *
     * @codeCoverageIgnore
     *
     * @return $this for chaining
     */
    public function makePreviousSiblingOf(self $siblingNode)
    {
        return $this->moveToLeftOf($siblingNode);
    }

    /**
     * Moves a node and its subtree as a the first (leftmost) child of $parentNode
     *
     * @return $this for chaining
     *
     * @throws Exception
     */
    public function makeFirstChildOf(self $parentNode)
    {
        // Sanity checks on current and sibling node position
        if ($this->lft >= $this->rgt) {
            throw new RuntimeException('Invalid position values for the current node');
        }

        if ($parentNode->lft >= $parentNode->rgt) {
            throw new RuntimeException('Invalid position values for the parent node');
        }

        $xtf0FDatabaseDriver = $this->getDbo();
        $left = $xtf0FDatabaseDriver->qn($this->getColumnAlias('lft'));
        $right = $xtf0FDatabaseDriver->qn($this->getColumnAlias('rgt'));

        // Get node metrics
        $myLeft = $this->lft;
        $myRight = $this->rgt;
        $myWidth = $myRight - $myLeft + 1;

        // Get parent metrics
        $parentLeft = $parentNode->lft;

        // Start the transaction
        $xtf0FDatabaseDriver->transactionStart();

        try {
            // Temporary remove subtree being moved
            $query = $xtf0FDatabaseDriver->getQuery(true)
                ->update($xtf0FDatabaseDriver->qn($this->getTableName()))
                ->set($left . ' = '.$xtf0FDatabaseDriver->q(0).(' - ' . $left))
                ->set($right . ' = '.$xtf0FDatabaseDriver->q(0).(' - ' . $right))
                ->where($left.' >= '.$xtf0FDatabaseDriver->q($myLeft))
                ->where($right.' <= '.$xtf0FDatabaseDriver->q($myRight));
            $xtf0FDatabaseDriver->setQuery($query)->execute();

            // Close hole left behind
            $query = $xtf0FDatabaseDriver->getQuery(true)
                ->update($xtf0FDatabaseDriver->qn($this->getTableName()))
                ->set($left.' = '.$left.' - '.$xtf0FDatabaseDriver->q($myWidth))
                ->where($left.' > '.$xtf0FDatabaseDriver->q($myRight));
            $xtf0FDatabaseDriver->setQuery($query)->execute();

            $query = $xtf0FDatabaseDriver->getQuery(true)
                ->update($xtf0FDatabaseDriver->qn($this->getTableName()))
                ->set($right.' = '.$right.' - '.$xtf0FDatabaseDriver->q($myWidth))
                ->where($right.' > '.$xtf0FDatabaseDriver->q($myRight));
            $xtf0FDatabaseDriver->setQuery($query)->execute();

            // Make a hole for the new items
            $newParentLeft = ($parentLeft > $myRight) ? $parentLeft - $myWidth : $parentLeft;

            $query = $xtf0FDatabaseDriver->getQuery(true)
                ->update($xtf0FDatabaseDriver->qn($this->getTableName()))
                ->set($right.' = '.$right.' + '.$xtf0FDatabaseDriver->q($myWidth))
                ->where($right.' >= '.$xtf0FDatabaseDriver->q($newParentLeft));
            $xtf0FDatabaseDriver->setQuery($query)->execute();

            $query = $xtf0FDatabaseDriver->getQuery(true)
                ->update($xtf0FDatabaseDriver->qn($this->getTableName()))
                ->set($left.' = '.$left.' + '.$xtf0FDatabaseDriver->q($myWidth))
                ->where($left.' > '.$xtf0FDatabaseDriver->q($newParentLeft));
            $xtf0FDatabaseDriver->setQuery($query)->execute();

            // Move node and subnodes
            $moveRight = $newParentLeft - $myLeft + 1;

            $query = $xtf0FDatabaseDriver->getQuery(true)
                ->update($xtf0FDatabaseDriver->qn($this->getTableName()))
                ->set($left.' = '.$xtf0FDatabaseDriver->q(0).' - '.$left.' + '.$xtf0FDatabaseDriver->q($moveRight))
                ->set($right.' = '.$xtf0FDatabaseDriver->q(0).' - '.$right.' + '.$xtf0FDatabaseDriver->q($moveRight))
                ->where($left.' <= 0 - '.$xtf0FDatabaseDriver->q($myLeft))
                ->where($right.' >= 0 - '.$xtf0FDatabaseDriver->q($myRight));
            $xtf0FDatabaseDriver->setQuery($query)->execute();

            // Commit the transaction
            $xtf0FDatabaseDriver->transactionCommit();
        } catch (\Exception $exception) {
            $xtf0FDatabaseDriver->transactionRollback();

            throw $exception;
        }

        // Let's load the record again to fetch the new values for lft and rgt
        $this->load();

        return $this;
    }

    /**
     * Moves a node and its subtree as a the last (rightmost) child of $parentNode
     *
     * @return $this for chaining
     *
     * @throws Exception
     * @throws RuntimeException
     */
    public function makeLastChildOf(self $parentNode)
    {
        // Sanity checks on current and sibling node position
        if ($this->lft >= $this->rgt) {
            throw new RuntimeException('Invalid position values for the current node');
        }

        if ($parentNode->lft >= $parentNode->rgt) {
            throw new RuntimeException('Invalid position values for the parent node');
        }

        $xtf0FDatabaseDriver = $this->getDbo();
        $left = $xtf0FDatabaseDriver->qn($this->getColumnAlias('lft'));
        $right = $xtf0FDatabaseDriver->qn($this->getColumnAlias('rgt'));

        // Get node metrics
        $myLeft = $this->lft;
        $myRight = $this->rgt;
        $myWidth = $myRight - $myLeft + 1;

        // Get parent metrics
        $parentRight = $parentNode->rgt;

        // Start the transaction
        $xtf0FDatabaseDriver->transactionStart();

        try {
            // Temporary remove subtree being moved
            $query = $xtf0FDatabaseDriver->getQuery(true)
                ->update($xtf0FDatabaseDriver->qn($this->getTableName()))
                ->set($left . ' = '.$xtf0FDatabaseDriver->q(0).(' - ' . $left))
                ->set($right . ' = '.$xtf0FDatabaseDriver->q(0).(' - ' . $right))
                ->where($left.' >= '.$xtf0FDatabaseDriver->q($myLeft))
                ->where($right.' <= '.$xtf0FDatabaseDriver->q($myRight));
            $xtf0FDatabaseDriver->setQuery($query)->execute();

            // Close hole left behind
            $query = $xtf0FDatabaseDriver->getQuery(true)
                ->update($xtf0FDatabaseDriver->qn($this->getTableName()))
                ->set($left.' = '.$left.' - '.$xtf0FDatabaseDriver->q($myWidth))
                ->where($left.' > '.$xtf0FDatabaseDriver->q($myRight));
            $xtf0FDatabaseDriver->setQuery($query)->execute();

            $query = $xtf0FDatabaseDriver->getQuery(true)
                ->update($xtf0FDatabaseDriver->qn($this->getTableName()))
                ->set($right.' = '.$right.' - '.$xtf0FDatabaseDriver->q($myWidth))
                ->where($right.' > '.$xtf0FDatabaseDriver->q($myRight));
            $xtf0FDatabaseDriver->setQuery($query)->execute();

            // Make a hole for the new items
            $newLeft = ($parentRight > $myRight) ? $parentRight - $myWidth : $parentRight;

            $query = $xtf0FDatabaseDriver->getQuery(true)
                ->update($xtf0FDatabaseDriver->qn($this->getTableName()))
                ->set($left.' = '.$left.' + '.$xtf0FDatabaseDriver->q($myWidth))
                ->where($left.' >= '.$xtf0FDatabaseDriver->q($newLeft));
            $xtf0FDatabaseDriver->setQuery($query)->execute();

            $query = $xtf0FDatabaseDriver->getQuery(true)
                ->update($xtf0FDatabaseDriver->qn($this->getTableName()))
                ->set($right.' = '.$right.' + '.$xtf0FDatabaseDriver->q($myWidth))
                ->where($right.' >= '.$xtf0FDatabaseDriver->q($newLeft));
            $xtf0FDatabaseDriver->setQuery($query)->execute();

            // Move node and subnodes
            $moveRight = ($parentRight > $myRight) ? $parentRight - $myRight - 1 : $parentRight - $myRight - 1 + $myWidth;

            $query = $xtf0FDatabaseDriver->getQuery(true)
                ->update($xtf0FDatabaseDriver->qn($this->getTableName()))
                ->set($left.' = '.$xtf0FDatabaseDriver->q(0).' - '.$left.' + '.$xtf0FDatabaseDriver->q($moveRight))
                ->set($right.' = '.$xtf0FDatabaseDriver->q(0).' - '.$right.' + '.$xtf0FDatabaseDriver->q($moveRight))
                ->where($left.' <= 0 - '.$xtf0FDatabaseDriver->q($myLeft))
                ->where($right.' >= 0 - '.$xtf0FDatabaseDriver->q($myRight));
            $xtf0FDatabaseDriver->setQuery($query)->execute();

            // Commit the transaction
            $xtf0FDatabaseDriver->transactionCommit();
        } catch (\Exception $exception) {
            $xtf0FDatabaseDriver->transactionRollback();

            throw $exception;
        }

        // Let's load the record again to fetch the new values for lft and rgt
        $this->load();

        return $this;
    }

    /**
     * Alias for makeLastChildOf
     *
     * @codeCoverageIgnore
     *
     * @return $this for chaining
     */
    public function makeChildOf(self $parentNode)
    {
        return $this->makeLastChildOf($parentNode);
    }

    /**
     * Makes the current node a root (and moving its entire subtree along the way). This is achieved by moving the node
     * to the right of its root node
     *
     * @return $this for chaining
     */
    public function makeRoot()
    {
        // Make sure we are not a root
        if ($this->isRoot()) {
            return $this;
        }

        // Get a reference to my root
        $xtf0FTableNested = $this->getRoot();

        // Double check I am not a root
        if ($this->equals($xtf0FTableNested)) {
            return $this;
        }

        // Move myself to the right of my root
        $this->moveToRightOf($xtf0FTableNested);
        $this->treeDepth = 0;

        return $this;
    }

    /**
     * Gets the level (depth) of this node in the tree. The result is cached in $this->treeDepth for faster retrieval.
     *
     * @return int|mixed
     *
     * @throws RuntimeException
     */
    public function getLevel()
    {
        // Sanity checks on current node position
        if ($this->lft >= $this->rgt) {
            throw new RuntimeException('Invalid position values for the current node');
        }

        if (null === $this->treeDepth) {
            $db = $this->getDbo();

            $fldLft = $db->qn($this->getColumnAlias('lft'));
            $fldRgt = $db->qn($this->getColumnAlias('rgt'));

            $query = $db->getQuery(true)
                ->select('(COUNT('.$db->qn('parent').'.'.$fldLft.') - 1) AS '.$db->qn('depth'))
                ->from($db->qn($this->getTableName()).' AS '.$db->qn('node'))
                ->join('CROSS', $db->qn($this->getTableName()).' AS '.$db->qn('parent'))
                ->where($db->qn('node').'.'.$fldLft.' >= '.$db->qn('parent').'.'.$fldLft)
                ->where($db->qn('node').'.'.$fldLft.' <= '.$db->qn('parent').'.'.$fldRgt)
                ->where($db->qn('node').'.'.$fldLft.' = '.$db->q($this->lft))
                ->group($db->qn('node').'.'.$fldLft)
                ->order($db->qn('node').'.'.$fldLft.' ASC');

            $this->treeDepth = $db->setQuery($query, 0, 1)->loadResult();
        }

        return $this->treeDepth;
    }

    /**
     * Returns the immediate parent of the current node
     *
     * @return XTF0FTableNested
     *
     * @throws RuntimeException
     */
    public function getParent()
    {
        // Sanity checks on current node position
        if ($this->lft >= $this->rgt) {
            throw new RuntimeException('Invalid position values for the current node');
        }

        if ($this->isRoot()) {
            return $this;
        }

        if (empty($this->treeParent) || !is_object($this->treeParent) || !($this->treeParent instanceof self)) {
            $db = $this->getDbo();

            $fldLft = $db->qn($this->getColumnAlias('lft'));
            $fldRgt = $db->qn($this->getColumnAlias('rgt'));

            $query = $db->getQuery(true)
                ->select($db->qn('parent').'.'.$fldLft)
                ->from($db->qn($this->getTableName()).' AS '.$db->qn('node'))
                ->join('CROSS', $db->qn($this->getTableName()).' AS '.$db->qn('parent'))
                ->where($db->qn('node').'.'.$fldLft.' > '.$db->qn('parent').'.'.$fldLft)
                ->where($db->qn('node').'.'.$fldLft.' <= '.$db->qn('parent').'.'.$fldRgt)
                ->where($db->qn('node').'.'.$fldLft.' = '.$db->q($this->lft))
                ->order($db->qn('parent').'.'.$fldLft.' DESC');
            $targetLft = $db->setQuery($query, 0, 1)->loadResult();

            $table = $this->getClone();
            $table->reset();
            $this->treeParent = $table
                ->whereRaw($fldLft.' = '.$db->q($targetLft))
                ->get()->current();
        }

        return $this->treeParent;
    }

    /**
     * Is this a top-level root node?
     *
     * @return bool
     */
    public function isRoot()
    {
        // If lft=1 it is necessarily a root node
        if (1 == $this->lft) {
            return true;
        }

        // Otherwise make sure its level is 0
        return 0 == $this->getLevel();
    }

    /**
     * Is this a leaf node (a node without children)?
     *
     * @return bool
     *
     * @throws RuntimeException
     */
    public function isLeaf()
    {
        // Sanity checks on current node position
        if ($this->lft >= $this->rgt) {
            throw new RuntimeException('Invalid position values for the current node');
        }

        return ($this->rgt - 1) == $this->lft;
    }

    /**
     * Is this a child node (not root)?
     *
     * @codeCoverageIgnore
     *
     * @return bool
     */
    public function isChild()
    {
        return !$this->isRoot();
    }

    /**
     * Returns true if we are a descendant of $otherNode
     *
     * @return bool
     *
     * @throws RuntimeException
     */
    public function isDescendantOf(self $otherNode)
    {
        // Sanity checks on current node position
        if ($this->lft >= $this->rgt) {
            throw new RuntimeException('Invalid position values for the current node');
        }

        if ($otherNode->lft >= $otherNode->rgt) {
            throw new RuntimeException('Invalid position values for the other node');
        }

        return ($otherNode->lft < $this->lft) && ($otherNode->rgt > $this->rgt);
    }

    /**
     * Returns true if $otherNode is ourselves or if we are a descendant of $otherNode
     *
     * @return bool
     *
     * @throws RuntimeException
     */
    public function isSelfOrDescendantOf(self $otherNode)
    {
        // Sanity checks on current node position
        if ($this->lft >= $this->rgt) {
            throw new RuntimeException('Invalid position values for the current node');
        }

        if ($otherNode->lft >= $otherNode->rgt) {
            throw new RuntimeException('Invalid position values for the other node');
        }

        return ($otherNode->lft <= $this->lft) && ($otherNode->rgt >= $this->rgt);
    }

    /**
     * Returns true if we are an ancestor of $otherNode
     *
     * @codeCoverageIgnore
     *
     * @return bool
     */
    public function isAncestorOf(self $otherNode)
    {
        return $otherNode->isDescendantOf($this);
    }

    /**
     * Returns true if $otherNode is ourselves or we are an ancestor of $otherNode
     *
     * @codeCoverageIgnore
     *
     * @return bool
     */
    public function isSelfOrAncestorOf(self $otherNode)
    {
        return $otherNode->isSelfOrDescendantOf($this);
    }

    /**
     * Is $node this very node?
     *
     * @return bool
     *
     * @throws RuntimeException
     */
    public function equals(self &$node)
    {
        // Sanity checks on current node position
        if ($this->lft >= $this->rgt) {
            throw new RuntimeException('Invalid position values for the current node');
        }

        if ($node->lft >= $node->rgt) {
            throw new RuntimeException('Invalid position values for the other node');
        }

        return
            ($this->getId() == $node->getId())
            && ($this->lft == $node->lft)
            && ($this->rgt == $node->rgt);
    }

    /**
     * Alias for isDescendantOf
     *
     * @codeCoverageIgnore
     *
     * @return bool
     */
    public function insideSubtree(self $otherNode)
    {
        return $this->isDescendantOf($otherNode);
    }

    /**
     * Returns true if both this node and $otherNode are root, leaf or child (same tree scope)
     *
     * @return bool
     */
    public function inSameScope(self $otherNode)
    {
        if ($this->isLeaf()) {
            return $otherNode->isLeaf();
        } elseif ($this->isRoot()) {
            return $otherNode->isRoot();
        } elseif ($this->isChild()) {
            return $otherNode->isChild();
        } else {
            return false;
        }
    }

    /**
     * get() will not return the selected node if it's part of the query results
     *
     * @param XTF0FTableNested $node The node to exclude from the results
     *
     * @return void
     */
    public function withoutNode(self $node)
    {
        $xtf0FDatabaseDriver = $this->getDbo();

        $fldLft = $xtf0FDatabaseDriver->qn($this->getColumnAlias('lft'));

        $this->whereRaw('NOT('.$xtf0FDatabaseDriver->qn('node').'.'.$fldLft.' = '.$xtf0FDatabaseDriver->q($node->lft).')');
    }

    /**
     * Returns the root node of the tree this node belongs to
     *
     * @return self
     *
     * @throws \RuntimeException
     */
    public function getRoot()
    {
        // Sanity checks on current node position
        if ($this->lft >= $this->rgt) {
            throw new RuntimeException('Invalid position values for the current node');
        }

        // If this is a root node return itself (there is no such thing as the root of a root node)
        if ($this->isRoot()) {
            return $this;
        }

        if (empty($this->treeRoot) || !is_object($this->treeRoot) || !($this->treeRoot instanceof self)) {
            $this->treeRoot = null;

            // First try to get the record with the minimum ID
            $db = $this->getDbo();

            $fldLft = $db->qn($this->getColumnAlias('lft'));
            $fldRgt = $db->qn($this->getColumnAlias('rgt'));

            $subQuery = $db->getQuery(true)
                ->select('MIN('.$fldLft.')')
                ->from($db->qn($this->getTableName()));

            try {
                $table = $this->getClone();
                $table->reset();
                $root = $table
                    ->whereRaw($fldLft.' = ('.(string) $subQuery.')')
                    ->get(0, 1)->current();

                if ($this->isDescendantOf($root)) {
                    $this->treeRoot = $root;
                }
            } catch (\RuntimeException $e) {
                // If there is no root found throw an exception. Basically: your table is FUBAR.
                throw new \RuntimeException('No root found for table '.$this->getTableName().', node lft='.$this->lft, $e->getCode(), $e);
            }

            // If the above method didn't work, get all roots and select the one with the appropriate lft/rgt values
            if (null === $this->treeRoot) {
                // Find the node with depth = 0, lft < our lft and rgt > our right. That's our root node.
                $query = $db->getQuery(true)
                    ->select([
                        $db->qn('node').'.'.$fldLft,
                        '(COUNT('.$db->qn('parent').'.'.$fldLft.') - 1) AS '.$db->qn('depth'),
                    ])
                    ->from($db->qn($this->getTableName()).' AS '.$db->qn('node'))
                    ->join('CROSS', $db->qn($this->getTableName()).' AS '.$db->qn('parent'))
                    ->where($db->qn('node').'.'.$fldLft.' >= '.$db->qn('parent').'.'.$fldLft)
                    ->where($db->qn('node').'.'.$fldLft.' <= '.$db->qn('parent').'.'.$fldRgt)
                    ->where($db->qn('node').'.'.$fldLft.' < '.$db->q($this->lft))
                    ->where($db->qn('node').'.'.$fldRgt.' > '.$db->q($this->rgt))
                    ->having($db->qn('depth').' = '.$db->q(0))
                    ->group($db->qn('node').'.'.$fldLft);

                // Get the lft value
                $targetLeft = $db->setQuery($query)->loadResult();

                if (empty($targetLeft)) {
                    // If there is no root found throw an exception. Basically: your table is FUBAR.
                    throw new \RuntimeException('No root found for table '.$this->getTableName().', node lft='.$this->lft);
                }

                try {
                    $table = $this->getClone();
                    $table->reset();
                    $this->treeRoot = $table
                        ->whereRaw($fldLft.' = '.$db->q($targetLeft))
                        ->get(0, 1)->current();
                } catch (\RuntimeException $e) {
                    // If there is no root found throw an exception. Basically: your table is FUBAR.
                    throw new \RuntimeException('No root found for table '.$this->getTableName().', node lft='.$this->lft, $e->getCode(), $e);
                }
            }
        }

        return $this->treeRoot;
    }

    /**
     * Get all ancestors to this node and the node itself. In other words it gets the full path to the node and the node
     * itself.
     *
     * @codeCoverageIgnore
     *
     * @return XTF0FDatabaseIterator
     */
    public function getAncestorsAndSelf()
    {
        $this->scopeAncestorsAndSelf();

        return $this->get();
    }

    /**
     * Get all ancestors to this node and the node itself, but not the root node. If you want to
     *
     * @codeCoverageIgnore
     *
     * @return XTF0FDatabaseIterator
     */
    public function getAncestorsAndSelfWithoutRoot()
    {
        $this->scopeAncestorsAndSelf();
        $this->scopeWithoutRoot();

        return $this->get();
    }

    /**
     * Get all ancestors to this node but not the node itself. In other words it gets the path to the node, without the
     * node itself.
     *
     * @codeCoverageIgnore
     *
     * @return XTF0FDatabaseIterator
     */
    public function getAncestors()
    {
        $this->scopeAncestorsAndSelf();
        $this->scopeWithoutSelf();

        return $this->get();
    }

    /**
     * Get all ancestors to this node but not the node itself and its root.
     *
     * @codeCoverageIgnore
     *
     * @return XTF0FDatabaseIterator
     */
    public function getAncestorsWithoutRoot()
    {
        $this->scopeAncestors();
        $this->scopeWithoutRoot();

        return $this->get();
    }

    /**
     * Get all sibling nodes, including ourselves
     *
     * @codeCoverageIgnore
     *
     * @return XTF0FDatabaseIterator
     */
    public function getSiblingsAndSelf()
    {
        $this->scopeSiblingsAndSelf();

        return $this->get();
    }

    /**
     * Get all sibling nodes, except ourselves
     *
     * @codeCoverageIgnore
     *
     * @return XTF0FDatabaseIterator
     */
    public function getSiblings()
    {
        $this->scopeSiblings();

        return $this->get();
    }

    /**
     * Get all leaf nodes in the tree. You may want to use the scopes to narrow down the search in a specific subtree or
     * path.
     *
     * @codeCoverageIgnore
     *
     * @return XTF0FDatabaseIterator
     */
    public function getLeaves()
    {
        $this->scopeLeaves();

        return $this->get();
    }

    /**
     * Get all descendant (children) nodes and ourselves.
     *
     * Note: all descendant nodes, even descendants of our immediate descendants, will be returned.
     *
     * @codeCoverageIgnore
     *
     * @return XTF0FDatabaseIterator
     */
    public function getDescendantsAndSelf()
    {
        $this->scopeDescendantsAndSelf();

        return $this->get();
    }

    /**
     * Get only our descendant (children) nodes, not ourselves.
     *
     * Note: all descendant nodes, even descendants of our immediate descendants, will be returned.
     *
     * @codeCoverageIgnore
     *
     * @return XTF0FDatabaseIterator
     */
    public function getDescendants()
    {
        $this->scopeDescendants();

        return $this->get();
    }

    /**
     * Get the immediate descendants (children). Unlike getDescendants it only goes one level deep into the tree
     * structure. Descendants of descendant nodes will not be returned.
     *
     * @codeCoverageIgnore
     *
     * @return XTF0FDatabaseIterator
     */
    public function getImmediateDescendants()
    {
        $this->scopeImmediateDescendants();

        return $this->get();
    }

    /**
     * Returns a hashed array where each element's key is the value of the $key column (default: the ID column of the
     * table) and its value is the value of the $column column (default: title). Each nesting level will have the value
     * of the $column column prefixed by a number of $separator strings, as many as its nesting level (depth).
     *
     * This is useful for creating HTML select elements showing the hierarchy in a human readable format.
     *
     * @param string $column
     * @param null   $key
     * @param string $seperator
     *
     * @return array
     */
    public function getNestedList($column = 'title', $key = null, $seperator = '  ')
    {
        $xtf0FDatabaseDriver = $this->getDbo();

        $fldLft = $xtf0FDatabaseDriver->qn($this->getColumnAlias('lft'));
        $fldRgt = $xtf0FDatabaseDriver->qn($this->getColumnAlias('rgt'));

        if (empty($key) || !$this->hasField($key)) {
            $key = $this->getKeyName();
        }

        if (empty($column)) {
            $column = 'title';
        }

        $fldKey = $xtf0FDatabaseDriver->qn($this->getColumnAlias($key));
        $fldColumn = $xtf0FDatabaseDriver->qn($this->getColumnAlias($column));

        $xtf0FDatabaseQuery = $xtf0FDatabaseDriver->getQuery(true)
            ->select([
                $xtf0FDatabaseDriver->qn('node').'.'.$fldKey,
                $xtf0FDatabaseDriver->qn('node').'.'.$fldColumn,
                '(COUNT('.$xtf0FDatabaseDriver->qn('parent').'.'.$fldKey.') - 1) AS '.$xtf0FDatabaseDriver->qn('depth'),
            ])
            ->from($xtf0FDatabaseDriver->qn($this->getTableName()).' AS '.$xtf0FDatabaseDriver->qn('node'))
            ->join('CROSS', $xtf0FDatabaseDriver->qn($this->getTableName()).' AS '.$xtf0FDatabaseDriver->qn('parent'))
            ->where($xtf0FDatabaseDriver->qn('node').'.'.$fldLft.' >= '.$xtf0FDatabaseDriver->qn('parent').'.'.$fldLft)
            ->where($xtf0FDatabaseDriver->qn('node').'.'.$fldLft.' <= '.$xtf0FDatabaseDriver->qn('parent').'.'.$fldRgt)
            ->group($xtf0FDatabaseDriver->qn('node').'.'.$fldLft)
            ->order($xtf0FDatabaseDriver->qn('node').'.'.$fldLft.' ASC');

        $tempResults = $xtf0FDatabaseDriver->setQuery($xtf0FDatabaseQuery)->loadAssocList();
        $ret = [];

        if (!empty($tempResults)) {
            foreach ($tempResults as $tempResult) {
                $ret[$tempResult[$key]] = str_repeat($seperator, $tempResult['depth']).$tempResult[$column];
            }
        }

        return $ret;
    }

    /**
     * Locate a node from a given path, e.g. "/some/other/leaf"
     *
     * Notes:
     * - This will only work when you have a "slug" and a "hash" field in your table.
     * - If the path starts with "/" we will use the root with lft=1. Otherwise the first component of the path is
     *   supposed to be the slug of the root node.
     * - If the root node is not found you'll get null as the return value
     * - You will also get null if any component of the path is not found
     *
     * @param string $path The path to locate
     *
     * @return XTF0FTableNested|null The found node or null if nothing is found
     */
    public function findByPath($path)
    {
        // @todo
    }

    public function isValid()
    {
        // @todo
    }

    public function rebuild()
    {
        // @todo
    }

    /**
     * Add custom, pre-compiled WHERE clauses for use in buildQuery. The raw WHERE clause you specify is added as is to
     * the query generated by buildQuery. You are responsible for quoting and escaping the field names and data found
     * inside the WHERE clause.
     *
     * @param string $rawWhereClause The raw WHERE clause to add
     *
     * @return $this For chaining
     */
    public function whereRaw($rawWhereClause)
    {
        $this->whereClauses[] = $rawWhereClause;

        return $this;
    }

    /**
     * Returns a database iterator to retrieve records. Use the scope methods and the whereRaw method to define what
     * exactly will be returned.
     *
     * @param int $limitstart How many items to skip from the start, only when $overrideLimits = true
     * @param int $limit      How many items to return, only when $overrideLimits = true
     *
     * @return XTF0FDatabaseIterator The data collection
     */
    public function get($limitstart = 0, $limit = 0)
    {
        $limitstart = max($limitstart, 0);
        $limit = max($limit, 0);

        $xtf0FDatabaseQuery = $this->buildQuery();
        $xtf0FDatabaseDriver = $this->getDbo();
        $xtf0FDatabaseDriver->setQuery($xtf0FDatabaseQuery, $limitstart, $limit);

        $cursor = $xtf0FDatabaseDriver->execute();

        $dataCollection = XTF0FDatabaseIterator::getIterator($xtf0FDatabaseDriver->name, $cursor, null, $this->config['_table_class']);

        return $dataCollection;
    }

    protected function onAfterDelete($oid)
    {
        $xtf0FDatabaseDriver = $this->getDbo();

        $myLeft = $this->lft;
        $myRight = $this->rgt;

        $fldLft = $xtf0FDatabaseDriver->qn($this->getColumnAlias('lft'));
        $fldRgt = $xtf0FDatabaseDriver->qn($this->getColumnAlias('rgt'));

        // Move all siblings to the left
        $width = $this->rgt - $this->lft + 1;

        // Wrap everything in a transaction
        $xtf0FDatabaseDriver->transactionStart();

        try {
            // Shrink lft values
            $query = $xtf0FDatabaseDriver->getQuery(true)
                        ->update($xtf0FDatabaseDriver->qn($this->getTableName()))
                        ->set($fldLft.' = '.$fldLft.' - '.$width)
                        ->where($fldLft.' > '.$xtf0FDatabaseDriver->q($myLeft));
            $xtf0FDatabaseDriver->setQuery($query)->execute();

            // Shrink rgt values
            $query = $xtf0FDatabaseDriver->getQuery(true)
                        ->update($xtf0FDatabaseDriver->qn($this->getTableName()))
                        ->set($fldRgt.' = '.$fldRgt.' - '.$width)
                        ->where($fldRgt.' > '.$xtf0FDatabaseDriver->q($myRight));
            $xtf0FDatabaseDriver->setQuery($query)->execute();

            // Commit the transaction
            $xtf0FDatabaseDriver->transactionCommit();
        } catch (\Exception $exception) {
            // Roll back the transaction on error
            $xtf0FDatabaseDriver->transactionRollback();

            throw $exception;
        }

        return parent::onAfterDelete($oid);
    }

    /**
     * get() will return all ancestor nodes and ourselves
     *
     * @return void
     */
    protected function scopeAncestorsAndSelf()
    {
        $this->treeNestedGet = true;

        $xtf0FDatabaseDriver = $this->getDbo();

        $fldLft = $xtf0FDatabaseDriver->qn($this->getColumnAlias('lft'));
        $fldRgt = $xtf0FDatabaseDriver->qn($this->getColumnAlias('rgt'));

        $this->whereRaw($xtf0FDatabaseDriver->qn('parent').'.'.$fldLft.' >= '.$xtf0FDatabaseDriver->qn('node').'.'.$fldLft);
        $this->whereRaw($xtf0FDatabaseDriver->qn('parent').'.'.$fldLft.' <= '.$xtf0FDatabaseDriver->qn('node').'.'.$fldRgt);
        $this->whereRaw($xtf0FDatabaseDriver->qn('parent').'.'.$fldLft.' = '.$xtf0FDatabaseDriver->q($this->lft));
    }

    /**
     * get() will return all ancestor nodes but not ourselves
     *
     * @return void
     */
    protected function scopeAncestors()
    {
        $this->treeNestedGet = true;

        $xtf0FDatabaseDriver = $this->getDbo();

        $fldLft = $xtf0FDatabaseDriver->qn($this->getColumnAlias('lft'));
        $fldRgt = $xtf0FDatabaseDriver->qn($this->getColumnAlias('rgt'));

        $this->whereRaw($xtf0FDatabaseDriver->qn('parent').'.'.$fldLft.' > '.$xtf0FDatabaseDriver->qn('node').'.'.$fldLft);
        $this->whereRaw($xtf0FDatabaseDriver->qn('parent').'.'.$fldLft.' < '.$xtf0FDatabaseDriver->qn('node').'.'.$fldRgt);
        $this->whereRaw($xtf0FDatabaseDriver->qn('parent').'.'.$fldLft.' = '.$xtf0FDatabaseDriver->q($this->lft));
    }

    /**
     * get() will return all sibling nodes and ourselves
     *
     * @return void
     */
    protected function scopeSiblingsAndSelf()
    {
        $xtf0FDatabaseDriver = $this->getDbo();

        $fldLft = $xtf0FDatabaseDriver->qn($this->getColumnAlias('lft'));
        $fldRgt = $xtf0FDatabaseDriver->qn($this->getColumnAlias('rgt'));

        $xtf0FTableNested = $this->getParent();
        $this->whereRaw($xtf0FDatabaseDriver->qn('node').'.'.$fldLft.' > '.$xtf0FDatabaseDriver->q($xtf0FTableNested->lft));
        $this->whereRaw($xtf0FDatabaseDriver->qn('node').'.'.$fldRgt.' < '.$xtf0FDatabaseDriver->q($xtf0FTableNested->rgt));
    }

    /**
     * get() will return all sibling nodes but not ourselves
     *
     * @codeCoverageIgnore
     *
     * @return void
     */
    protected function scopeSiblings()
    {
        $this->scopeSiblingsAndSelf();
        $this->scopeWithoutSelf();
    }

    /**
     * get() will return only leaf nodes
     *
     * @return void
     */
    protected function scopeLeaves()
    {
        $xtf0FDatabaseDriver = $this->getDbo();

        $fldLft = $xtf0FDatabaseDriver->qn($this->getColumnAlias('lft'));
        $fldRgt = $xtf0FDatabaseDriver->qn($this->getColumnAlias('rgt'));

        $this->whereRaw($xtf0FDatabaseDriver->qn('node').'.'.$fldLft.' = '.$xtf0FDatabaseDriver->qn('node').'.'.$fldRgt.' - '.$xtf0FDatabaseDriver->q(1));
    }

    /**
     * get() will return all descendants (even subtrees of subtrees!) and ourselves
     *
     * @return void
     */
    protected function scopeDescendantsAndSelf()
    {
        $this->treeNestedGet = true;

        $xtf0FDatabaseDriver = $this->getDbo();

        $fldLft = $xtf0FDatabaseDriver->qn($this->getColumnAlias('lft'));
        $fldRgt = $xtf0FDatabaseDriver->qn($this->getColumnAlias('rgt'));

        $this->whereRaw($xtf0FDatabaseDriver->qn('node').'.'.$fldLft.' >= '.$xtf0FDatabaseDriver->qn('parent').'.'.$fldLft);
        $this->whereRaw($xtf0FDatabaseDriver->qn('node').'.'.$fldLft.' <= '.$xtf0FDatabaseDriver->qn('parent').'.'.$fldRgt);
        $this->whereRaw($xtf0FDatabaseDriver->qn('parent').'.'.$fldLft.' = '.$xtf0FDatabaseDriver->q($this->lft));
    }

    /**
     * get() will return all descendants (even subtrees of subtrees!) but not ourselves
     *
     * @return void
     */
    protected function scopeDescendants()
    {
        $this->treeNestedGet = true;

        $xtf0FDatabaseDriver = $this->getDbo();

        $fldLft = $xtf0FDatabaseDriver->qn($this->getColumnAlias('lft'));
        $fldRgt = $xtf0FDatabaseDriver->qn($this->getColumnAlias('rgt'));

        $this->whereRaw($xtf0FDatabaseDriver->qn('node').'.'.$fldLft.' > '.$xtf0FDatabaseDriver->qn('parent').'.'.$fldLft);
        $this->whereRaw($xtf0FDatabaseDriver->qn('node').'.'.$fldLft.' < '.$xtf0FDatabaseDriver->qn('parent').'.'.$fldRgt);
        $this->whereRaw($xtf0FDatabaseDriver->qn('parent').'.'.$fldLft.' = '.$xtf0FDatabaseDriver->q($this->lft));
    }

    /**
     * get() will only return immediate descendants (first level children) of the current node
     *
     * @return void
     */
    protected function scopeImmediateDescendants()
    {
        // Sanity checks on current node position
        if ($this->lft >= $this->rgt) {
            throw new RuntimeException('Invalid position values for the current node');
        }

        $xtf0FDatabaseDriver = $this->getDbo();

        $fldLft = $xtf0FDatabaseDriver->qn($this->getColumnAlias('lft'));
        $fldRgt = $xtf0FDatabaseDriver->qn($this->getColumnAlias('rgt'));

        $xtf0FDatabaseQuery = $xtf0FDatabaseDriver->getQuery(true)
            ->select([
                $xtf0FDatabaseDriver->qn('node').'.'.$fldLft,
                '(COUNT(*) - 1) AS '.$xtf0FDatabaseDriver->qn('depth'),
            ])
            ->from($xtf0FDatabaseDriver->qn($this->getTableName()).' AS '.$xtf0FDatabaseDriver->qn('node'))
            ->from($xtf0FDatabaseDriver->qn($this->getTableName()).' AS '.$xtf0FDatabaseDriver->qn('parent'))
            ->where($xtf0FDatabaseDriver->qn('node').'.'.$fldLft.' >= '.$xtf0FDatabaseDriver->qn('parent').'.'.$fldLft)
            ->where($xtf0FDatabaseDriver->qn('node').'.'.$fldLft.' <= '.$xtf0FDatabaseDriver->qn('parent').'.'.$fldRgt)
            ->where($xtf0FDatabaseDriver->qn('node').'.'.$fldLft.' = '.$xtf0FDatabaseDriver->q($this->lft))
            ->group($xtf0FDatabaseDriver->qn('node').'.'.$fldLft)
            ->order($xtf0FDatabaseDriver->qn('node').'.'.$fldLft.' ASC');

        $query = $xtf0FDatabaseDriver->getQuery(true)
            ->select([
                $xtf0FDatabaseDriver->qn('node').'.'.$fldLft,
                '(COUNT('.$xtf0FDatabaseDriver->qn('parent').'.'.$fldLft.') - ('.
                $xtf0FDatabaseDriver->qn('sub_tree').'.'.$xtf0FDatabaseDriver->qn('depth').' + 1)) AS '.$xtf0FDatabaseDriver->qn('depth'),
            ])
            ->from($xtf0FDatabaseDriver->qn($this->getTableName()).' AS '.$xtf0FDatabaseDriver->qn('node'))
            ->join('CROSS', $xtf0FDatabaseDriver->qn($this->getTableName()).' AS '.$xtf0FDatabaseDriver->qn('parent'))
            ->join('CROSS', $xtf0FDatabaseDriver->qn($this->getTableName()).' AS '.$xtf0FDatabaseDriver->qn('sub_parent'))
            ->join('CROSS', '('.$xtf0FDatabaseQuery.') AS '.$xtf0FDatabaseDriver->qn('sub_tree'))
            ->where($xtf0FDatabaseDriver->qn('node').'.'.$fldLft.' >= '.$xtf0FDatabaseDriver->qn('parent').'.'.$fldLft)
            ->where($xtf0FDatabaseDriver->qn('node').'.'.$fldLft.' <= '.$xtf0FDatabaseDriver->qn('parent').'.'.$fldRgt)
            ->where($xtf0FDatabaseDriver->qn('node').'.'.$fldLft.' >= '.$xtf0FDatabaseDriver->qn('sub_parent').'.'.$fldLft)
            ->where($xtf0FDatabaseDriver->qn('node').'.'.$fldLft.' <= '.$xtf0FDatabaseDriver->qn('sub_parent').'.'.$fldRgt)
            ->where($xtf0FDatabaseDriver->qn('sub_parent').'.'.$fldLft.' = '.$xtf0FDatabaseDriver->qn('sub_tree').'.'.$fldLft)
            ->group($xtf0FDatabaseDriver->qn('node').'.'.$fldLft)
            ->having([
                $xtf0FDatabaseDriver->qn('depth').' > '.$xtf0FDatabaseDriver->q(0),
                $xtf0FDatabaseDriver->qn('depth').' <= '.$xtf0FDatabaseDriver->q(1),
            ])
            ->order($xtf0FDatabaseDriver->qn('node').'.'.$fldLft.' ASC');

        $leftValues = $xtf0FDatabaseDriver->setQuery($query)->loadColumn();

        if (empty($leftValues)) {
            $leftValues = [0];
        }

        array_walk($leftValues, function (&$item, $key) use (&$xtf0FDatabaseDriver): void {
            $item = $xtf0FDatabaseDriver->q($item);
        });

        $this->whereRaw($xtf0FDatabaseDriver->qn('node').'.'.$fldLft.' IN ('.implode(',', $leftValues).')');
    }

    /**
     * get() will not return ourselves if it's part of the query results
     *
     * @codeCoverageIgnore
     *
     * @return void
     */
    protected function scopeWithoutSelf()
    {
        $this->withoutNode($this);
    }

    /**
     * get() will not return our root if it's part of the query results
     *
     * @codeCoverageIgnore
     *
     * @return void
     */
    protected function scopeWithoutRoot()
    {
        $xtf0FTableNested = $this->getRoot();
        $this->withoutNode($xtf0FTableNested);
    }

    /**
     * Resets cached values used to speed up querying the tree
     *
     * @return static for chaining
     */
    protected function resetTreeCache()
    {
        $this->treeDepth = null;
        $this->treeRoot = null;
        $this->treeParent = null;
        $this->treeNestedGet = false;

        return $this;
    }

    /**
     * Builds the query for the get() method
     *
     * @return XTF0FDatabaseQuery
     */
    protected function buildQuery()
    {
        $xtf0FDatabaseDriver = $this->getDbo();

        $xtf0FDatabaseQuery = $xtf0FDatabaseDriver->getQuery(true)
            ->select($xtf0FDatabaseDriver->qn('node').'.*')
            ->from($xtf0FDatabaseDriver->qn($this->getTableName()).' AS '.$xtf0FDatabaseDriver->qn('node'));

        if ($this->treeNestedGet) {
            $xtf0FDatabaseQuery
                ->join('CROSS', $xtf0FDatabaseDriver->qn($this->getTableName()).' AS '.$xtf0FDatabaseDriver->qn('parent'));
        }

        // Apply custom WHERE clauses
        if (count($this->whereClauses)) {
            foreach ($this->whereClauses as $whereClause) {
                $xtf0FDatabaseQuery->where($whereClause);
            }
        }

        return $xtf0FDatabaseQuery;
    }
}
