<?php

namespace App\Repository;

use App\Entity\Apply;
use App\Exceptions\DatabaseException;
use App\Services\ConnectionDbService;
use App\Services\DataBaseServices\BindValueService;
use PDO;
use PDOException;

/**
 * Repository for Apply entity
 */
class ApplyRepository
{
    /**
     * @var PDO
     */
    private PDO $connection;
    /**
     * @var BindValueService
     */
    private BindValueService $bindValueService;

    /**
     * @var array
     * list of values to be used in queries
     */
    CONST array VALUES = [
        'applyId' => ':applyId',
        'status' => ':status',
        'message' => ':message',
        'candidateId' => ':candidateId',
        'resumeId' => ':resumeId',
        'jobofferId' => ':jobofferId',
        'updatedAt' => ':updatedAt',
    ];


    /**
     * @param ConnectionDbService $connection
     * @param BindValueService $bindValueService
     */
    public function __construct(ConnectionDbService $connection, BindValueService $bindValueService)
    {
        $this->connection = $connection->connection();
        $this->bindValueService = $bindValueService;
        $this->connection->setAttribute(PDO::ATTR_STRINGIFY_FETCHES, false);

    }

    /**
     * @throws DatabaseException
     */
    public function create(Apply $apply): bool
    {
        $applyAttributes = [];
        $this->executeTransaction(function () use ($apply, &$applyAttributes){
            $query = 'INSERT INTO APICHADO.apply (status, message, candidateId, resumeId, jobofferId) VALUES (:status, :message, :candidateId, :resumeId, :jobofferId)';
            $statement = $this->connection->prepare($query);

            foreach (self::VALUES as $key => $value) {
                if ($key !== 'applyId' && $key !== 'updatedAt') {
                    $applyAttributes[$value] = $apply->{"get" . ucfirst($key)}();
                }
            }

            $this->bindValueService->bindValuesToStatement($statement, $applyAttributes);
            $statement->execute();
        });

        return true;
    }

    /**
     * @throws DatabaseException
     */
    public function read(int $applyId): Apply | bool
    {
        try {
            $query = 'SELECT * FROM APICHADO.apply WHERE applyId = :applyId';
            $statement = $this->connection->prepare($query);
            $statement->bindValue(':applyId', $applyId);
            $statement->execute();
            $apply = $statement->fetchObject(Apply::class);

            return $apply;
        } catch (PDOException $e) {
            throw new DatabaseException($e->getMessage(), $e->getCode());
        }
    }

    /**
     * @throws DatabaseException
     */
    public function update(Apply $apply): bool
    {
        $applyAttributes = [];
        $this->executeTransaction(function () use ($apply, &$applyAttributes){
            $query = 'UPDATE APICHADO.apply SET status = :status, message = :message, candidateId = :candidateId, resumeId = :resumeId, jobofferId = :jobofferId, updatedAt = :updatedAt WHERE applyId = :applyId';
            $statement = $this->connection->prepare($query);

            foreach (self::VALUES as $key => $value) {
                $applyAttributes[$value] = $apply->{"get" . ucfirst($key)}();
            }

            $this->bindValueService->bindValuesToStatement($statement, $applyAttributes);
            $statement->execute();
        });

        return true;
    }

    /**
     * @param int $candidateId
     * @return bool
     * @throws DatabaseException
     */
    public function delete(int $applyId): bool
    {

        $this->executeTransaction(function () use ($applyId) {
            $query = 'DELETE FROM APICHADO.apply WHERE applyId = :applyId';
            $statement = $this->connection->prepare($query);
            $statement->bindValue(':applyId', $applyId);
            $statement->execute();
        });

        return $this->read($applyId) ? false : true;
    }

    /**
     * @throws DatabaseException
     */
    public function list(): array | bool
    {
        try {
            $query = 'SELECT * FROM APICHADO.apply';
            $statement = $this->connection->query($query);
            $applys = $statement->fetchAll(\PDO::FETCH_CLASS, Apply::class);

            return $applys;
        } catch (PDOException $e) {
            throw new DatabaseException($e->getMessage(), $e->getCode());
        }
    }

    /**
     * @throws DatabaseException
     */
    private function executeTransaction(callable $transaction): void
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