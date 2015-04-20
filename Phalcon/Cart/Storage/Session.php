<?php

/**
 * This file is part of Moltin Cart, a PHP package to handle
 * your shopping basket.
 *
 * Copyright (c) 2013 Moltin Ltd.
 * http://github.com/moltin/cart
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @package moltin/cart
 * @author Chris Harvey <chris@molt.in>
 * @copyright 2013 Moltin Ltd.
 * @version dev
 * @link http://github.com/moltin/cart
 *
 */

namespace Phalcon\Cart\Storage;

use Phalcon\Cart\StorageInterface;
use Phalcon\Mvc\User\Plugin;
use Phalcon\Cart\Item;

class Session extends Plugin implements StorageInterface
{
    protected $identifier;
    protected static $cart = array();

    public function restore()
    {
        $carts = $this->session->get('cart');
        if ($carts) static::$cart = $carts;
    }

    /**
     * Add or update an item in the cart
     *
     * @param  Item $item The item to insert or update
     * @return void
     */
    public function insertUpdate(Item $item)
    {
        static::$cart[$this->id][$item->identifier] = $item;
        $this->saveCart();
    }

    /**
     * Retrieve the cart data
     * @param bool
     * @return array
     */
    public function &data($asArray = false)
    {
        $cart =& static::$cart[$this->id];
        if (!$asArray) return $cart;
        $data = $cart;
        foreach ($data as &$item) {
            $item = $item->toArray();
        }
        return $data;
    }

    /**
     * Check if the item exists in the cart
     *
     * @param  mixed $identifier
     * @return boolean
     */
    public function has($identifier)
    {
        foreach (static::$cart[$this->id] as $item) {
            if ($item->identifier == $identifier) return true;
        }
        return false;
    }

    /**
     * Get a single cart item by id
     *
     * @param  mixed $identifier The item id
     * @return Item  The item class
     */
    public function item($identifier)
    {
        foreach (static::$cart[$this->id] as $item) {
            if ($item->identifier == $identifier) return $item;
        }
        return false;
    }

    /**
     * Returns the first occurance of an item with a given id
     *
     * @param  string $id The item id
     * @return Item       Item object
     */
    public function find($id)
    {
        foreach (static::$cart[$this->id] as $item) {
            if ($item->id == $id) return $item;
        }
        return false;
    }

    /**
     * Remove an item from the cart
     *
     * @param  mixed $id
     * @return void
     */
    public function remove($id)
    {
        unset(static::$cart[$this->id][$id]);
        $this->saveCart();
    }

    /**
     * Destroy the cart
     *
     * @return void
     */
    public function destroy()
    {
        static::$cart[$this->id] = array();
        $this->saveCart();
    }

    /**
     * Set the cart identifier
     *
     * @param string $id
     */
    public function setIdentifier($id)
    {
        $this->id = $id;
        if (!array_key_exists($this->id, static::$cart)) {
            static::$cart[$this->id] = array();
        }
        $this->saveCart();
    }

    /**
     * Return the current cart identifier
     *
     * @return void
     */
    public function getIdentifier()
    {
        return $this->identifier;
    }

    protected function saveCart()
    {
        $data = static::$cart;
        $this->session->set('cart', $data);
    }
}
