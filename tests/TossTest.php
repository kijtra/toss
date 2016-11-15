<?php
use Kijtra\Toss;
use Kijtra\Toss\Type;

class TossTest extends \PHPUnit_Framework_TestCase
{
    public function testDefaultType()
    {
        $type = 'ERROR';
        $instance = new Toss;
        $instance->setDefaultType($type);
        $this->assertEquals('error', $instance->getDefaultType());
    }

    public function testGetAvailableTypes()
    {
        $instance = new Toss;
        $avails = $instance->getAvailableTypes();
        $this->assertTrue(in_array('error', $avails));
    }

    public function testUnAvailableType()
    {
        try {
            $instance = new Toss('Message', 'aiueo');
        } catch(\Exception $e) {
            $this->assertEquals('InvalidArgumentException', get_class($e));
            return;
        }

        $this->assertTrue(false);
    }

    public function testGetMessage()
    {
        $text = 'Message';
        $instance = new Toss($text, 'error');
        $message = $instance->getMessage();
        $this->assertTrue($message instanceof Type\Error);
        $this->assertEquals($text, $message->message());
    }

    public function testGetMessageAtTypeInstance()
    {
        $text = 'Message';
        $type = new Type\Error($text);
        $instance = new Toss($type);
        $message = $instance->getMessage();
        $this->assertEquals((string) $message, $text);
    }

    public function testSetMessageByAlias()
    {
        $text = 'Message';
        $instance = new Toss;
        $instance->error($text);
        $message = $instance->getMessage();
        $this->assertTrue($message instanceof Type\Error);
        $this->assertEquals($text, $message->message());
    }

    public function testGetByArray()
    {
        $text = 'Message';
        $instance = new Toss($text, 'success');
        $messages = $instance->success();
        $this->assertTrue(($messages[0]->message === $text));
    }

    public function testHaveByProperty()
    {
        $text = 'Message';
        $instance = new Toss($text, 'notice');
        $this->assertTrue(($instance->notice[0]->message === $text));
    }

    public function testHaveByArray()
    {
        $text = 'Message';
        $instance = new Toss($text, 'warning');
        $this->assertTrue(($instance['warning'][0]->message === $text));
    }

    public function testHasMessage()
    {
        $text = 'Message';
        $instance = new Toss($text, 'warning');
        $this->assertTrue($instance->has('warning'));
    }

    public function testNotHaveMessage()
    {
        $text = 'Message';
        $instance = new Toss($text, 'warning');
        $this->assertFalse($instance->has('error'));
    }

    public function testHasMessageByMethod()
    {
        $text = 'Message';
        $instance = new Toss($text, 'warning');
        $this->assertTrue($instance->hasWarning());
        $this->assertFalse($instance->hasError());
    }

    public function testHasMessageByProperty()
    {
        $text = 'Message';
        $instance = new Toss($text, 'warning');
        $this->assertTrue($instance->hasWarning);
        $this->assertFalse($instance->hasError);
    }

    public function testHasMessageByPropertyAlias()
    {
        $text = 'Message';
        $instance = new Toss($text, 'warning');
        $this->assertTrue($instance->isWarning);
        $this->assertFalse($instance->isError);
    }

    public function testIsNothing()
    {
        $instance = new Toss;
        $this->assertTrue($instance->isNothing());
    }

    public function testIsEmpty()
    {
        $instance = new Toss;
        $this->assertTrue($instance->isEmpty());
    }

    public function testIsNotNothing()
    {
        $instance = new Toss('Message');
        $this->assertFalse($instance->isNothing());
    }

    public function testClear()
    {
        $instance = new Toss('Message');
        $this->assertFalse($instance->isNothing());
        $instance->clear();
        $this->assertTrue($instance->isNothing());
    }

    public function testClearType()
    {
        $instance = new Toss;
        $instance->add('Message', 'info');
        $instance->add('Message', 'notice');
        $this->assertFalse($instance->isNothing());
        $instance->clear('info');
        $this->assertFalse($instance->isNothing());
        $this->assertFalse($instance->hasInfo());
        $this->assertTrue($instance->hasNotice());
        $instance->clear('notice');
        $this->assertTrue($instance->isNothing());
    }

    public function testWithDataAsConstruct()
    {
        $instance = new Toss('Message', null, 'test');
        $this->assertEquals($instance->getMessage()->data(), 'test');
    }

    public function testWithDataAsAdding()
    {
        $instance = new Toss;
        $message = $instance->add('Message', 'info', 'test');
        $this->assertEquals($message->data(), 'test');
    }

    public function testWithDataAsMethod()
    {
        $instance = new Toss;
        $message = $instance->add('Message', 'info')->setData('test');
        $this->assertEquals($message->data(), 'test');
    }

    public function testWithDataAsShortMethod()
    {
        $instance = new Toss;
        $message = $instance->add('Message', 'info')->data('test');
        $this->assertEquals($message->data(), 'test');
    }

    public function testGetDataAsProperty()
    {
        $instance = new Toss;
        $message = $instance->add('Message', 'info')->data('test');
        $this->assertEquals($message->data, 'test');
    }

    public function testCountable()
    {
        $instance = new Toss;
        $instance->add('Message', 'success');
        $instance->add('Message', 'info');
        $this->assertEquals(count($instance), $instance->count());
    }

    public function testIterator()
    {
        $text = array(
            'First',
            'Second',
        );
        $instance = new Toss;
        $instance->add($text[0], 'warning');
        $instance->add($text[1], 'error');
        foreach ($instance as $type => $group) {
            if ('warning' === $type) {
                $this->assertEquals($text[0], $group->first()->message());
            } elseif ('error' === $type) {
                $this->assertEquals($text[1], $group->first()->message());
            } else {
                $this->assertTrue(false);
            }
        }
    }
}
