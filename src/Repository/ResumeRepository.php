<?php

namespace App\Repository;

use App\Entity\Resume;
use App\Services\ConnectionDbService;
use App\Services\DataBaseServices\BindValueService;
use PDO;
use PDOException;

class ResumeRepository
{
    private PDO $connection;
    private BindValueService $bindValueService;

    CONST array VALUES = [
        'title' => ':title',
        'filename' => ':filename',
        'candidateId' => ':candidateId',
    ];

    public function __construct(ConnectionDbService $connection, BindValueService $bindValueService)
    {
        $this->connection = $connection->connection();
        $this->bindValueService = $bindValueService;
    }

    public function create(Resume $resume): void
    {
        $resumeAttributes = [];

        $this->executeTransaction(function () use ($resume, &$resumeAttributes) {
            $query = '
            INSERT INTO APICHADO.resume
            (title, filename, candidateId) 
            VALUES 
            (:title, :filename, :candidateId)';

            $statement = $this->connection->prepare($query);

            foreach (self::VALUES as $key => $value) {
                $resumeAttributes[$value] = $resume->{"get" . ucfirst($key)}();
            }
            $this->bindValueService->bindValuesToStatement($statement, $resumeAttributes);

            $statement->execute();
        });
    }

    public function read(int $resumeId): Resume | bool
    {
        $this->connection->beginTransaction();
        $query = 'SELECT * FROM APICHADO.resume WHERE resumeId = :resumeId';
        $statement = $this->connection->prepare($query);
        $statement->bindValue(':resumeId', $resumeId, PDO::PARAM_INT);
        $statement->execute();
        $resume = $statement->fetchObject(Resume::class);
        $this->connection->commit();
        return $resume;
    }

    public function update(Resume $resume): bool
    {
        $resumeAttributes = [];

        $this->executeTransaction(function () use ($resume, &$resumeAttributes) {
            $query = '
            UPDATE APICHADO.resume
            SET title = :title, filename = :filename, candidateId = :candidateId
            WHERE resumeId = :resumeId';

            $statement = $this->connection->prepare($query);

            foreach (self::VALUES as $key => $value) {
                $resumeAttributes[$value] = $resume->{"get" . ucfirst($key)}();
            }
            $resumeAttributes[':resumeId'] = $resume->getResumeId();
            $this->bindValueService->bindValuesToStatement($statement, $resumeAttributes);
            $statement->execute();
        });

        return true;
    }
    public function delete(string $filename): bool
    {
        $this->executeTransaction(function () use ($filename) {
            $query = 'DELETE FROM APICHADO.resume WHERE filename = :filename';
            $statement = $this->connection->prepare($query);
            $statement->bindValue(':filename', $filename, PDO::PARAM_STR);
            $statement->execute();
        });
        return true;
    }
    public function list(): array
    {
        $this->connection->beginTransaction();
        $query = 'SELECT * FROM APICHADO.resume';
        $statement = $this->connection->query($query);
        $resumes = $statement->fetchAll(PDO::FETCH_CLASS, Resume::class);
        $this->connection->commit();
        return $resumes;
    }

    private function executeTransaction(callable $transaction): Void
    {
        try {
            $this->connection->beginTransaction();

            $transaction();

            $this->connection->commit();
        } catch (PDOException $e) {
            $this->connection->rollBack();
            throw $e;
        }
    }

    public function findByCandidate($resumeId)
    {
        $this->connection->beginTransaction();
        $query = 'SELECT * FROM APICHADO.resume WHERE candidateId = :candidateId';
        $statement = $this->connection->prepare($query);
        $statement->bindValue(':candidateId', $resumeId, PDO::PARAM_INT);
        $statement->execute();
        $resumes = $statement->fetchAll(PDO::FETCH_CLASS, Resume::class);
        $this->connection->commit();
        return $resumes;
    }
}