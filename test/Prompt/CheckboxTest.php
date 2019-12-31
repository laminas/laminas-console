<?php

/**
 * @see       https://github.com/laminas/laminas-console for the canonical source repository
 * @copyright https://github.com/laminas/laminas-console/blob/master/COPYRIGHT.md
 * @license   https://github.com/laminas/laminas-console/blob/master/LICENSE.md New BSD License
 */

namespace LaminasTest\Console\Prompt;

use Laminas\Console\Prompt\Checkbox;
use LaminasTest\Console\TestAssets\ConsoleAdapter;

/**
 * @group      Laminas_Console
 * @covers \Laminas\Console\Prompt\Checkbox
 */
class CheckboxTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var ConsoleAdapter
     */
    protected $adapter;

    public function setUp()
    {
        $this->adapter = new ConsoleAdapter(false);
        $this->adapter->stream = fopen('php://memory', 'w+');
    }

    public function tearDown()
    {
        fclose($this->adapter->stream);
    }

    public function testCanCheckOneOption()
    {
        fwrite($this->adapter->stream, "0");
        fwrite($this->adapter->stream, "\n");
        rewind($this->adapter->stream);

        $checkbox = new Checkbox('Check an option :', array('foo', 'bar'));
        $checkbox->setConsole($this->adapter);
        ob_start();
        $response = $checkbox->show();
        $text = ob_get_clean();
        $this->assertSame(1, substr_count($text, '0) [X] foo'));
        $this->assertSame(1, substr_count($text, '0) [ ] foo'));
        $this->assertSame(2, substr_count($text, '1) [ ] bar'));
        $this->assertEquals(array('0' => 'foo'), $response);
    }

    public function testCanUncheckOneOption()
    {
        fwrite($this->adapter->stream, "0");
        fwrite($this->adapter->stream, "0");
        fwrite($this->adapter->stream, "\n");
        rewind($this->adapter->stream);

        $checkbox = new Checkbox('Check an option :', array('foo', 'bar'));
        $checkbox->setConsole($this->adapter);
        ob_start();
        $response = $checkbox->show();
        $text = ob_get_clean();
        $this->assertSame(1, substr_count($text, '0) [X] foo'));
        $this->assertSame(2, substr_count($text, '0) [ ] foo'));
        $this->assertSame(3, substr_count($text, '1) [ ] bar'));
        $this->assertEquals(array(), $response);
    }

    public function testCanCheckTwoOption()
    {
        fwrite($this->adapter->stream, "0");
        fwrite($this->adapter->stream, "1");
        fwrite($this->adapter->stream, "\n");
        rewind($this->adapter->stream);

        $checkbox = new Checkbox('Check an option :', array('foo', 'bar'));
        $checkbox->setConsole($this->adapter);
        ob_start();
        $response = $checkbox->show();
        $text = ob_get_clean();
        $this->assertSame(2, substr_count($text, '0) [X] foo'));
        $this->assertSame(1, substr_count($text, '1) [X] bar'));
        $this->assertSame(1, substr_count($text, '0) [ ] foo'));
        $this->assertSame(2, substr_count($text, '1) [ ] bar'));
        $this->assertEquals(array('0' => 'foo', '1' => 'bar'), $response);
    }

    public function testCanCheckOptionWithCustomIndex()
    {
        fwrite($this->adapter->stream, "2");
        fwrite($this->adapter->stream, "\n");
        rewind($this->adapter->stream);

        $checkbox = new Checkbox('Check an option :', array('2' => 'foo', '6' => 'bar'));
        $checkbox->setConsole($this->adapter);
        ob_start();
        $response = $checkbox->show();
        $text = ob_get_clean();
        $this->assertSame(1, substr_count($text, '2) [X] foo'));
        $this->assertSame(1, substr_count($text, '2) [ ] foo'));
        $this->assertSame(2, substr_count($text, '6) [ ] bar'));
        $this->assertEquals(array('0' => 'foo'), $response);
    }
}
