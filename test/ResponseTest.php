<?php

/**
 * @see       https://github.com/laminas/laminas-console for the canonical source repository
 * @copyright https://github.com/laminas/laminas-console/blob/master/COPYRIGHT.md
 * @license   https://github.com/laminas/laminas-console/blob/master/LICENSE.md New BSD License
 */

namespace LaminasTest\Console;

use Laminas\Console\Response;
use PHPUnit\Framework\TestCase;

/**
 * @group      Laminas_Console
 */
class ResponseTest extends TestCase
{
    /**
     * @var Response
     */
    protected $response;

    public function setUp(): void
    {
        $this->response = new Response();
    }

    public function testInitialisation()
    {
        $this->assertEquals(false, $this->response->contentSent());
        $this->assertEquals(0, $this->response->getErrorLevel());
    }

    public function testSetContent()
    {
        $this->response->setContent('foo, bar');
        $this->assertEquals(false, $this->response->contentSent());
        ob_start();
        $this->response->sendContent();
        $content = ob_get_clean();
        $this->assertEquals('foo, bar', $content);
        $this->assertEquals(true, $this->response->contentSent());
        $this->assertEquals($this->response, $this->response->sendContent());
    }

    public function testGetErrorLevelDefault()
    {
        $this->assertSame(0, $this->response->getErrorLevel());
    }

    public function testSetErrorLevel()
    {
        $errorLevel = 2;
        $this->response->setErrorLevel($errorLevel);
        $this->assertSame($errorLevel, $this->response->getErrorLevel());
    }

    public function testSetErrorLevelWithNonIntValueIsNotSet()
    {
        $errorLevel = '2String';
        $this->response->setErrorLevel($errorLevel);
        $this->assertSame(0, $this->response->getErrorLevel());
    }

    /*
    public function testSetContentWithExit()
    {
        if (!function_exists('set_exit_overload')) {
            $this->markTestSkipped("Install ext/test_helpers to test method with exit :
            https://github.com/sebastianbergmann/php-test-helpers.");
        }

        $self = $this;
        set_exit_overload(
            function ($param = null) use ($self) {
                if ($param) {
                    $self->assertEquals($param, 1);
                }

                return false;
            }
        );
        $this->response->setErrorLevel(1);
        $this->response->setContent('foo, bar');
        ob_start();
        $this->response->send();
        $content = ob_get_clean();
        $this->assertEquals('foo, bar', $content);

        unset_exit_overload();
    }
    */
}
