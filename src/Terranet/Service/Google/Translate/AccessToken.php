<?php
namespace Terranet\Service\Google\Translate;

class AccessToken
{
    /**
     * Required. The API Key.
     *
     * @var string
     */
    protected $_apiKey = '';

    public function __construct($apiKey)
    {
        $this->_apiKey     = $apiKey;
    }

    public function getApiKey()
    {
        return $this->_apiKey;
    }
}