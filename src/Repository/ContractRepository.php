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

    public function read(int $id): array
    {
        $query = 'SELECT * FROM contract WHERE id = :id';
        $statement = $this->connection->prepare($query);
        $statement->bindValue(':id', $id);
        $statement->execute();
        return $statement->fetch();
    }

    public function update():bool
    {
    }

    public function delete():bool
    {
    }

    public function list(): array
    {
    }
}