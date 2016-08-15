<?php

namespace Sda\Crossroad\Controller;

use Sda\Crossroad\Config\Config;
use Sda\Crossroad\Db\DbConnection;
use Sda\Crossroad\Light\LightCollection;
use Sda\Crossroad\Light\LightFactory;
use Sda\Crossroad\Light\LightFactoryException;
use Sda\Crossroad\Light\LightNotFoundException;
use Sda\Crossroad\Light\LightRepository;
use Sda\Crossroad\Request\Request;
use Sda\Crossroad\Response\Response;
use Sda\Crossroad\Routing\Routing;

/**
 * Class ApiController
 * @package Sda\Trystar\Controller
 */
class ApiController
{
    /**
     * @var Request
     */
    private $request;
    /**
     * @var Response
     */
    private $response;
    /**
     * @var LightRepository
     */
    private $lightRepository;

    /**
     * ApiController constructor.
     * @param Request $request
     * @param Response $response
     * @param DbConnection $connection
     */
    public function __construct(
        Request $request,
        Response $response,
        DbConnection $connection
    )
    {
        $this->request = $request;
        $this->response = $response;

        $this->lightRepository = new LightRepository($connection);
    }

    public function run()
    {
        $key=$this->request->getKeyFromHeader();
        if($key!==Config::KEY_FROM_HEADER){
            $this->response->send401();

        }

        $action = $this->request->getParamFormGet('action');

        switch ($action) {
            case  Routing::LIGHT:

                $id = $this->request->getParamFormGet('id', 0);
                if (0 === $id) {
                    $this->response->send404();
                }

                if (Request::HTTP_METHOD_GET === $this->request->getHttpMethod()) {
                    $this->getLight($id);
                } else if (Request::HTTP_METHOD_POST === $this->request->getHttpMethod()) {

                    $this->setLight($id);
                } else {
                    $this->response->send('', Response::STATUS_400_BAD_REQUEST);
                }

                break;
            case Routing::ALL_LIGHT:
                if (Request::HTTP_METHOD_GET === $this->request->getHttpMethod()) {
                    $this->getLights();
                } else if (Request::HTTP_METHOD_POST === $this->request->getHttpMethod()) {

                    $this->setLights();
                } else {
                    $this->response->send('', Response::STATUS_400_BAD_REQUEST);
                }
                $this->getLights();
                break;
            default:
                $this->response->send404();
                break;
        }

    }

    private function getLights()
    {
        try {
            $collection = $this->lightRepository->getAllLights();
            $this->response->send($collection);
        } catch (LightNotFoundException $e) {
            $this->response->send404();
        }
    }

    private function getLight($id)
    {
        try {
            $light = $this->lightRepository->getLight($id);
            $this->response->send($light);
        } catch (LightNotFoundException $e) {
            $this->response->send404();
        }
    }

    private function setLight($id)
    {
        $inputData = $this->request->getJsonRequestBody();
        try {
            $light = LightFactory::makeWithId($id, $inputData);
            if (false === $light->validate()) {
                $this->response->send('Incorrect state', Response::STATUS_400_BAD_REQUEST);
            }
            $this->lightRepository->save($light);
        } catch (LightFactoryException $e) {

            $this->response->send('Undefined state param in request', Response::STATUS_400_BAD_REQUEST);
        }
    }

    private function setLights()
    {
        $collection = new LightCollection();
        $inputData = $this->request->getJsonRequestBody();
        foreach ($inputData as $dt) {
            $light = LightFactory::makeFromArray($dt);
            $collection->add($light);
            if (false === $light->validate() || false === $this->lightRepository->checkIfLightExist($light)) {
                $this->response->send('Incorrect data', Response::STATUS_400_BAD_REQUEST);
            }
        }
        foreach ($collection->getIterator() as $dt) {
            $this->lightRepository->save($dt);
        }

    }

}