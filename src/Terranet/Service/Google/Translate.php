<?php

namespace Terranet\Service\Google;

/**
 * https://developers.google.com/translate/?hl=ru-RU
 *
 * Class Translate
 */
class Translate
{
    const TRANSLATE_URI             = 'https://www.googleapis.com/language/translate/v2';
    const DETECT_URI                = 'https://www.googleapis.com/language/translate/v2/detect';

    protected $_accessToken = null;

    protected $_accessTokenAdapter = null;

    protected $httpClient   = null;

    public function __construct(Translate\AccessToken $accessTokenAdapter)
    {
        $this->_accessTokenAdapter = $accessTokenAdapter;

        if (null === $this->httpClient) {
            $this->httpClient = new \Zend_Http_Client();
            $this->httpClient->setConfig(array(
                'adapter'      => '\Zend_Http_Client_Adapter_Socket',
                'maxredirects' => 0,
                'timeout'      => 3,
                'ssltransport' => 'ssl'
            ));
        }
    }

    /**
     * HTTP client preparation procedures - should be called before every API
     * call.
     *
     * Will clean up the HTTP client parameters, set the request method to POST
     * and add the always-required authentication information
     *
     * @param  string $hitType The API method we are about to use
     * @return void
     */
    protected function prepare($method)
    {
        $requestMethod = \Zend_Http_Client::GET;
        switch ($method) {
            case 'translate':
                $this->httpClient->setUri(self::TRANSLATE_URI);
                break;

            case 'detect':
                $this->httpClient->setUri(self::DETECT_URI);
                break;

            default:
                throw new Translate\Exception('Unknown method');
        }

        // Reset parameters
        $this->httpClient->resetParameters();
        $this->httpClient->setParameterGet('key', $this->_accessTokenAdapter->getApiKey());
        $this->httpClient->setMethod($requestMethod);
    }

    /**
     * Preform the request and return a response object
     *
     * @throws Translate\Exception
     * @return Translate\Response
     */
    protected function process()
    {
        $httpResponse = $this->httpClient->request();

        if (! $httpResponse->isSuccessful()) {
            throw new Translate\Exception('HTTP response from Google: ' . $httpResponse->getStatus());
        }

        return new Translate\Response($httpResponse->getBody());
    }

    /**
     * Translates a text string from one language to another.
     *
     * @param $text
     * @param $from
     * @param $to
     * @return null
     */
    public function translate($text, $from, $to)
    {
        $this->prepare('translate');

        $this->httpClient->setParameterGet(array(
            'q'      => $text,
            'source' => $from,
            'target' => $to
        ));

        $response = $this->process();

        return ($response->getData());
    }

    /**
     * Detect text language.
     *
     * @param $text
     * @return mixed
     * @throws Translate\Exception
     */
    public function detect($text)
    {
        $this->prepare('detect');
        $this->httpClient->setParameterGet(array(
            'q'  => $text
        ));
        $response = $this->process();

        return ($response->getData());
    }
}