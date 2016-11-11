<?php
use Kijtra\Toss;
use Kijtra\Toss\Type;

class CustomExtended extends Type {}
class CustomNotExtended {}

class TossCustomTypeTest extends \PHPUnit_Framework_TestCase
{

    public function testAddExtended()
    {
        $instance = new Toss;
        $instance->addType('CustomExtended');
        $this->assertTrue($instance->isAvailableType('CustomExtended'));
    }

    public function testAddExtendedFromStringClassName()
    {
        $instance = new Toss;
        $instance->addType('\\CustomExtended');
        $this->assertTrue($instance->isAvailableType('CustomExtended'));
    }

    public function testAddFromInstance()
    {
        $text = 'Custom Type';
        $custom = new CustomExtended($text);
        $instance = new Toss;
        $instance->addType($custom);
        $instance->add($custom);
        $message = $instance->getMessage();
        $this->assertEquals($message->type, 'customextended');
    }

    public function testAddDirect()
    {
        $text = 'Custom Type';
        $custom = new CustomExtended($text);
        $instance = new Toss;
        $instance->add($custom);
        $message = $instance->getMessage();
        $this->assertEquals($message->message, $text);
    }

    public function testAddNotExists()
    {
        try {
            $instance = new Toss;
            $instance->addType('NotExistsType');
        } catch(\Exception $e) {
            $this->assertEquals('InvalidArgumentException', get_class($e));
            return;
        }
        $this->assertTrue(false);
    }

    public function testAddInvalidFromName()
    {
        try {
            $instance = new Toss;
            $instance->addType('CustomNotExtended');
        } catch(\Exception $e) {
            $this->assertEquals('InvalidArgumentException', get_class($e));
            return;
        }
        $this->assertTrue(false);
    }

    public function testAddInvalidFromInstance()
    {
        try {
            $custom = new CustomNotExtended('Custom Type');
            $instance = new Toss;
            $instance->addType($custom);
            $instance->add($custom);
        } catch(\Exception $e) {
            $this->assertEquals('LogicException', get_class($e));
            return;
        }
        $this->assertTrue(false);
    }

    public function testCheck()
    {
        $text = 'Custom Type';
        $instance = new Toss;
        $instance->addType('CustomExtended');
        $instance->CustomExtended($text);
        $this->assertTrue($instance->hasCustomExtended());
    }
}
