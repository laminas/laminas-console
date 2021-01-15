<?php

/**
 * @see       https://github.com/laminas/laminas-console for the canonical source repository
 * @copyright https://github.com/laminas/laminas-console/blob/master/COPYRIGHT.md
 * @license   https://github.com/laminas/laminas-console/blob/master/LICENSE.md New BSD License
 */

namespace LaminasTest\Console\Prompt;

use Laminas\Console\Prompt\Char;
use LaminasTest\Console\TestAssets\ConsoleAdapter;
use PHPUnit\Framework\TestCase;

/**
 * @group      Laminas_Console
 */
class CharTest extends TestCase
{
    /**
     * @var ConsoleAdapter
     */
    protected $adapter;

    public function setUp(): void
    {
        $this->adapter = new ConsoleAdapter();
        $this->adapter->stream = fopen('php://memory', 'w+');
    }

    public function tearDown(): void
    {
        fclose($this->adapter->stream);
    }

    public function testCanPromptChar()
    {
        fwrite($this->adapter->stream, 'a');

        $char = new Char();
        $char->setEcho(false);
        $char->setConsole($this->adapter);
        ob_start();
        $response = $char->show();
        $text = ob_get_clean();
        $this->assertEquals("Please hit a key\n", $text);
        $this->assertEquals('a', $response);
    }

    public function testCanPromptCharWithCharNotInDefaultMask()
    {
        fwrite($this->adapter->stream, '*zywa');

        $char = new Char();
        $char->setEcho(false);
        $char->setConsole($this->adapter);
        ob_start();
        $response = $char->show();
        ob_get_clean();
        $this->assertEquals('z', $response);
    }

    public function testCanPromptCharWithNewQuestionAndMask()
    {
        fwrite($this->adapter->stream, 'foo123');

        $char = new Char("Give a number", '0123456789');
        $char->setEcho(false);
        $char->setConsole($this->adapter);
        ob_start();
        $response = $char->show();
        $text = ob_get_clean();
        $this->assertEquals("Give a number\n", $text);
        $this->assertEquals('1', $response);
    }

    public function testCanPromptCharWithIgnoreCaseByDefault()
    {
        fwrite($this->adapter->stream, 'FOObar');

        $char = new Char();
        $char->setEcho(false);
        $char->setConsole($this->adapter);
        ob_start();
        $response = $char->show();
        ob_get_clean();
        $this->assertEquals('F', $response);
    }

    public function testCanPromptCharWithoutIgnoreCase()
    {
        fwrite($this->adapter->stream, 'FOObar');

        $char = new Char();
        $char->setEcho(false);
        $char->setConsole($this->adapter);
        $char->setIgnoreCase(false);
        ob_start();
        $response = $char->show();
        ob_get_clean();
        $this->assertEquals('b', $response);
    }

    /**
     * @group 12
     */
    public function testShowWritesToConsoleAdapterWhenEchoIsSetToTrue()
    {
        fwrite($this->adapter->stream, 'a');

        $char = new Char();
        $char->setEcho(true);
        $char->setConsole($this->adapter);

        ob_start();
        $response = $char->show();
        $text = ob_get_clean();

        $this->assertEquals("Please hit a keya\n", $text);
        $this->assertEquals('a', $response);
    }
}
