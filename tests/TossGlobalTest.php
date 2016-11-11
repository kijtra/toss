<?php
use Kijtra\Toss;
use Kijtra\Toss\Type;

class TossGlobalTest extends \PHPUnit_Framework_TestCase
{
    public function testMakeGlobal()
    {
        $global = Toss::getGlobal();
        $instance = new Toss;
        $this->assertEquals($global, $instance::getGlobal());
    }

    public function testToGlobalAtConstructor()
    {
        $message = new Toss('Message', 'error', true);
        $global = Toss::getGlobal();
        $this->assertTrue($message->hasError());
        $this->assertTrue($global->hasError());
    }

    public function testToGlobalAtType()
    {
        $type = new Type\Notice('Message');
        $type->toGlobal();
        $global = Toss::getGlobal();
        $this->assertTrue($global->hasNotice());
    }

    public function testToGlobalAtMessage()
    {
        $message = new Toss('Message', 'warning');
        $global = Toss::getGlobal();
        $message->getMessage()->toGlobal();
        $this->assertTrue($global->hasWarning());
    }

    public function testClear()
    {
        $message = new Toss('Message', 'info', true);
        $global = Toss::getGlobal();

        $global->clear('info');
        $this->assertFalse($global->hasInfo());
        $this->assertTrue($global->hasError());
        $this->assertFalse($global->isNothing());

        $global->clear();
        $this->assertFalse($global->hasError());
        $this->assertTrue($global->isNothing());
        $this->assertTrue($message->hasInfo());
        $this->assertFalse($message->isNothing());
    }
}
