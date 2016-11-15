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
     * @param mixed $message  Toss\Type
     */
    public function __construct($type = 'info', $message = null)
    {
        $this->type = $type;

        if (!empty($message)) {
            $this->add($message);
        }
    }

    /**
     * Add data
     *
     * @param mixed $item  Toss\Type
     * @return object  Current object
     */
    public function add(Type $message)
    {
        $this->container[] = $message;
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
     * Get all messages
     *
     * @return array
     */
    public function all()
    {
        return $this->container;
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
        $typeName = ucfirst($this->type);
        $type = __NAMESPACE__.'\\Type\\'.$typeName;
        if (!$value instanceof $type) {
            throw new \InvalidArgumentException(sprintf('Type is must be "%s".', $typeName));
        }
        
        if (null === $offset) {
            $offset = count($this->container);
        } elseif (!ctype_digit((string) $offset)) {
            throw new \InvalidArgumentException('Array key must be integer.');
        }
        
        $this->container[(int) $offset] = $value;
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
