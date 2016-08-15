<?php

namespace Sda\Mastercross\Cycler;

use Doctrine\DBAL\Query\QueryBuilder;
use Sda\Mastercross\Db\DbConnection;

/**
 * Class Cycler
 * @package Sda\Mastercross\Cycler
 */
class Cycler
{
    const API_URL = "http://crossroad.local/api/lights";
    /**
     * @var DbConnection
     */
    private $dbConnection;
    /**
     * @var string
     */
    private $apiKey;
    /**
     * @var int
     */
    private $crossroadId;

    /**
     * Cycler constructor.
     * @param DbConnection $dbConnection
     */
    public function __construct(
        DbConnection $dbConnection
    )
    {
        $this->dbConnection = $dbConnection;
    }

    /**
     *
     */
    public function run()
    {
        $crossroads = $this->getCrossroads();

        foreach ($crossroads as $crossroad) {

            $this->apiKey = $crossroad['auth_key'];
            $this->crossroadId = $crossroad['id'];

            $cycle = $this->getActiveCycleForCrossroad();

            $currentState = $this->getCrossroadStates();

            if (false === $currentState){
                $phase = $this->getFirstPhaseFromCycle($cycle['id']);
            }else{
                $phase = $this->getPhaseById($currentState['phases_id']);

                $interval = (int)$phase['interval'] / 1000;
                $actualStartTime = strtotime($currentState['created_at']);
                $now = time();


                if (($now-$actualStartTime) > $interval){
                    try{
                        $phase = $this->getNextPhase($cycle['id'], $phase['priority']);
                    }catch (PhaseNotFoundException $e){
                        $phase = $this->getFirstPhaseFromCycle($cycle['id']);
                    }
                }else{
                    continue;
                }
            }

            $this->activatePhase($phase);
        }
    }

    /**
     * @return mixed
     */
    public function getCrossroadStates()
    {
        $query = $this->getQueryBuilder();

        $sth = $query
            ->select('*')
            ->from('current_crossroads_state')
            ->where('crossroads_id = :crossroadId')
            ->setParameter('crossroadId', $this->crossroadId)
            ->execute();

        $data = $sth->fetch(\PDO::FETCH_ASSOC);

        return $data;
    }

    /**
     * @return array
     */
    public function getCrossroads(){
        $query = $this->getQueryBuilder();
        $sth = $query
            ->select('*')
            ->from('crossroads')
            ->execute();

        $data = $sth->fetchAll(\PDO::FETCH_ASSOC);

        return $data;
    }

    /**
     * @param $crossroadId
     * @return array
     */
    private function getActiveCycleForCrossroad()
    {
        $query = $this->getQueryBuilder();
        $sth = $query
            ->select('*')
            ->from('cycles', 'c')
            ->where('c.crossroads_id = :crossroadId')
            ->setParameter('crossroadId', $this->crossroadId)
            ->andWhere("c.active = 'yes'")
            ->execute();

        $data = $sth->fetch(\PDO::FETCH_ASSOC);

        return $data;
    }

    /**
     * @return QueryBuilder
     */
    private function getQueryBuilder()
    {
        return $this->dbConnection->getConnection()->createQueryBuilder();
    }

    /**
     * @param $cycleId
     * @return array
     */
    private function getFirstPhaseFromCycle($cycleId)
    {
        $query = $this->getQueryBuilder();
        $sth = $query
            ->select('*')
            ->from('phases')
            ->where('cycles_id = :cycleId')
            ->setParameter('cycleId', $cycleId)
            ->andWhere('priority = 0')
            ->execute();

        $data = $sth->fetch(\PDO::FETCH_ASSOC);

        return $data;
    }

    /**
     * @param int $phaseId
     */
    private function getPhaseById($phaseId)
    {
        $query = $this->getQueryBuilder();

        $sth = $query
            ->select('*')
            ->from('phases')
            ->where('id = :phaseId')
            ->setParameter('phaseId', $phaseId)
            ->execute();

        $data = $sth->fetch(\PDO::FETCH_ASSOC);

        return $data;

    }

    /**
     * @param int $cycleId
     * @param int $priority
     * @return array
     * @throws PhaseNotFoundException
     */
    private function getNextPhase($cycleId, $priority)
    {
        $query = $this->getQueryBuilder();

        $sth = $query
            ->select('*')
            ->from('phases')
            ->where('cycles_id = :cycleId')
            ->setParameter('cycleId', $cycleId)
            ->andWhere('priority = :prio')
            ->setParameter('prio', ($priority + 1))
            ->execute();

        $data = $sth->fetch(\PDO::FETCH_ASSOC);

        if (false === $data){
            throw new PhaseNotFoundException();
        }

        return $data;
    }

    /**
     * @param array $phase
     */
    private function activatePhase(array $phase)
    {
        $lights = $this->getPhaseLights($phase['id']);

        try {
            $this->sendApiRequest(json_encode($lights));
            $this->updateCrurrentCrossroadState($phase['id']);
        } catch (ApiUpdateException $e) {
            $this->logError($e);
        }
    }

    /**
     * @param $phaseId
     */
    private function getPhaseLights($phaseId)
    {
        $query = $this->getQueryBuilder();

        $sth = $query
            ->select('cl.external_id AS id, lp.state')
            ->from('crossroad_lights', 'cl')
            ->join('cl', 'crossroads', 'c', 'c.id = cl.crossroads_id')
            ->join('c', 'cycles', 'cc', 'c.id = cc.crossroads_id')
            ->join('cc', 'phases', 'p', 'cc.id = p.cycles_id')
            ->join('cl', 'light_phases', 'lp', 'cl.id = lp.crossroad_lights_id')
            ->where('lp.phases_id = :phaseId')
            ->setParameter('phaseId', $phaseId)
            ->groupBy('cl.id')
            ->execute();

        $data = $sth->fetchAll(\PDO::FETCH_ASSOC);

        return $data;
    }

    /**
     * @param $jsonData
     * @throws ApiUpdateException
     */
    private function sendApiRequest($jsonData)
    {

        $curl = curl_init();

        curl_setopt_array($curl, array(
            CURLOPT_URL => self::API_URL,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => "",
            CURLOPT_MAXREDIRS => 1,
            CURLOPT_TIMEOUT => 10,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => "POST",
            CURLOPT_POSTFIELDS => $jsonData,
            CURLOPT_HTTPHEADER => array(
                "X-CROSSROAD-AUTH: trzasq",
                "content-type: application/json"
            ),
        ));

        $response = curl_exec($curl);
        $err = curl_error($curl);

        curl_close($curl);

        if ($err) {
            throw new ApiUpdateException();
        }
    }

    /**
     * @param \Exception $e
     */
    private function logError($e)
    {
        //TODO - dorobic logowanie
        echo $e->getMessage();
    }

    private function updateCrurrentCrossroadState($phaseId)
    {
        $sql = '
            INSERT INTO `current_crossroads_state`
            ( `crossroads_id`, `phases_id`, `created_at`)
            VALUES 
            (
                :crossroadsid,
                :phasesid,
                CURRENT_TIMESTAMP 
            )
            ON DUPLICATE KEY UPDATE
              `crossroads_id` = VALUES(`crossroads_id`),
              `phases_id` = VALUES(`phases_id`),
              `created_at` = CURRENT_TIMESTAMP
        ';

        $params = [
            'crossroadsid' => $this->crossroadId,
            'phasesid' => $phaseId
        ];

        $this->dbConnection->getConnection()->executeUpdate($sql, $params);

    }
}