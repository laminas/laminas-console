<?php

/**
 * @see       https://github.com/laminas/laminas-console for the canonical source repository
 * @copyright https://github.com/laminas/laminas-console/blob/master/COPYRIGHT.md
 * @license   https://github.com/laminas/laminas-console/blob/master/LICENSE.md New BSD License
 */

namespace LaminasTest\Console\Prompt;

use Laminas\Console\Prompt\Confirm;
use LaminasTest\Console\TestAssets\ConsoleAdapter;
use PHPUnit\Framework\TestCase;

/**
 * @group      Laminas_Console
 */
class ConfirmTest extends TestCase
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

    public function testCanPromptConfirm()
    {
        fwrite($this->adapter->stream, 'y');

        $confirm = new Confirm("Is Laminas the best framework ?");
        $confirm->setEcho(false);
        $confirm->setConsole($this->adapter);
        ob_start();
        $response = $confirm->show();
        $text = ob_get_clean();
        $this->assertEquals($text, "Is Laminas the best framework ?\n");
        $this->assertTrue($response);
    }

    public function testCanPromptConfirmWithDefaultIgnoreCase()
    {
        fwrite($this->adapter->stream, 'Y');

        $confirm = new Confirm("Is Laminas the best framework ?");
        $confirm->setEcho(false);
        $confirm->setConsole($this->adapter);
        ob_start();
        $response = $confirm->show();
        $text = ob_get_clean();
        $this->assertEquals($text, "Is Laminas the best framework ?\n");
        $this->assertTrue($response);
    }

    public function testCanPromptConfirmWithoutIgnoreCase()
    {
        fwrite($this->adapter->stream, 'Yn');

        $confirm = new Confirm("Is Laminas the best framework ?");
        $confirm->setEcho(false);
        $confirm->setConsole($this->adapter);
        $confirm->setIgnoreCase(false);
        ob_start();
        $response = $confirm->show();
        $text = ob_get_clean();
        $this->assertEquals($text, "Is Laminas the best framework ?\n");
        $this->assertFalse($response);
    }

    public function testCanPromptConfirmWithYesNoCharChanged()
    {
        fwrite($this->adapter->stream, 'on0');

        $confirm = new Confirm("Is Laminas the best framework ?", "1", "0");
        $confirm->setEcho(false);
        $confirm->setConsole($this->adapter);
        ob_start();
        $response = $confirm->show();
        $text = ob_get_clean();
        $this->assertEquals($text, "Is Laminas the best framework ?\n");
        $this->assertFalse($response);
    }

    public function testCanPromptConfirmWithYesNoCharChangedWithSetter()
    {
        fwrite($this->adapter->stream, 'oaB');

        $confirm = new Confirm("Is Laminas the best framework ?", "1", "0");
        $confirm->setYesChar("A");
        $confirm->setNoChar("B");
        $confirm->setEcho(false);
        $confirm->setConsole($this->adapter);
        ob_start();
        $response = $confirm->show();
        $text = ob_get_clean();
        $this->assertEquals($text, "Is Laminas the best framework ?\n");
        $this->assertTrue($response);
    }
}
