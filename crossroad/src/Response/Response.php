<?php

namespace Sda\Crossroad\Response;

/**
 * Class Response
 * @package Sda\Trystar\Response
 */
class Response
{
    const STATUS_OK='200';
    const STATUS_404_NOT_FOUND='404';
    const STATUS_400_BAD_REQUEST='400';
    const STATUS_401_UNAUTHORIZED='401';

    public function send404()
    {
        $this->send('',self::STATUS_404_NOT_FOUND);
    }
    public function send401()
    {
        $this->send('',self::STATUS_401_UNAUTHORIZED);
    }

    /**
     * @param string $data
     * @param string $status
     */
    public function send($data='', $status=self::STATUS_OK)
    {
        http_response_code($status);
        echo json_encode($data);
        $this->stopApi();
    }

    public function stopApi()
    {
        die();
    }
}