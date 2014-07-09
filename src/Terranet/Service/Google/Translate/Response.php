<?php
namespace Terranet\Service\Google\Translate;

class Response
{
    protected $_data = null;

    public function __construct($data)
    {
        $this->_data = json_decode($data);
    }

    public function isSuccess()
    {
        return (! empty($this->_data->data));
    }

    public function isFailure()
    {
        return ! $this->isSuccess();
    }

    public function getData()
    {
        return $this->_data->data;
    }
}