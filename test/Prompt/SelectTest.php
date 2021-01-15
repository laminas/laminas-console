<?php

/**
 * @see       https://github.com/laminas/laminas-console for the canonical source repository
 * @copyright https://github.com/laminas/laminas-console/blob/master/COPYRIGHT.md
 * @license   https://github.com/laminas/laminas-console/blob/master/LICENSE.md New BSD License
 */

namespace LaminasTest\Console\Prompt;

use Laminas\Console\Prompt\Select;
use LaminasTest\Console\TestAssets\ConsoleAdapter;
use PHPUnit\Framework\TestCase;

/**
 * @group      Laminas_Console
 */
class SelectTest extends TestCase
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

    public function testCanSelectOption()
    {
        fwrite($this->adapter->stream, "0");

        $select = new Select('Select an option :', ['foo', 'bar']);
        $select->setConsole($this->adapter);
        ob_start();
        $response = $select->show();
        $text = ob_get_clean();
        $this->assertStringContainsString('0) foo', $text);
        $this->assertStringContainsString('1) bar', $text);
        $this->assertEquals('0', $response);
    }

    public function testCanSelectOptionWithCustomIndex()
    {
        fwrite($this->adapter->stream, "2");

        $select = new Select('Select an option :', ['2' => 'foo', '6' => 'bar']);
        $select->setConsole($this->adapter);
        ob_start();
        $response = $select->show();
        $text = ob_get_clean();
        $this->assertStringContainsString('2) foo', $text);
        $this->assertStringContainsString('6) bar', $text);
        $this->assertEquals('2', $response);
    }
}
