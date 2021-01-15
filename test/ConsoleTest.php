<?php

/**
 * @see       https://github.com/laminas/laminas-console for the canonical source repository
 * @copyright https://github.com/laminas/laminas-console/blob/master/COPYRIGHT.md
 * @license   https://github.com/laminas/laminas-console/blob/master/LICENSE.md New BSD License
 */

namespace LaminasTest\Console;

use Laminas\Console\Console;
use PHPUnit\Framework\TestCase;

/**
 * @group      Laminas_Console
 */
class ConsoleTest extends TestCase
{
    public function setUp(): void
    {
        Console::overrideIsConsole(null);
        Console::resetInstance();
    }

    public function testCanTestIsConsole()
    {
        $this->assertTrue(Console::isConsole());
        $className = Console::detectBestAdapter();
        $adpater = new $className;
        $this->assertInstanceOf('Laminas\Console\Adapter\AdapterInterface', $adpater);

        Console::overrideIsConsole(false);

        $this->assertFalse(Console::isConsole());
        $this->assertEquals(null, Console::detectBestAdapter());
    }

    public function testCanOverrideIsConsole()
    {
        $this->assertEquals(true, Console::isConsole());

        Console::overrideIsConsole(true);
        $this->assertEquals(true, Console::isConsole());

        Console::overrideIsConsole(false);
        $this->assertEquals(false, Console::isConsole());

        Console::overrideIsConsole(1);
        $this->assertEquals(true, Console::isConsole());

        Console::overrideIsConsole('false');
        $this->assertEquals(true, Console::isConsole());
    }

    public function testCanGetInstance()
    {
        $console = Console::getInstance();
        $this->assertInstanceOf('Laminas\Console\Adapter\AdapterInterface', $console);
    }

    public function testCanNotGetInstanceInNoConsoleMode()
    {
        Console::overrideIsConsole(false);
        $this->expectException('Laminas\Console\Exception\RuntimeException');
        Console::getInstance();
    }

    public function testCanForceInstance()
    {
        $console = Console::getInstance('Posix');
        $this->assertInstanceOf('Laminas\Console\Adapter\AdapterInterface', $console);
        $this->assertInstanceOf('Laminas\Console\Adapter\Posix', $console);

        Console::overrideIsConsole(null);
        Console::resetInstance();

        $console = Console::getInstance('Windows');
        $this->assertInstanceOf('Laminas\Console\Adapter\AdapterInterface', $console);
        $this->assertInstanceOf('Laminas\Console\Adapter\Windows', $console);
    }
}
