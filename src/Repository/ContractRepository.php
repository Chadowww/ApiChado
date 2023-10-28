<?php

namespace App\Repository;

use App\Entity\Contract;
use App\Services\ConnectionDbService;

class ContractRepository
{
    private \PDO $connection;

    public function __construct(ConnectionDbService $connectionDbService)
    {
        $this->connection = $connectionDbService->getconnection();
    }
    public function create(Contract $contract): bool
    {
        try {
            $this->connection->beginTransaction();
            $query = 'INSERT INTO contract (type) VALUES (:type)';
            $statement = $this->connection->prepare($query);
            $statement->bindValue(':type', $contract->getType());
            $statement->execute();
            $this->connection->commit();
            return true;
        } catch (\Exception $e) {
            $this->connection->rollBack();
            return false;
        }
    }

    public function read(int $id): Contract
    {
        $query = 'SELECT * FROM contract WHERE id = :id';
        $statement = $this->connection->prepare($query);
        $statement->bindValue(':id', $id);
        $statement->execute();
        return $statement->fetchObject(Contract::class);
    }

    public function update(Contract $contract):bool
    {
        try {
            $this->connection->beginTransaction();
            $query = 'UPDATE contract SET type = :type WHERE id = :id';
            $statement = $this->connection->prepare($query);
            $statement->bindValue(':type', $contract->getType());
            $statement->bindValue(':id', $contract->getId());
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
            $query = 'DELETE FROM contract WHERE id = :id';
            $statement = $this->connection->prepare($query);
            $statement->bindValue(':id', $contract->getId());
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
    }
}