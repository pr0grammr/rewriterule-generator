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
}