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
        'candidate_id' => ':candidate_id',
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
            (title, filename, candidate_id) 
            VALUES 
            (:title, :filename, :candidate_id)';

            $statement = $this->connection->prepare($query);

            foreach (self::VALUES as $key => $value) {
                $resumeAttributes[$value] = $resume->{"get" . ucfirst($key)}();
            }
            $this->bindValueService->bindValuesToStatement($statement, $resumeAttributes);

            $statement->execute();
        });
    }

    public function read(int $id): Resume | bool
    {
        $this->connection->beginTransaction();
        $query = 'SELECT * FROM APICHADO.resume WHERE id = :id';
        $statement = $this->connection->prepare($query);
        $statement->bindValue(':id', $id, PDO::PARAM_INT);
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
            SET title = :title, filename = :filename, candidate_id = :candidate_id
            WHERE id = :id';

            $statement = $this->connection->prepare($query);

            foreach (self::VALUES as $key => $value) {
                $resumeAttributes[$value] = $resume->{"get" . ucfirst($key)}();
            }
            $resumeAttributes[':id'] = $resume->getId();
            $this->bindValueService->bindValuesToStatement($statement, $resumeAttributes);
            $statement->execute();
        });

        return true;
    }
    public function delete(int $id): bool
    {
        $this->executeTransaction(function () use ($id) {
            $query = 'DELETE FROM APICHADO.resume WHERE id = :id';
            $statement = $this->connection->prepare($query);
            $statement->bindValue(':id', $id, PDO::PARAM_INT);
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
}