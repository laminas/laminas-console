<?php

/**
 * @see       https://github.com/laminas/laminas-console for the canonical source repository
 * @copyright https://github.com/laminas/laminas-console/blob/master/COPYRIGHT.md
 * @license   https://github.com/laminas/laminas-console/blob/master/LICENSE.md New BSD License
 */

namespace LaminasTest\Console\Adapter;

use LaminasTest\Console\TestAssets\ConsoleAdapter;

/**
 * @group      Laminas_Console
 */
class AbstractAdapterTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var ConsoleAdapter
     */
    protected $adapter;

    public function setUp()
    {
        $this->adapter = new ConsoleAdapter();
        $this->adapter->stream = fopen('php://memory', 'w+');
    }

    public function tearDown()
    {
        fclose($this->adapter->stream);
    }

    public function testWriteChar()
    {
        ob_start();
        $this->adapter->write('foo');
        $this->assertEquals('foo', ob_get_clean());
    }

    public function testWriteText()
    {
        ob_start();
        $this->adapter->writeText('foo');
        $this->assertEquals('foo', ob_get_clean());
    }

    public function testWriteLine()
    {
        ob_start();
        $this->adapter->writeLine('foo');
        $this->assertEquals("foo" . PHP_EOL, ob_get_clean());

        ob_start();
        $this->adapter->writeLine("foo\nbar");
        $this->assertEquals("foo\nbar" . PHP_EOL, ob_get_clean());

        ob_start();
        $this->adapter->writeLine("\rfoo\r");
        $this->assertEquals("\rfoo\r" . PHP_EOL, ob_get_clean());
    }

    /**
     * @issue Laminas-4051
     * @link https://github.com/zendframework/zf2/issues/4051
     */
    public function testWriteLineOverflowAndWidthMatch()
    {
        // make sure console width is reported as 80
        $this->adapter->setTestWidth(80);

        ob_start();
        $line = str_repeat('#', 80);
        $this->adapter->writeLine($line);
        $this->assertEquals($line . PHP_EOL, ob_get_clean());

        ob_start();
        $line2 = $line . '#';
        $this->adapter->writeLine($line2);
        $this->assertEquals($line2 . PHP_EOL, ob_get_clean());
    }

    public function testReadLine()
    {
        fwrite($this->adapter->stream, 'baz');

        $line = $this->adapter->readLine();
        $this->assertEquals($line, 'baz');
    }

    public function testReadLineWithLimit()
    {
        fwrite($this->adapter->stream, 'baz, bar, foo');

        $line = $this->adapter->readLine(6);
        $this->assertEquals($line, 'baz, b');
    }

    public function testReadChar()
    {
        fwrite($this->adapter->stream, 'bar');

        $char = $this->adapter->readChar();
        $this->assertEquals($char, 'b');
    }

    public function testReadCharWithMask()
    {
        fwrite($this->adapter->stream, 'bar');

        $char = $this->adapter->readChar('ar');
        $this->assertEquals($char, 'a');
    }

    public function testReadCharWithMaskInsensitiveCase()
    {
        fwrite($this->adapter->stream, 'bAr');

        $char = $this->adapter->readChar('ar');
        $this->assertEquals($char, 'r');
    }
}
