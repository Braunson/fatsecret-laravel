<?php

namespace Braunson\FatSecret;

class FatSecretApi
{
    private $urlBuilder;
    private $curl;

    public function __construct(
        UrlBuilder $urlBuilder,
        Curl $curl
    ) {
        $this->urlBuilder = $urlBuilder;
        $this->curl = $curl;

        return $this;
    }

    /**
     * Executes an api method.
     *
     * @param string $name   The method name to be executed
     * @param array  $params The params to execute the method with
     * @param string $token  The token used for executing the method.
     * @param string $secret The secret used for executing the method.
     *
     * @return json
     */
    public function executeMethod(string $name, array $params, string $token = null, string $secret = null)
    {
        $urlBuilder = $this->urlBuilder->setMethod($name)
            ->setTimestamp()
            ->setNonce()
            ->setMethodParameters($params)
            ->sign($token, $secret);

        $result = json_decode(
            $this->curl->query(
                $urlBuilder->getBase(),
                http_build_query($urlBuilder->getParams())
            )
        );

        $this->errorCheck($result);

        return $result;
    }

    /**
     * Checking for errors on the response, if so we throw a custom exception.
     *
     * @param object $response
     */
    private function errorCheck($response)
    {
        if (isset($response->error)) {
            throw new FatSecretException(
                $response->error->message,
                (int) $response->error->code
            );
        }
    }
}
