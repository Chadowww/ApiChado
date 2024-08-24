<?php

namespace App\Repository;

use App\Entity\Candidate;
use App\Exceptions\DatabaseException;
use App\Services\DataBaseServices\ConnectionDbService;
use App\Services\DataBaseServices\TransactionDbService;
use PDO;

/**
 * Repository for Candidate entity
 */
class CandidateRepository
{
    /**
     * @var array
     * list of values to be used in queries
     */
    const array VALUES = [
        'candidateId' => ':candidateId',
        'firstname' => ':firstname',
        'lastname' => ':lastname',
        'phone' => ':phone',
        'address' => ':address',
        'city' => ':city',
        'country' => ':country',
        'avatar' => ':avatar',
        'slug' => ':slug',
        'coverLetter' => ':coverLetter',
        'userId' => ':userId',
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
     * @param Candidate $candidate
     * @return bool
     * @throws DatabaseException
     */
    public function create(Candidate $candidate): bool
    {
        $this->transactionDbService->executeTransaction(function () use ($candidate) {
            $query = '
            INSERT INTO candidate
            (firstname, lastname, phone, address, city, country, avatar, slug, coverLetter, userId) 
            VALUES 
            (:firstname, :lastname, :phone, :address, :city, :country, :avatar, :slug, :coverLetter, :userId)';

            $statement = $this->connection->prepare($query);

            foreach (self::VALUES as $key => $value) {
                if ($key === 'resumeId' || $key === 'updatedAt') {
                    continue;
                }
                $method = "get" . ucfirst($key);
                $statement->bindValue($value, $candidate->$method());
            }

            $statement->execute();
        });

        return true;
    }

    /**
     * @param int $candidateId
     * @return Candidate|bool
     * @throws DatabaseException
     */
    public function read(int $candidateId) : Candidate | bool
    {
        $this->transactionDbService->executeTransaction(function () use ($candidateId, &$candidate) {
            $query = 'SELECT c.*, u.* FROM candidate c LEFT JOIN user u ON c.userId = u.userId WHERE c.candidateId = :candidateId';
            $statement = $this->connection->prepare($query);
            $statement->bindValue(':candidateId', $candidateId);
            $statement->execute();
            $candidate = $statement->fetchObject(Candidate::class);
        });

        return $candidate;
    }

    /**
     * @param Candidate $candidate
     * @return bool
     * @throws DatabaseException
     */
    public function update(Candidate $candidate): bool
    {
        $this->transactionDbService->executeTransaction(function () use ($candidate){
            $query = '
            UPDATE candidate
            SET firstname = :firstname, lastname = :lastname, phone = :phone, address = :address, city = :city, country = :country, avatar = :avatar, slug = :slug, userId = :userId
            WHERE userId = :userId';

            $statement = $this->connection->prepare($query);

            foreach (self::VALUES as $key => $value) {
                $method = "get" . ucfirst($key);
                $statement->bindValue($value, $candidate->$method());
            }
            $statement->execute();
        });

        return true;
    }

    /**
     * @param int $candidateId
     * @return bool
     * @throws DatabaseException
     */
    public function delete(int $candidateId): bool
    {
        $this->transactionDbService->executeTransaction(function () use ($candidateId, &$statement) {
            $query = 'DELETE FROM candidate WHERE candidateId = :candidateId';
            $statement = $this->connection->prepare($query);
            $statement->bindValue(':candidateId', $candidateId, PDO::PARAM_INT);
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
        $this->transactionDbService->executeTransaction(function () use (&$candidates) {
            $query = 'SELECT * FROM candidate';
            $statement = $this->connection->query($query);
            $candidates = $statement->fetchAll(PDO::FETCH_CLASS, Candidate::class);
        });
        return $candidates;
    }

    /**
     * @param $candidateId
     * @return Candidate|bool
     * @throws DatabaseException
     */
    public function getByUserId($candidateId):Candidate | bool
    {
        $this->transactionDbService->executeTransaction(function () use ($candidateId, &$candidate) {
            $query = 'SELECT c.*, u.* FROM candidate c LEFT JOIN user u ON c.userId = u.userId WHERE c.userId = :candidateId';
            $statement = $this->connection->prepare($query);
            $statement->bindValue(':candidateId', $candidateId);
            $statement->execute();
            $candidate = $statement->fetchObject(Candidate::class);
        });

        return $candidate;
    }
}