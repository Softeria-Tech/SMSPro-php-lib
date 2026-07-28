<?php

namespace SofteriaTech\SmsPro\Exceptions;

use Exception;

class SmsProException extends Exception
{
    /**
     * @var array
     */
    protected $responseData = [];

    /**
     * Constructor
     *
     * @param string $message
     * @param int $code
     * @param array $responseData
     */
    public function __construct(string $message, int $code = 0, array $responseData = [])
    {
        parent::__construct($message, $code);
        $this->responseData = $responseData;
    }

    /**
     * Get response data
     *
     * @return array
     */
    public function getResponseData(): array
    {
        return $this->responseData;
    }
}