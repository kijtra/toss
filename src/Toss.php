<?php
/**
 * Kijtra/Toss
 *
 * Licensed under The MIT License
 * @license http://www.opensource.org/licenses/mit-license.php MIT License
 */
namespace Kijtra;

use \Kijtra\Toss\Group;
use \Kijtra\Toss\Type;

class Toss implements \ArrayAccess, \Countable, \IteratorAggregate
{
    /**
     * Default message type
     *
     * @var string
     */
    protected $defaultType = 'info';

    /**
     * Available message types
     *
     * @var array
     */
    protected $availableTypes = array(
        'error',
        'warning',
        'notice',
        'info',
        'success',
        'invalid',
        'valid',
    );

    /**
     * Message container
     *
     * @var array
     */
    protected $messages = array();

    /**
     * Custom added Toss\Type class map
     *
     * @var array
     */
    protected $classMaps = array();

    /**
     * Latest added type
     *
     * @var string
     */
    protected $latestType;

    /**
     * Latest added Message
     *
     * @var object
     */
    protected $latestMessage;

    /**
     * Latest added type
     *
     * @param mixed $message  Something message data or Toss\Type or Exception object
     * @param string $type  Message type name
     * @param mixed $data  Extend data
     * @param boolean $toGlobal  Sync to global instance
     */
    public function __construct($message = null, $type = null, $data = null, $toGlobal = false)
    {
        if (null !== $message) {
            $this->add($message, $type, $data, $toGlobal);
        }
    }

    /**
     * Get Global(Singleton) instance
     *
     * @param mixed $text  Something message data or Toss\Type or Exception object
     * @param string $type  Message type name
     */
    public static function getGlobal($data = null, $type = null)
    {
        static $global;
        if (null === $global) {
            $global = new static();
        }
        if (null !== $data) {
            $global->add($data, $type);
        }
        return $global;
    }

    /**
     * Set default message type
     *
     * @param string $type  Message type name
     * @return object  Current object
     */
    public function setDefaultType($type)
    {
        if (!$this->isAvailableType($type)) {
            throw new \InvalidArgumentException(sprintf('Message Type "%s" is not available.', $type));
        }

        $this->defaultType = strtolower($type);

        return $this;
    }

    /**
     * Get default message type
     *
     * @return string  Message type name
     */
    public function getDefaultType()
    {
        return $this->defaultType;
    }

    /**
     * Get available message types
     *
     * @return array  Message types
     */
    public function getAvailableTypes()
    {
        return $this->availableTypes;
    }

    /**
     * Check available message type
     *
     * @param string $type Message type name
     */
    public function isAvailableType($name)
    {
        if (empty($name)) {
            return false;
        }
        return in_array(strtolower($name), $this->availableTypes);
    }

    /**
     * Add custom type
     *
     * @param object $type Toss\Type object
     * @return object  Current object
     */
    public function addType($type)
    {
        $withAdd = false;

        if (is_string($type)) {
            if (!class_exists($type)) {
                throw new \InvalidArgumentException(sprintf('Class "%s" is not exists.', $type));
            } elseif(Type::class !== get_parent_class($type)) {
                throw new \InvalidArgumentException('Prease extend Toss\\Type class.');
            }
            $class = $type;
        } else {
            if ($type instanceof Type) {
                $class = get_class($type);
                $withAdd = true;
            } else {
                throw new \LogicException('Prease extend Toss\\Type class.');
            }
        }
        
        $type = strtolower(substr(strrchr('\\'.$class, '\\'), 1));
        $this->availableTypes[] = $type;
        $this->availableTypes = array_unique($this->availableTypes);
        $this->classMaps[$type] = $class;

        if ($withAdd) {
            $this->add($type);
        }

        return $this;
    }

