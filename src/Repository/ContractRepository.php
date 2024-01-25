<?php

namespace App\Repository;

use App\Entity\Contract;
use App\Services\ConnectionDbService;
use PDOException;

class ContractRepository
{
    private \PDO $connection;

    public function __construct(ConnectionDbService $connectionDbService)
    {
        $this->connection = $connectionDbService->connection();
    }

    public function create(Contract $contract): bool
    {
        try {
            $this->connection->beginTransaction();
            $query = 'INSERT INTO APICHADO.contract (type) VALUES (:type)';
            $statement = $this->connection->prepare($query);
            $statement->bindValue(':type', $contract->getType());
            $statement->execute();
            $this->connection->commit();
            return true;
        } catch (PDOException $e) {
            $this->connection->rollBack();
            throw $e;
        }
    }

    public function read(int $contract_id): Contract | bool
    {
        $this->connection->beginTransaction();
        $query = 'SELECT * FROM APICHADO.contract WHERE contract_id = :contract_id';
        $statement = $this->connection->prepare($query);
        $statement->bindValue(':contract_id', $contract_id);
        $statement->execute();
        $contract = $statement->fetchObject(Contract::class);
        $this->connection->commit();

        if ($contract === false) {
           return false;
        }

        return $contract;
    }

    public function update(Contract $contract): bool
    {
        try {
            $this->connection->beginTransaction();
            $query = 'UPDATE APICHADO.contract SET type = :type WHERE contract_id = :contract_id';
            $statement = $this->connection->prepare($query);
            $statement->bindValue(':type', $contract->getType());
            $statement->bindValue(':contract_id', $contract->getContract_id());
            $statement->execute();
            $this->connection->commit();
            return true;
        } catch (\Exception $e) {
            $this->connection->rollBack();
            return false;
        }
    }

    public function delete(Contract $contract):bool
    {
        try {
            $this->connection->beginTransaction();
            $query = 'DELETE FROM APICHADO.contract WHERE contract_id = :contract_id';
            $statement = $this->connection->prepare($query);
            $statement->bindValue(':contract_id', $contract->getContract_id());
            $statement->execute();
            $this->connection->commit();
            return true;
        } catch (\Exception $e) {
            $this->connection->rollBack();
            return false;
        }
    }

    public function list(): array
    {
        $query = 'SELECT * FROM contract';
        return $this->connection->query($query)->fetchAll(\PDO::FETCH_CLASS, Contract::class);
    }
}