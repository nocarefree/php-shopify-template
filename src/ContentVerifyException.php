<?php

namespace ShopifyTemplate;

class ContentVerifyException extends \Exception
{
    protected $messages = [];

    public function __construct($message = '', $code = 0, $previous = null)
    {
        parent::__construct($message, $code, $previous);
        $this->add($message);
    }

    public function add($message)
    {
        $this->messages[] = $message;
    }

    public function errors()
    {
        return $this->messages;
    }
}
