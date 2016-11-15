<?php
/**
 * Kijtra/Toss
 *
 * Licensed under The MIT License
 * @license http://www.opensource.org/licenses/mit-license.php MIT License
 */
namespace Kijtra\Toss;

use \Kijtra\Toss;

/**
 * Kijtra\Toss\Type
 *
 * Licensed under The MIT License
 * @license http://www.opensource.org/licenses/mit-license.php MIT License
 */
class Type
{
    /**
     * Current type name
     *
     * @var string
     */
    private $type;

    /**
     * Something message data
     *
     * @var mixed
     */
    private $message;

    /**
     * Caller file path
     *
     * @var string
     */
    private $file;

    /**
     * Caller file line
     *
     * @var integer
     */
    private $line;

    /**
     * Extend data
     *
     * @var mixed
     */
    private $data;

    /**
     * Constructor
     *
     * @param mixed $message  Something message data or Toss\Type or Exception object
     * @param mixed $data  Extend data
     */
    final public function __construct($message = null, $data = null)
    {
        if (__CLASS__ === get_class($this)) {
            throw new \LogicException('Toss\\Type cannot call direct.');
        }

        // Class name equal type name
        $this->type = strtolower(substr(strrchr('\\'.get_class($this), '\\'), 1));

        if ($message instanceof \Exception) {
            $this->message = $message->getMessage();
            if (empty($this->message)) {
                throw new \InvalidArgumentException('Message data is empty.');
            }
            $this->file = $message->getFile();
            $this->line = $message->getLine();
        } elseif (is_string($message)) {
            $this->message = trim($message);

            $parentFile = self::getParentFilePath();
            $currentFile = self::getCurrentFilePath();
            foreach (debug_backtrace() as $val) {
                if (!empty($val['file'])
                    && $val['file'] !== $parentFile
                    && $val['file'] !== $currentFile
                ) {
                    $this->file = $val['file'];
                    $this->line = $val['line'];
                    break;
                }
            }
        } else {
            throw new \InvalidArgumentException('Invalid message argument.');
        }

        if (!empty($data)) {
            $this->withData($data);
        }
    }

    /**
     * Get global instance
     *
     * @return object  Toss object
     */
    final public function getGlobal()
    {
        return Toss::getGlobal();
    }

    /**
     * Get Toss file path
     *
     * @return string  File path
     */
    final protected static function getParentFilePath()
    {
        static $path;
        if (null === $path) {
            $reflector = new \ReflectionClass(Toss::class);
            $path = $reflector->getFileName();
        }
        return $path;
    }

    /**
     * Get current file path
     *
     * @return string  File path
     */
    final protected static function getCurrentFilePath()
    {
        static $path;
        if (null === $path) {
            $reflector = new \ReflectionClass(static::class);
            $path = $reflector->getFileName();
        }
        return $path;
    }

    /**
     * Get current type name
     *
     * @return string  Message type name
     */
    final public function getType()
    {
        return $this->type;
    }

    /**
     * getType() method alias
     *
     * @return string  Message type name
     */
    final public function type()
    {
        return $this->getType();
    }

    /**
     * Get message data
     *
     * @return mixed  Message data
     */
    final public function getMessage()
    {
        return $this->message;
    }

    /**
     * getMessage() method alias
     *
     * @return string  Message data
     */
    final public function message()
    {
        return $this->getMessage();
    }

    /**
     * Get caller file path
     *
     * @return string  File path
     */
    final public function getFile()
    {
        return $this->file;
    }

    /**
     * getFile() method alias
     *
     * @return string  File path
     */
    final public function file()
    {
        return $this->getFile();
    }

    /**
     * Get caller file line
     *
     * @return integer  Line number
     */
    final public function getLine()
    {
        return $this->line;
    }

    /**
     * getLine() method alias
     *
     * @return integer  Line number
     */
    final public function line()
    {
        return $this->getLine();
    }

    /**
     * Add extend data
     *
     * @param mixed $data  Extend data
     * @return object  Current object
     */
    final public function setData($data)
    {
        $this->data = $data;
        return $this;
    }

    /**
     * Get extend data
     *
     * @return mixed
     */
    final public function getData()
    {
        return $this->data;
    }

    /**
     * setData() / getData() method alias
     *
     * @return integer  Line number
     */
    final public function data($data = null)
    {
        if (0 === func_num_args()) {
            return $this->getData();
        } else {
            return $this->setData($data);
        }
    }

    /**
     * Check current type
     *
     * @return boolean  Type name is valid
     */
    final public function is($type)
    {
        return (strtolower($type) === $this->type);
    }

    /**
     * Check custom added type
     *
     * @return mixed  Type name is valid
     */
    final public function __call($name, $args)
    {
        $lower = strtolower($name);
        if (0 === strncmp($lower, 'is', 2)) {
            return $this->is(substr($lower, 2));
        }
        
        throw new \BadMethodCallException(sprintf('Method "%s" is not exists', $name));
    }

    /**
     * Sync to global instance
     */
    final public function toGlobal()
    {
        $global = $this->getGlobal();
        if (!$global->isAvailableType($this->type)) {
            $global->addType($this);
        }
        $global->add($this);
    }

    /**
     * Convert to array
     *
     * @return array 
     */
    final public function toArray()
    {
        return array(
            'type' => $this->getType(),
            'message' => $this->getMessage(),
            'file' => $this->getFile(),
            'line' => $this->getLine(),
            'data' => $this->getData(),
        );
    }

    /**
     * Print message data
     */
    final public function __toString()
    {
        if (is_scalar($this->message)) {
            return $this->message;
        } else {
            return '';
        }
    }

    /**
     * is() method alias or getting property alias
     *
     * @param string $key  Type name or property name
     * @return mixed  Result data
     */
    final public function __get($name)
    {
        $lower = strtolower($name);
        if (0 === strncmp($lower, 'is', 2)) {
            return $this->is(substr($lower, 2));
        } elseif (isset($this->{$name})) {
            return $this->{$name};
        }
        return false;
    }

    /**
     * Setting property is not support
     *
     * @param string $key  Property name
     * @return boolean  Always false
     */
    final public function __set($key, $value)
    {
        return false;
    }
}
