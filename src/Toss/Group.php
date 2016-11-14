<?php
namespace Kijtra\Toss;

use \Kijtra\Toss\Type;

class Group implements \ArrayAccess, \Countable, \IteratorAggregate
{
    /**
     * Data container
     *
     * @var array
     */
    private $container = array();

    /**
     * Current type name
     *
     * @var string
     */
    private $type;

    /**
     * Add data(s)
     *
     * @param string $type  Message type name
     * @param mixed $item  Toss\Type or Array
     */
    public function __construct($type = 'info', $item = null)
    {
        $this->type = $type;

        if (!empty($item)) {
            if (is_array($item)) {
                foreach ($item as $val) {
                    $this->add($val);
                }
            } else {
                $this->add($item);
            }
        }
    }

    /**
     * Add data
     *
     * @param mixed $item  Toss\Type
     * @return object  Current object
     */
    public function add(Type $item)
    {
        $this->container[] = $item;
        return $this;
    }

    /**
     * Check empty
     *
     * @return boolean
     */
    public function isEmpty()
    {
        return empty($this->container);
    }

    /**
     * Check current type
     *
     * @return boolean
     */
    public function is($type)
    {
        return (is_string($type) && strtolower($type) === $this->type);
    }

    /**
     * Get first item
     *
     * @return mixed  Toss\Type or null
     */
    public function first()
    {
        if (!$this->isEmpty()) {
            $container = $this->container;
            return reset($container);
        }
    }

    /**
     * Get last item
     *
     * @return mixed  Toss\Type or null
     */
    public function last()
    {
        if (!$this->isEmpty()) {
            $container = $this->container;
            return end($container);
        }
    }

    /**
     * Get current pointer
     *
     * @return mixed  Toss\Type or null
     */
    public function current()
    {
        if (!$this->isEmpty()) {
            return current($this->container);
        }
    }

    /**
     * Get last pointer
     *
     * @return mixed  Toss\Type or null
     */
    public function end()
    {
        if (!$this->isEmpty()) {
            return end($this->container);
        }
    }

    /**
     * Convert to array
     *
     * @return array
     */
    public function toArray()
    {
        if ($this->isEmpty()) {
            return array();
        }

        $array = array();
        foreach ($this->container as $key => $val) {
            $array[$key] = $val->toArray();
        }
        return $array;
    }

    /**
     * Convert to JSON
     *
     * @return string  JSON string
     */
    public function toJson()
    {
        return $this->jsonSerialize();
    }

    /**
     * Method alias
     *
     * @param string $name Method name
     * @param string $args Method arguments
     * @return mixed
     */
    public function __call($name, $args)
    {
        $lower = strtolower($name);
        if (0 === strncmp($lower, 'is', 2)) {
            if ($type = substr($lower, 2)) {
                if ('empty' === $type) {
                    return $this->isEmpty();
                } else {
                    return $this->is($type);
                }
            }
        }
        
        throw new \BadMethodCallException(sprintf('Method "%s" is not exists', $name));
    }


    /**
     * ArrayAccess overrides
     */
    public function offsetSet($offset, $value) {
        if (ctype_digit((string) $offset)) {
            $this->container[(int) $offset] = $value;
        }
    }

    public function offsetExists($offset) {
        return array_key_exists($offset, $this->container);
    }

    public function offsetUnset($offset) {
        unset($this->container[$offset]);
    }

    public function offsetGet($offset) {
        return array_key_exists($offset, $this->container) ? $this->container[$offset] : null;
    }

    /**
     * Countable
     */
    public function count() 
    { 
        return count($this->container); 
    }

    /**
     * IteratorAggregate
     */
    public function getIterator() {
        return new \ArrayIterator($this->container);
    }
}
