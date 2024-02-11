<?php

namespace App\Repository;

use App\Entity\Resume;
use App\Exceptions\DatabaseException;
use App\Services\ConnectionDbService;
use App\Services\DataBaseServices\BindValueService;
use App\Services\DataBaseServices\TransactionDbService;
use PDO;
use PDOException;

class ResumeRepository
{
    private PDO $connection;

    const array VALUES = [
        'title' => ':title',
        'filename' => ':filename',
        'candidateId' => ':candidateId',
    ];
    private TransactionDbService $transactionDbService;

    public function __construct(ConnectionDbService $connection, TransactionDbService$transactionDbService)
    {
        $this->connection = $connection->connection();
        $this->transactionDbService = $transactionDbService;
    }

    /**
     * @throws DatabaseException
     */
    public function create(Resume $resume): bool
    {
        $this->transactionDbService->executeTransaction(function () use ($resume) {
            $query = 'INSERT INTO resume (title, filename, candidateId) VALUES (:title, :filename, :candidateId)';

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
            $statement->bindValue(':resumeId', $resume->getResumeId(), PDO::PARAM_INT);

            $statement->execute();
        });

        return true;
    }

    /**
     * @throws DatabaseException
     */
    public function delete(int $id): bool
    {
        $this->transactionDbService->executeTransaction(function () use ($id, &$statement) {
            $query = 'DELETE FROM resume WHERE resumeId = :id';
            $statement = $this->connection->prepare($query);
            $statement->bindValue(':id', $id, PDO::PARAM_INT);
            $statement->execute();
        });

        return $statement->rowCount() > 0;
    }

    /**
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