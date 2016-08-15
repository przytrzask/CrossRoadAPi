<?php

namespace Sda\Crossroad\Light;


use Sda\Crossroad\Db\DbConnection;

/**
 * Class LightRepository
 * @package Sda\Trystar\Light
 */
class LightRepository
{
    /**
     * @var DbConnection
     */
    private $connection;

    /**
     * LightRepository constructor.
     * @param DbConnection $connection
     */
    public function __construct(DbConnection $connection)
    {

        $this->connection = $connection;
    }

    /**
     * @param int $id
     * @return Light
     * @throws LightNotFoundException
     */
    public function getLight($id)
    {
        $query = $this->getQueryBuilder();
        $sth = $query
            ->select('*')
            ->from('lights')
            ->where('id=?')
            ->setParameter(0, $id)
            ->execute();
        $data = $sth->fetch(\PDO::FETCH_ASSOC);

        if (false === $data) {
            throw new LightNotFoundException();
        }
        return LightFactory::makeFromArray($data);
    }

    /**
     * @return LightCollection
     */
    public function getAllLights()
    {
        $query = $this->getQueryBuilder();
        $sth = $query
            ->select('*')
            ->from('lights')
            ->execute();
        $data = $sth->fetchAll(\PDO::FETCH_ASSOC);


        $collection = new LightCollection();

        foreach ($data as $light) {
            $collection->add(LightFactory::makeFromArray($light));
        }

        return $collection;
    }

    public function save(Light $light)
    {
        $query = $this->getQueryBuilder();
        $query
            ->update('lights', 'l')
            ->set('l.state', '?')
            ->where('l.id=?')
            ->setParameter(0, $light->getState())
            ->setParameter(1, $light->getId())
            ->execute();
    }

    private function getQueryBuilder()
    {
        return $this->connection->getConnection()->createQueryBuilder();
    }

    /**
     * @param Light $light
     * @return bool
     */
    public function checkIfLightExist(Light $light)
    {
        $query = $this->getQueryBuilder();
        $sth = $query
            ->select('*')
            ->from('lights')
            ->where('id=?')
            ->setParameter(0, $light->getId())
            ->execute();
        $data = $sth->fetch(\PDO::FETCH_ASSOC);

        return false !== $data;

    }
}