    /**
     * Add new message
     *
     * @param mixed $data  Something message data or Toss\Type or Exception object
     * @param string $type  Message type name
     * @param mixed $data  Extend data
     * @param boolean $toGlobal  Sync to global instance
     * @return object  Current object
     */
    public function add($text = null, $type = null, $data = null, $toGlobal = false)
    {
        $message = null;

        if (empty($text)) {
            throw new \InvalidArgumentException('Message data is empty.');
        } elseif ($text instanceof Type) {
            $message = $text;
            $type = $text->type();
            if (!$this->isAvailableType($type)) {
                $this->addType($text);
            }
        } elseif ($text instanceof \Exception) {
            $type = 'error';
            $message = new Type\Error($text);
        } else {
            if (empty($type)) {
                $type = $this->defaultType;
            } elseif (!$this->isAvailableType($type)) {
                throw new \InvalidArgumentException(sprintf('Message Type "%s" is not available.', $type));
            } else {
                $type = strtolower($type);
            }

            if (!empty($this->classMaps[$type])) {
                $class = $this->classMaps[$type];
            } else {
                $class = __CLASS__.'\\Type\\'.ucfirst($type);
            }
            
            $message = new $class($text);
        }

        if (!empty($data)) {
            $message->setData($data);
        }

        if (!empty($toGlobal)) {
            $message->toGlobal();
        }

        if (empty($this->messages[$type])) {
            $this->messages[$type] = new Group($type);
        }

        $this->messages[$type]->add($message);
        $this->latestType = $message->type();
        $this->latestMessage = $message;

        return $message;
    }

    /**
     * Get latest message
     *
     * @return object Toss\Type object
     */
    public function getMessage()
    {
        return $this->latestMessage;
    }

    /**
     * Get latest messages of type
     *
     * @return array Messages
     */
    public function getMessages($type = null)
    {
        if (null === $type) {
            if (!empty($this->latestType)) {
                $type = $this->latestType;
            } else {
                return;
            }
        } elseif (!$this->isAvailableType($type)) {
            throw new \InvalidArgumentException(sprintf('Message Type "%s" is not available.', $type));
        } else {
            $type = strtolower($type);
        }
        
        if (!empty($this->messages[$type])) {
            return $this->messages[$type];
        }
    }

    /**
     * Check message is not empty
     *
     * @param string $type Message type name
     * @return boolean
     */
    public function has($type)
    {
        return ($this->isAvailableType($type) && !empty($this->messages[$type]));
    }

    /**
     * Check message is nothing
     *
     * @param boolean $withGlobal Check with global instance
     * @return boolean
     */
    public function isNothing($withGlobal = false)
    {
        $nothing = true;

        if (!empty($this->messages)) {
            foreach ($this->messages as $val) {
                if (!empty($val)) {
                    $nothing = false;
                    break;
                }
            }
        }

        if ($nothing && !empty($withGlobal)) {
            if (!self::getGlobal()->isNothing()) {
                $nothing = false;
            }
        }

        return $nothing;
    }

    /**
     * isNothing() method alias
     *
     * @param boolean $withGlobal Check with global instance
     * @return boolean
     */
    public function isEmpty($withGlobal = false)
    {
        return $this->isNothing($withGlobal);
    }

    /**
     * Clear all or typed message
     *
     * @param string $type Message type name
     * @return object  Current object
     */
    public function clear($type = null)
    {
        if (null !== $type) {
            if ($this->isAvailableType($type)) {
                unset($this->messages[$type]);
            }
        } else {
            $this->messages = array();
        }

        return $this;
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
        if (0 === strncmp($lower, 'has', 3)) {
            if ($type = substr($lower, 3)) {
                return $this->has($type);
            }
        } elseif ($this->isAvailableType($lower)) {
            if (!empty($args[0])) {
                return $this->add($args[0], $lower);
            } else {
                return $this->getMessages($lower);
            }
        }
        
        throw new \BadMethodCallException(sprintf('Method "%s" is not exists', $name));
    }


    public static function __callStatic($name, $args)
    {
        $global = self::getGlobal();
        return call_user_func_array(array($global, $name), $args);
    }
    
    public function __get($name)
    {
        $lower = strtolower($name);

        // Check "hasXxxx"
        if (0 === strncmp($lower, 'has', 3)) {
            if ($type = substr($lower, 3)) {
                return $this->has($type);
            }
        }
        // Check "isXxxx"
        elseif (0 === strncmp($lower, 'is', 2)) {
            if ($type = substr($lower, 2)) {
                return $this->has($type);
            }
        } elseif ($this->isAvailableType($lower)) {
            return $this->offsetGet($lower);
        }
    }

    public function offsetExists($type)
    {
        return (!empty($this->messages[strtolower($type)]));
    }

    public function offsetGet($type)
    {
        $type = strtolower($type);
        if (!empty($this->messages[$type])) {
            return $this->messages[$type];
        }
    }

    public function offsetSet($key, $value)
    {
        return false;
    }

    public function offsetUnset($type)
    {
        $this->clear($type);
    }

    /**
     * Countable
     */
    public function count() 
    { 
        return count($this->messages); 
    }

    /**
     * IteratorAggregate
     */
    public function getIterator() {
        return new \ArrayIterator($this->messages);
    }
}
