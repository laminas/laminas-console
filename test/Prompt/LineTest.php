<?php

/**
 * @see       https://github.com/laminas/laminas-console for the canonical source repository
 * @copyright https://github.com/laminas/laminas-console/blob/master/COPYRIGHT.md
 * @license   https://github.com/laminas/laminas-console/blob/master/LICENSE.md New BSD License
 */

namespace LaminasTest\Console\Prompt;

use Laminas\Console\Prompt\Line;
use LaminasTest\Console\TestAssets\ConsoleAdapter;
use PHPUnit\Framework\TestCase;

/**
 * @group      Laminas_Console
 */
class LineTest extends TestCase
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

    public function testCanReadLine()
    {
        fwrite($this->adapter->stream, 'Bryan is in the kitchen');

        $line = new Line('Where is Bryan ?');
        $line->setConsole($this->adapter);
        ob_start();
        $response = $line->show();
        $text = ob_get_clean();
        $this->assertEquals($text, "Where is Bryan ?");
        $this->assertEquals('Bryan is in the kitchen', $response);
    }

    public function testCanReadLineWithMax()
    {
        fwrite($this->adapter->stream, 'Kitchen no ?');

        $line = new Line('Where is Bryan ?', false, 7);
        $line->setConsole($this->adapter);
        ob_start();
        $response = $line->show();
        $text = ob_get_clean();
        $this->assertEquals($text, "Where is Bryan ?");
        $this->assertEquals('Kitchen', $response);
    }

    public function testCanReadLineWithEmptyAnswer()
    {
        $line = new Line('Where is Bryan ?', true);
        $line->setConsole($this->adapter);
        ob_start();
        $response = $line->show();
        $text = ob_get_clean();
        $this->assertEquals($text, "Where is Bryan ?");
        $this->assertEquals('', $response);
    }
}
