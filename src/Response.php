<?php
namespace LucidTunes\Jaak;

class Response
{
    /** @var array */
    protected $data;

    /** @var array */
    protected $errors;

    /**
     * Response constructor.
     * @param mixed $response
     */
    public function __construct(\stdClass $response)
    {
        $this->data = $response->data ?? [];
        $this->errors = $response->errors ?? [];
    }

    public function data()
    {
        return $this->data;
    }

    public function errors()
    {
        return $this->errors;
    }

    public function hasErrors()
    {
        return count($this->errors) > 0;
    }
}