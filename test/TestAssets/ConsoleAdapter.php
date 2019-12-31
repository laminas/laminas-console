<?php

/**
 * @see       https://github.com/laminas/laminas-console for the canonical source repository
 * @copyright https://github.com/laminas/laminas-console/blob/master/COPYRIGHT.md
 * @license   https://github.com/laminas/laminas-console/blob/master/LICENSE.md New BSD License
 */

namespace LaminasTest\Console\TestAssets;

use Laminas\Console\Adapter\AbstractAdapter;

/**
 * @group      Laminas_Console
 */
class ConsoleAdapter extends AbstractAdapter
{
    public $stream;

    public $autoRewind = true;

    /**
     * Read a single line from the console input
     *
     * @param int $maxLength        Maximum response length
     * @return string
     */
    public function readLine($maxLength = 2048)
    {
        if($this->autoRewind) {
            rewind($this->stream);
        }
        $line = stream_get_line($this->stream, $maxLength, PHP_EOL);
        return rtrim($line,"\n\r");
    }

    /**
     * Read a single character from the console input
     *
     * @param string|null   $mask   A list of allowed chars
     * @return string
     */
    public function readChar($mask = null)
    {
        if($this->autoRewind) {
            rewind($this->stream);
        }
        do {
            $char = fread($this->stream, 1);
        } while ("" === $char || ($mask !== null && false === strstr($mask, $char)));
        return $char;
    }
}
