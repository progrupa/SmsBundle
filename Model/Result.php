<?php

namespace Progrupa\SmsBundle\Model;


class Result
{
    /** @var  bool */
    private $success;
    /** @var  string */
    private $message;

    public function __construct($success = true, $message = '')
    {
        $this->success = $success;
        $this->message = $message;
    }

    /**
     * @return boolean
     */
    public function isSuccess()
    {
        return $this->success;
    }

    /**
     * @param boolean $success
     */
    public function setSuccess($success)
    {
        $this->success = $success;
    }

    /**
     * @return string
     */
    public function getMessage()
    {
        return $this->message;
    }

    /**
     * @param string $message
     */
    public function setMessage($message)
    {
        $this->message = $message;
    }
}
