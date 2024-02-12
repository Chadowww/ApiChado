<?php

namespace App\Repository;

use App\Entity\Resume;
use App\Exceptions\DatabaseException;
use App\Services\ConnectionDbService;
use App\Services\DataBaseServices\TransactionDbService;
use PDO;

/**
 * Repository for Resume entity
 */
class ResumeRepository
{
    /**
     * @var array
     * list of values to be used in queries
     */
    const array VALUES = [
        'resumeId' => ':resumeId',
        'title' => ':title',
        'filename' => ':filename',
        'candidateId' => ':candidateId',
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
     * @param Resume $resume
     * @return bool
     * @throws DatabaseException
     */
    public function create(Resume $resume): bool
    {
        $this->transactionDbService->executeTransaction(function () use ($resume) {
            $query = 'INSERT INTO resume (title, filename, candidateId) VALUES (:title, :filename, :candidateId)';

            $statement = $this->connection->prepare($query);

            foreach (self::VALUES as $key => $value) {
               if ($key === 'resumeId' || $key === 'updatedAt') {
                   continue;
               }
                $method = "get" . ucfirst($key);
                $statement->bindValue($value, $resume->$method());
            }

            $statement->execute();
        });

        return true;
    }

    /**
     * @param int $resumeId
     * @return Resume|null
     * @throws DatabaseException
     */
    public function read(int $resumeId): ?Resume
    {
        $this->transactionDbService->executeTransaction(function () use ($resumeId, &$resume) {
            $query = 'SELECT * FROM resume WHERE resumeId = :resumeId';
            $statement = $this->connection->prepare($query);
            $statement->bindValue(':resumeId', $resumeId, PDO::PARAM_INT);
            $statement->execute();
            $resume = $statement->fetchObject(Resume::class);
        });
        return $resume;
    }

    /**
     * @param Resume $resume
     * @return bool
     * @throws DatabaseException
     */
    public function update(Resume $resume): bool
    {
        $this->transactionDbService->executeTransaction(function () use ($resume) {
            $query = '
            UPDATE resume
            SET title = :title, filename = :filename, candidateId = :candidateId
            WHERE resumeId = :resumeId';

            $statement = $this->connection->prepare($query);

            foreach (self::VALUES as $key => $value) {
                $method = "get" . ucfirst($key);
                $statement->bindValue($value, $resume->$method());
            }
            $statement->execute();
        });

        return true;
    }

    /**
     * @param int $resumeId
     * @return bool
     * @throws DatabaseException
     */
    public function delete(int $resumeId): bool
    {
        $this->transactionDbService->executeTransaction(function () use ($resumeId, &$statement) {
            $query = 'DELETE FROM resume WHERE resumeId = :resumeId';
            $statement = $this->connection->prepare($query);
            $statement->bindValue(':resumeId', $resumeId, PDO::PARAM_INT);
            $statement->execute();
        });

        return $statement->rowCount() > 0;
    }

    /**
     * @return array
     * @throws DatabaseException
     */
    public function list(): array
    {
        $this->transactionDbService->executeTransaction(function () use (&$resumes) {
            $query = 'SELECT * FROM resume';
            $statement = $this->connection->query($query);
            $resumes = $statement->fetchAll(PDO::FETCH_CLASS, Resume::class);
        });

        return $resumes;
    }

    /**
     * @param $candidateId
     * @return false|array
     * @throws DatabaseException
     */
    public function findByCandidate($candidateId): false|array
    {
        $this->transactionDbService->executeTransaction(function () use ($candidateId, &$resumes) {
            $query = 'SELECT * FROM resume WHERE candidateId = :candidateId';
            $statement = $this->connection->prepare($query);
            $statement->bindValue(':candidateId', $candidateId, PDO::PARAM_INT);
            $statement->execute();
            $resumes = $statement->fetchAll(PDO::FETCH_CLASS, Resume::class);
        });

        return $resumes;
    }

}