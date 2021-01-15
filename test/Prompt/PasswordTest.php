<?php

/**
 * @see       https://github.com/laminas/laminas-console for the canonical source repository
 * @copyright https://github.com/laminas/laminas-console/blob/master/COPYRIGHT.md
 * @license   https://github.com/laminas/laminas-console/blob/master/LICENSE.md New BSD License
 */

namespace LaminasTest\Console\Prompt;

use PHPUnit\Framework\TestCase;
use Prophecy\PhpUnit\ProphecyTrait;
use Laminas\Console\Prompt\Password;
use Prophecy\Prophecy\ObjectProphecy;
use Laminas\Console\Adapter\AbstractAdapter;

/**
 * Tests for {@see \Laminas\Console\Prompt\Password}
 *
 * @covers \Laminas\Console\Prompt\Password
 */
class PasswordTest extends TestCase
{
    use ProphecyTrait;

    /**
     * @var AbstractAdapter|ObjectProphecy
     */
    protected $adapter;

    public function setUp(): void
    {
        $this->adapter = $this->prophesize(AbstractAdapter::class);
    }

    public function testCanPromptPassword()
    {
        $this->adapter->writeLine('Password: ')->shouldBeCalledTimes(1);
        $this->adapter->readChar()->willReturn('f', 'o', 'o', PHP_EOL)->shouldBeCalledTimes(4);
        $this->adapter->clearLine()->willReturn(null);
        $this->adapter->write()->shouldNotBeCalled();

        $char = new Password('Password: ');

        $char->setConsole($this->adapter->reveal());

        $this->assertEquals('foo', $char->show());
    }

    public function testCanPromptPasswordRepeatedly()
    {
        $this->adapter->writeLine('New password? ')->shouldBeCalledTimes(2);
        $this->adapter->readChar()->willReturn('b', 'a', 'r', PHP_EOL, 'b', 'a', 'z', PHP_EOL)->shouldBeCalledTimes(8);
        $this->adapter->clearLine()->willReturn(null);
        $this->adapter->write()->shouldNotBeCalled();

        $char = new Password('New password? ');

        $char->setConsole($this->adapter->reveal());

        $this->assertEquals('bar', $char->show());
        $this->assertEquals('baz', $char->show());
    }

    public function testProducesStarSymbolOnInput()
    {
        $this->adapter->writeLine('New password? ')->shouldBeCalledTimes(1);
        $this->adapter->readChar()->willReturn('t', 'a', 'b', PHP_EOL)->shouldBeCalledTimes(4);
        $this->adapter->clearLine()->willReturn(null);
        $this->adapter->write('*')->shouldBeCalledTimes(1);
        $this->adapter->write('**')->shouldBeCalledTimes(1);
        $this->adapter->write('***')->shouldBeCalledTimes(1);

        $char = new Password('New password? ', true);

        $char->setConsole($this->adapter->reveal());

        $this->assertSame('tab', $char->show());
    }
}
