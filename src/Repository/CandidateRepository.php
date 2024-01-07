<?php

namespace App\Repository;

use App\Entity\Candidate;
use App\Services\ConnectionDbService;
use App\Services\DataBaseServices\BindValueService;
use PDO;
use PDOException;

class CandidateRepository
{
    private PDO $connection;
    private BindValueService $bindValueService;

    CONST array VALUES = [
        'firstname' => ':firstname',
        'lastname' => ':lastname',
        'phone' => ':phone',
        'address' => ':address',
        'city' => ':city',
        'country' => ':country',
        'avatar' => ':avatar',
        'slug' => ':slug',
        'coverLetter' => ':coverLetter',
        'user_id' => ':user_id',
    ];

    public function __construct(ConnectionDbService $connection, BindValueService $bindValueService)
    {
        $this->connection = $connection->connection();
        $this->bindValueService = $bindValueService;
    }

    public function create(Candidate $candidate): bool
    {
        $candidateAttributes = [];

        $this->executeTransaction(function () use ($candidate, &$candidateAttributes) {
            $query = '
            INSERT INTO APICHADO.candidate
            (firstname, lastname, phone, address, city, country, avatar, slug, coverLetter, user_id) 
            VALUES 
            (:firstname, :lastname, :phone, :address, :city, :country, :avatar, :slug, :coverLetter, :user_id)';

            $statement = $this->connection->prepare($query);

            foreach (self::VALUES as $key => $value) {
                $candidateAttributes[$value] = $candidate->{"get" . ucfirst($key)}();
            }
            $this->bindValueService->bindValuesToStatement($statement, $candidateAttributes);
            $statement->execute();
        });

        return true;
    }

    public function read(int $id) : Candidate | bool
    {
        $this->connection->beginTransaction();
        $query = 'SELECT c.*, u.* FROM APICHADO.candidate c LEFT JOIN APICHADO.user u ON c.user_id = u.id WHERE c.id = :id';
        $statement = $this->connection->prepare($query);
        $statement->bindValue(':id', $id);
        $statement->execute();
        $candidate = $statement->fetchObject(Candidate::class);
        $this->connection->commit();

        return $candidate;
    }

    public function update(Candidate $candidate): bool
    {
        $candidateAttributes = [];

        $this->executeTransaction(function () use ($candidate, &$candidateAttributes){
            $query = '
            UPDATE APICHADO.candidate
            SET firstname = :firstname, lastname = :lastname, phone = :phone, address = :address, city = :city, country = :country, avatar = :avatar, slug = :slug, user_id = :user_id
            WHERE user_id = :user_id';

            $statement = $this->connection->prepare($query);

            foreach (self::VALUES as $key => $value) {
                $candidateAttributes[$value] = $candidate->{"get" . ucfirst($key)}();
            }
            $this->bindValueService->bindValuesToStatement($statement, $candidateAttributes);

            $statement->execute();
        });

        return true;
    }

    public function delete(int $id): bool
    {
        $this->executeTransaction(function () use ($id) {
            $query = 'DELETE FROM APICHADO.candidate WHERE id = :id';
            $statement = $this->connection->prepare($query);
            $statement->bindValue(':id', $id);
            $statement->execute();
        });
        return true;
    }

    public function list(): array
    {
        $this->executeTransaction(function () use (&$candidates) {
            $query = 'SELECT c.*, u.* FROM APICHADO.candidate c LEFT JOIN APICHADO.user u ON c.user_id = u.id';
            $statement = $this->connection->query($query);
            $candidates = $statement->fetchAll(PDO::FETCH_CLASS, Candidate::class);
        });
        return $candidates;
    }

    public function getByUserId($id):Candidate | bool
    {
        $this->connection->beginTransaction();
        $query = 'SELECT c.*, u.* FROM APICHADO.candidate c LEFT JOIN APICHADO.user u ON c.user_id = u.id WHERE c.user_id = :id';
        $statement = $this->connection->prepare($query);
        $statement->bindValue(':id', $id);
        $statement->execute();
        $candidate = $statement->fetchObject(Candidate::class);
        $this->connection->commit();

        return $candidate;
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