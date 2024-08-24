<?php

namespace App\Repository;

use App\Entity\Apply;
use App\Exceptions\DatabaseException;
use App\Services\DataBaseServices\ConnectionDbService;
use App\Services\DataBaseServices\TransactionDbService;
use PDO;

/**
 * Repository for Apply entity
 */
class ApplyRepository
{
    /**
     * @var array
     * list of values to be used in queries
     */
    const array VALUES = [
        'applyId' => ':applyId',
        'status' => ':status',
        'message' => ':message',
        'candidateId' => ':candidateId',
        'resumeId' => ':resumeId',
        'jobofferId' => ':jobofferId',
        'updatedAt' => ':updatedAt',
    ];
    /**
     * @var PDO
     */
    private PDO $connection;
    /**
     * @var TransactionDbService
     */
    private TransactionDbService $transactionDbService;

    /**
     * @param ConnectionDbService $connection
     * @param TransactionDbService $transactionDbService
     */
    public function __construct(ConnectionDbService $connection, TransactionDbService $transactionDbService)
    {
        $this->connection = $connection->connection();
        $this->transactionDbService = $transactionDbService;
    }

    /**
     * @param Apply $apply
     * @return bool
     * @throws DatabaseException
     */
    public function create(Apply $apply): bool
    {
        $this->transactionDbService->executeTransaction(function () use ($apply){
            $query = 'INSERT INTO apply (status, message, candidateId, resumeId, jobofferId) VALUES (:status, :message, :candidateId, :resumeId, :jobofferId)';
            $statement = $this->connection->prepare($query);

            foreach (self::VALUES as $key => $value) {
                if ($key === 'applyId' || $key === 'updatedAt') {
                    continue;
                }
                $method = "get" . ucfirst($key);
                $statement->bindValue($value, $apply->$method());
            }

            $statement->execute();
        });

        return true;
    }

    /**
     * @param int $applyId
     * @return Apply|bool
     * @throws DatabaseException
     */
    public function read(int $applyId): Apply | bool
    {
        $this->transactionDbService->executeTransaction(function () use ($applyId, &$apply){
            $query = 'SELECT * FROM apply WHERE applyId = :applyId';
            $statement = $this->connection->prepare($query);
            $statement->bindValue(':applyId', $applyId);
            $statement->execute();
            $apply = $statement->fetchObject(Apply::class);
        });
        return $apply;
    }

    /**
     * @param Apply $apply
     * @return bool
     * @throws DatabaseException
     */
    public function update(Apply $apply): bool
    {
        $this->transactionDbService->executeTransaction(function () use ($apply){
            $query = 'UPDATE apply SET status = :status, message = :message, candidateId = :candidateId, resumeId = :resumeId, jobofferId = :jobofferId, updatedAt = :updatedAt WHERE applyId = :applyId';
            $statement = $this->connection->prepare($query);

            foreach (self::VALUES as $key => $value) {
                $method = "get" . ucfirst($key);
                $statement->bindValue($value, $apply->$method());
            }
            $statement->execute();
        });

        return true;
    }

    /**
     * @param int $applyId
     * @return bool
     * @throws DatabaseException
     */
    public function delete(int $applyId): bool
    {

        $this->transactionDbService->executeTransaction(function () use ($applyId, &$statement) {
            $query = 'DELETE FROM apply WHERE applyId = :applyId';
            $statement = $this->connection->prepare($query);
            $statement->bindValue(':applyId', $applyId);
            $statement->execute();
        });

        return $statement->rowCount() > 0;
    }

    /**
     * @return array|bool
     * @throws DatabaseException
     */
    public function list(): array | bool
    {
       $this->transactionDbService->executeTransaction(function () use (&$applies){
           $query = 'SELECT * FROM apply';
           $statement = $this->connection->query($query);
           $applies = $statement->fetchAll(PDO::FETCH_CLASS, Apply::class);
       });

        return $applies;
    }
}