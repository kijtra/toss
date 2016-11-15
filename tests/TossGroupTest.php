<?php
use Kijtra\Toss;
use Kijtra\Toss\Group;
use Kijtra\Toss\Type;

class TossGroupTest extends \PHPUnit_Framework_TestCase
{
    public function testCheckInstance()
    {
        $instance = new Toss('Message', 'info');
        $messages = $instance->getMessages('info');
        $this->assertTrue($messages instanceof Group);
    }

    public function testGetCount()
    {
        $text = 'Message';
        $instance = new Toss;
        $instance->add($text);
        $instance->add($text);
        $messages = $instance->getMessages();
        $this->assertEquals(2, $messages->count());
    }

    public function testGetFirst()
    {
        $text = 'First';
        $instance = new Toss;
        $instance->add($text);
        $instance->add('Second');
        $messages = $instance->getMessages();
        $this->assertEquals($text, $messages->first()->message());
    }

    public function testGetLast()
    {
        $text = 'Second';
        $instance = new Toss;
        $instance->add('First');
        $instance->add($text);
        $messages = $instance->getMessages();
        $this->assertEquals($text, $messages->last()->message());
    }

    public function testGetCurrent()
    {
        $text = 'First';
        $instance = new Toss;
        $instance->add($text);
        $instance->add('Second');
        $messages = $instance->getMessages();
        $this->assertEquals($text, $messages->current()->message());
    }

    public function testGetEnd()
    {
        $text = 'Second';
        $instance = new Toss;
        $instance->add('First');
        $instance->add($text);
        $messages = $instance->getMessages();
        $this->assertEquals($text, $messages->end()->message());
    }

    public function testIsEmpty()
    {
        $instance = new Toss('Message', 'warning');
        $messages = $instance->warning();
        $this->assertFalse($messages->isEmpty());
        $this->assertFalse($messages->is('Empty'));
    }

    public function testIsType()
    {
        $instance = new Toss('Message', 'warning');
        $messages = $instance->warning();
        $this->assertTrue($messages->is('Warning'));
        $this->assertTrue($messages->isWarning());
        $this->assertFalse($messages->isInfo());
    }

    public function testIterator()
    {
        $text = array(
            'First',
            'Second',
        );
        $instance = new Toss;
        $instance->add($text[0]);
        $instance->add($text[1]);
        foreach ($instance->info() as $key => $val) {
            $this->assertEquals($text[$key], $val->message());
        }
    }

    public function testToArray()
    {
        $instance = new Toss('Message', 'warning');
        $messages = $instance->warning();
        $this->assertFalse(is_array($messages));
        $this->assertTrue(is_array($messages->toArray()));
    }
}
