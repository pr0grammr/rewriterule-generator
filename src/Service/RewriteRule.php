<?php

namespace App\Service;

class RewriteRule
{
    /**
     * @var string
     */
    private $_from;

    /**
     * @var string
     */
    private $_to;

    /**
     * @var int
     */
    private $_statusCode;

    public function __construct(string $from, string $to)
    {
        $this->_from = $from;
        $this->_to = $to;
    }

    /**
     * @return string
     */
    public function getFrom() : string
    {
        return $this->_from;
    }

    /**
     * @return string
     */
    public function getTo() : string
    {
        return $this->_to;
    }

    /**
     * @return int
     */
    public function getStatusCode() : int
    {
        return $this->_statusCode;
    }

    /**
     * @param int $statusCode
     */
    public function setStatusCode(int $statusCode) : void
    {
        $this->_statusCode = $statusCode;
    }
}