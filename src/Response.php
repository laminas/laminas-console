<?php

/**
 * @see       https://github.com/laminas/laminas-console for the canonical source repository
 * @copyright https://github.com/laminas/laminas-console/blob/master/COPYRIGHT.md
 * @license   https://github.com/laminas/laminas-console/blob/master/LICENSE.md New BSD License
 */

namespace Laminas\Console;

use Laminas\Stdlib\Message;
use Laminas\Stdlib\ResponseInterface;

/**
 * @category   Laminas
 * @package    Laminas_Console
 */
class Response extends Message implements ResponseInterface
{
    protected $contentSent = false;

    public function contentSent()
    {
        return $this->contentSent;
    }

    /**
     * Set the error level that will be returned to shell.
     *
     * @param integer   $errorLevel
     * @return Response
     */
    public function setErrorLevel($errorLevel)
    {
        $this->setMetadata('errorLevel', $errorLevel);
        return $this;
    }

    /**
     * Get response error level that will be returned to shell.
     *
     * @return integer|0
     */
    public function getErrorLevel()
    {
        return $this->getMetadata('errorLevel', 0);
    }

    public function sendContent()
    {
        if ($this->contentSent()) {
            return $this;
        }
        echo $this->getContent();
        $this->contentSent = true;
        return $this;
    }

    public function send()
    {
        $this->sendContent();
        $errorLevel = (int) $this->getMetadata('errorLevel',0);
        exit($errorLevel);
    }
}
