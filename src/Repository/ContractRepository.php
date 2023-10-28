<?php

namespace App\Repository;

use App\Entity\Contract;
use App\Services\ConnectionDbService;

class ContractRepository
{
    private ConnectionDbService $connectionDbService;

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
            $this->connectionDbService->getconnection()->rollBack();
            return false;
        }
    }

    public function read(): array
    {
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