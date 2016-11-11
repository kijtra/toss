<?php
use Kijtra\Toss;
use Kijtra\Toss\Type;

class TossTypeTest extends \PHPUnit_Framework_TestCase
{
    public function testGetMessage()
    {
        $text = 'Message';
        $type = new Type\Error($text);
        $this->assertEquals($text, $type->getMessage());
    }

    public function testGetMessageByAlias()
    {
        $text = 'Message';
        $type = new Type\Error($text);
        $this->assertEquals($text, $type->message());
    }

    public function testGetMessageByProperty()
    {
        $text = 'Message';
        $type = new Type\Error($text);
        $this->assertEquals($text, $type->message);
    }

    public function testGetMessageByToString()
    {
        $text = 'Message';
        $type = new Type\Error($text);
        $this->assertEquals($text, (string) $type);
    }

    public function testMessageException()
    {
        try {
            $type = new Type\Error;
        } catch(\Exception $e) {
            $this->assertEquals('InvalidArgumentException', get_class($e));
            return;
        }

        $this->assertTrue(false);
    }

    public function testDirectCall()
    {
        try {
            $type = new Type;
        } catch(\Exception $e) {
            $this->assertEquals('LogicException', get_class($e));
            return;
        }

        $this->assertTrue(false);
    }

    public function testGetType()
    {
        $type = new Type\Error('Message');
        $this->assertEquals('error', $type->getType());
    }

    public function testGetTypeByAlias()
    {
        $type = new Type\Notice('Message');
        $this->assertEquals('notice', $type->type());
    }

    public function testGetTypeByProperty()
    {
        $type = new Type\Notice('Message');
        $this->assertEquals('notice', $type->type);
    }

    public function testGetFile()
    {
        $type = new Type\Error('Message');
        $this->assertEquals(__FILE__, $type->getFile());
    }

    public function testGetFileByAlias()
    {
        $type = new Type\Error('Message');
        $this->assertEquals(__FILE__, $type->file());
    }

    public function testGetFileByProperty()
    {
        $type = new Type\Error('Message');
        $this->assertEquals(__FILE__, $type->file);
    }

    public function testGetLine()
    {
        $type = new Type\Error('Message');
        $this->assertEquals((__LINE__ - 1), $type->getLine());
    }

    public function testGetLineByAlias()
    {
        $type = new Type\Error('Message');
        $this->assertEquals((__LINE__ - 1), $type->line());
    }

    public function testGetLineByProperty()
    {
        $type = new Type\Error('Message');
        $this->assertEquals((__LINE__ - 1), $type->line);
    }

    public function testPropertySet()
    {
        $type = new Type\Error('Message');
        $type->hogehoge = 'Hoge Hoge';
        $this->assertTrue(empty($type->hogehoge));
    }
    
    public function testSetException()
    {
        $text = 'Message';
        $type = new Type\Error(new Exception($text));
        $this->assertEquals($text, $type->message());
    }

    public function testSetExceptionError()
    {
        try {
            $type = new Type\Error(new Exception());
        } catch(\Exception $e) {
            $this->assertEquals('InvalidArgumentException', get_class($e));
            return;
        }

        $this->assertTrue(false);
    }

    public function testIsType()
    {
        $type = new Type\Error('Message');
        $this->assertTrue($type->is('error'));
    }

    public function testIsNotType()
    {
        $type = new Type\Notice('Message');
        $this->assertFalse($type->is('success'));
    }

    public function testIsTypeByMethod()
    {
        $type = new Type\Error('Message');
        $this->assertTrue($type->isError());
    }

    public function testIsTypeByProperty()
    {
        $type = new Type\Error('Message');
        $this->assertTrue($type->isError);
    }

    public function testGetGlobal()
    {
        $type = new Type\Error('Message');
        $this->assertEquals(Toss::global(), $type->getGlobal());
    }
}
