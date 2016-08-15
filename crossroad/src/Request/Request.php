<?php

namespace Sda\Crossroad\Request;

class Request
{
    const  HTTP_METHOD_GET = 'GET';
    const HTTP_METHOD_POST = 'POST';

    /**
     * @param $param
     * @param string $default
     * @return string
     */
    public function getParamFormGet($param, $default = '')
    {
        if (true === array_key_exists($param, $_GET)) {
            return $_GET[$param];
        }

        return $default;
    }

    /**
     *
     * @param String $param
     * @param string $default
     * @return string
     */
    public function getParamsFormPost($param, $default = '')
    {
        if (true === array_key_exists($param, $_POST)) {
            return $_POST[$param];
        }

        return $default;
    }

    /**
     * @return array
     */
    public function getJsonRequestBody()
    {
        $result = json_decode(file_get_contents('php://input'), true);
        return (false === $result || null === $result) ? [] : $result;
    }

    /**
     * @return string
     */
    public function getHttpMethod()
    {
        return $_SERVER['REQUEST_METHOD'];
    }

    public function getKeyFromHeader()
    {
        $headers = getallheaders();
        return $headers['X-CROSSROAD-AUTH'];
    }
}
