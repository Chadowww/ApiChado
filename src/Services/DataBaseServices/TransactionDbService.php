<?php

namespace App\Services\DataBaseServices;

use App\Exceptions\DatabaseException;
use PDO;
use PDOException;

class TransactionDbService
{
    private PDO $connection;

    public function __construct(ConnectionDbService $connection)
    {
        $this->connection = $connection->connection();
    }

    /**
     * @throws DatabaseException
     */
    public function executeTransaction(callable $transaction): Void
    {
        try {
            $this->connection->beginTransaction();

            $transaction();

            $this->connection->commit();
        } catch (PDOException $e) {
            $this->connection->rollBack();
            throw new DatabaseException($e->getMessage(), 500);
        }
    }
}