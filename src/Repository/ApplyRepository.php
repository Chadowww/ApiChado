<?php

namespace App\Repository;

use App\Entity\Apply;
use App\Entity\Candidate;
use App\Services\ConnectionDbService;
use App\Services\DataBaseServices\BindValueService;
use PDO;
use PDOException;

class ApplyRepository
{
    private PDO $connection;
    private BindValueService $bindValueService;

    CONST array VALUES = [
        'status' => ':status',
        'message' => ':message',
        'candidateId' => ':candidate_id',
        'jobofferId' => ':joboffer_id',
    ];

    public function __construct(ConnectionDbService $connection, BindValueService $bindValueService)
    {
        $this->connection = $connection->connection();
        $this->bindValueService = $bindValueService;
    }

    public function create(Apply $apply): bool
    {
        $applyAttributes = [];
        $this->executeTransaction(function () use ($apply, &$applyAttributes){
            $query ='INSERT INTO APICHADO.apply (status, message, candidate_id, joboffer_id) VALUES (:status, :message, :candidate_id, :joboffer_id)';
            $statement = $this->connection->prepare($query);

            foreach (self::VALUES as $key => $value) {
                $applyAttributes[$value] = $apply->{"get" . ucfirst($key)}();
            }

            $this->bindValueService->bindValuesToStatement($statement, $applyAttributes);
            $statement->execute();
        });

        return true;
    }

    public function read(int $apply_id): Apply | bool
    {
        $query = 'SELECT * FROM APICHADO.apply WHERE apply_id = :apply_id';
        $statement = $this->connection->prepare($query);
        $statement->bindValue(':apply_id', $apply_id);
        $statement->execute();
        $apply = $statement->fetchObject(Apply::class);

        return $apply;
    }


    public function update(Apply $apply): bool
    {
        $candidateAttributes = [];
        $this->executeTransaction(function () use ($apply, &$candidateAttributes){
            $query = 'UPDATE APICHADO.apply SET status = :status, message = :message, candidate_id = :candidate_id, joboffer_id = :joboffer_id WHERE apply_id = :apply_id';
            $statement = $this->connection->prepare($query);

            foreach (self::VALUES as $key => $value) {
                $candidateAttributes[$value] = $candidate->{"get" . ucfirst($key)}();
            }

            $this->bindValueService->bindValuesToStatement($statement, $candidateAttributes);
            $statement->execute();
        });

        return true;
    }

    public function delete(int $candidate_id): bool
    {
        $this->executeTransaction(function () use ($candidate_id){
            $query = 'DELETE FROM APICHADO.candidate WHERE candidate_id = :candidate_id';
            $statement = $this->connection->prepare($query);
            $statement->bindValue(':candidate_id', $candidate_id);
            $statement->execute();
        });

        return true;
    }

    public function list(): array
    {
        $query = 'SELECT * FROM APICHADO.apply';
        $statement = $this->connection->query($query);
        $applys = $statement->fetchAll(\PDO::FETCH_CLASS, Apply::class);

        return $applys;
    }

    private function executeTransaction(callable $transaction): void
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