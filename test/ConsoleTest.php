<?php

/**
 * @see       https://github.com/laminas/laminas-console for the canonical source repository
 * @copyright https://github.com/laminas/laminas-console/blob/master/COPYRIGHT.md
 * @license   https://github.com/laminas/laminas-console/blob/master/LICENSE.md New BSD License
 */

namespace LaminasTest\Console;

use Laminas\Console\Adapter;
use Laminas\Console\Console;

/**
 * @group      Laminas_Console
 */
class ConsoleTest extends \PHPUnit_Framework_TestCase
{
    public function setUp()
    {
        Console::overrideIsConsole(null);
        Console::resetInstance();
    }

    public function testCanTestIsConsole()
    {
        $this->assertTrue(Console::isConsole());
        $className = Console::detectBestAdapter();
        $adpater = new $className;
        $this->assertTrue($adpater instanceof Adapter\AdapterInterface);

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
        $this->assertTrue($console instanceof Adapter\AdapterInterface);
    }

    public function testCanNotGetInstanceInNoConsoleMode()
    {
        Console::overrideIsConsole(false);
        $this->setExpectedException('Laminas\Console\Exception\RuntimeException');
        Console::getInstance();
    }

    public function testCanForceInstance()
    {
       $console = Console::getInstance('Posix');
       $this->assertTrue($console instanceof Adapter\AdapterInterface);
       $this->assertTrue($console instanceof Adapter\Posix);

       Console::overrideIsConsole(null);
       Console::resetInstance();

       $console = Console::getInstance('Windows');
       $this->assertTrue($console instanceof Adapter\AdapterInterface);
       $this->assertTrue($console instanceof Adapter\Windows);
    }
}
