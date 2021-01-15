<?php

/**
 * @see       https://github.com/laminas/laminas-console for the canonical source repository
 * @copyright https://github.com/laminas/laminas-console/blob/master/COPYRIGHT.md
 * @license   https://github.com/laminas/laminas-console/blob/master/LICENSE.md New BSD License
 */

namespace LaminasTest\Console\Adapter;

use PHPUnit\Framework\TestCase;
use LaminasTest\Console\TestAssets\ConsoleAdapter;
use Laminas\Console\Exception\InvalidArgumentException;

/**
 * @group      Laminas_Console
 */
class AbstractAdapterTest extends TestCase
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

    public function testEncodeText()
    {
        //Utf8 string
        $text = '\u00E9\u00E9\u00E9';

        //Console UTF8 - Text utf8
        $this->adapter->setTestUtf8(true);
        $encodedText = $this->adapter->encodeText($text);
        $this->assertEquals($text, $encodedText);

        //Console UTF8 - Text not utf8
        $encodedText = $this->adapter->encodeText(utf8_decode($text));
        $this->assertEquals($text, $encodedText);

        //Console not UTF8 - Text utf8
        $this->adapter->setTestUtf8(false);
        $encodedText = $this->adapter->encodeText($text);
        $this->assertEquals(utf8_decode($text), $encodedText);

        //Console not UTF8 - Text not utf8
        $encodedText = $this->adapter->encodeText(utf8_decode($text));
        $this->assertEquals(utf8_decode($text), $encodedText);
    }

    public function testWriteTextBlockSameAsWidth()
    {
        //set some text that's the same size as the width
        $text = 'hello there, I am short!';
        $width = strlen($text);

        ob_start();
        $this->adapter->writeTextBlock($text, $width);
        $this->assertSame($text, ob_get_clean());
    }

    public function testTextBlockLongUnbreakableWord()
    {
        $text = 'thisisaverylongwordthatwontbreakproperlysothereyouhaveit and here is some more text';
        $expected = ['thisisaver', 'ylongwordt', 'hatwontbre', 'akproperly'
           , 'sothereyou', 'haveit and', 'here is', 'some more'
           , 'text'];

        ob_start();
        $this->adapter->writeTextBlock($text, 10);
        $this->assertSame($expected, $this->adapter->writtenData);

        //just get rid of the data in ob
        ob_get_clean();
    }
    public function testTextBlockLongerThanHeight()
    {
        $text = 'thisisaverylongwordthatwontbreakproperlysothereyouhaveit and here is some more text';
        $expected = ['thisisaver', 'ylongwordt', 'hatwontbre'];

        //reset tracking of written data
        $this->adapter->writtenData = [];

        ob_start();
        $this->adapter->writeTextBlock($text, 10, 3);
        $this->assertSame($expected, $this->adapter->writtenData);

        //just get rid of the data in ob
        ob_get_clean();
    }

    public function testInvalidCoords()
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectDeprecationMessage('Supplied X,Y coordinates are invalid.');
        $this->adapter->writeTextBlock('', 1, 1, -1, -9);
    }

    public function testInvalidWidth()
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectDeprecationMessage('Invalid width supplied.');
        $this->adapter->writeTextBlock('', 0);
    }

    public function testInvalidHeight()
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectDeprecationMessage('Invalid height supplied.');
        $this->adapter->writeTextBlock('', 80, 0, 2, 2);
    }
}
