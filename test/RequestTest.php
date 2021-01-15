<?php

/**
 * @see       https://github.com/laminas/laminas-console for the canonical source repository
 * @copyright https://github.com/laminas/laminas-console/blob/master/COPYRIGHT.md
 * @license   https://github.com/laminas/laminas-console/blob/master/LICENSE.md New BSD License
 */

namespace LaminasTest\Console;

use Laminas\Console\Request;
use PHPUnit\Framework\TestCase;

/**
 * @group      Laminas_Console
 */
class RequestTest extends TestCase
{
    public function setUp(): void
    {
        if (ini_get('register_argc_argv') == false) {
            $this->markTestSkipped(
                "Cannot Test Laminas\\Console\\Getopt without 'register_argc_argv' ini option true."
            );
        }
    }

    public function testCanConstructRequestAndGetParams()
    {
        $_SERVER['argv'] = ['foo.php', 'foo' => 'baz', 'bar'];
        $_ENV["FOO_VAR"] = "bar";

        $request = new Request();
        $params = $request->getParams();

        $this->assertCount(2, $params);
        $this->assertEquals($params->toArray(), ['foo' => 'baz', 'bar']);
        $this->assertEquals($request->getParam('foo'), 'baz');
        $this->assertEquals($request->getScriptName(), 'foo.php');
        $this->assertGreaterThanOrEqual(1, count($request->env()));
        $this->assertEquals($request->env()->get('FOO_VAR'), 'bar');
        $this->assertEquals($request->getEnv('FOO_VAR'), 'bar');
    }
}
