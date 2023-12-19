<?php

namespace App\Repository;

use App\Entity\Candidate;
use App\Services\ConnectionDbService;
use PDO;
use PDOException;

class CandidateRepository
{
    private PDO $connection;

    public function __construct(ConnectionDbService $connection)
    {
        $this->connection = $connection->connection();
    }

    public function create(Candidate $candidate): bool
    {
        $this->connection->beginTransaction();

        try {
            $query = '
            INSERT INTO APICHADO.candidate
            (firstname, lastname, phone, address, city, country, avatar, slug, coverLetter, user_id) 
            VALUES 
            (:firstname, :lastname, :phone, :address, :city, :country, :avatar, :slug, :coverLetter, :user_id)';
            $statement = $this->connection->prepare($query);
            $statement->bindValue(':firstname', $candidate->getFirstname());
            $statement->bindValue(':lastname', $candidate->getLastname());
            $statement->bindValue(':phone', $candidate->getPhone());
            $statement->bindValue(':address', $candidate->getAddress());
            $statement->bindValue(':city', $candidate->getCity());
            $statement->bindValue(':country', $candidate->getCountry());
            $statement->bindValue(':avatar', $candidate->getAvatar());
            $statement->bindValue(':slug', $candidate->getSlug());
            $statement->bindValue(':coverLetter', $candidate->getCoverLetter());
            $statement->bindValue(':user_id', $candidate->getId());
            $statement->execute();
            $this->connection->commit();
            return true;
        } catch (PDOException $e) {
            $this->connection->rollBack();
            throw $e;
        }
    }

    public function read(int $id) : Candidate | bool
    {
        $this->connection->beginTransaction();
        $query = 'SELECT * FROM APICHADO.candidate WHERE id = :id';
        $statement = $this->connection->prepare($query);
        $statement->bindValue(':id', $id);
        $statement->execute();
        $candidate = $statement->fetchObject(Candidate::class);
        $this->connection->commit();

        return $candidate;
    }

    public function update(Candidate $candidate): bool
    {
        try {
            $this->connection->beginTransaction();
            $query = '
            UPDATE APICHADO.candidate
            SET firstname = :firstname, lastname = :lastname, phone = :phone, address = :address, city = :city, country = :country, avatar = :avatar, slug = :slug, coverLetter = :coverLetter
            WHERE id = :id';
            $statement = $this->connection->prepare($query);
            $statement->bindValue(':firstname', $candidate->getFirstname());
            $statement->bindValue(':lastname', $candidate->getLastname());
            $statement->bindValue(':phone', $candidate->getPhone());
            $statement->bindValue(':address', $candidate->getAddress());
            $statement->bindValue(':city', $candidate->getCity());
            $statement->bindValue(':country', $candidate->getCountry());
            $statement->bindValue(':avatar', $candidate->getAvatar());
            $statement->bindValue(':slug', $candidate->getSlug());
            $statement->bindValue(':coverLetter', $candidate->getCoverLetter());
            $statement->bindValue(':user_id', $candidate->getId());
            $statement->execute();
            $this->connection->commit();
            return true;
        } catch (PDOException $e) {
            $this->connection->rollBack();
            throw $e;
        }
    }

    public function delete(int $id): bool
    {
        try {
            $this->connection->beginTransaction();
            $query = 'DELETE FROM APICHADO.candidate WHERE id = :id';
            $statement = $this->connection->prepare($query);
            $statement->bindValue(':id', $id);
            $statement->execute();
            $this->connection->commit();
            return true;
        } catch (PDOException $e) {
            $this->connection->rollBack();
            throw $e;
        }
    }

    public function list(){
        try {
            $this->connection->beginTransaction();
            $query = 'SELECT * FROM APICHADO.candidate';
            $statement = $this->connection->query($query);
            $candidates = $statement->fetchAll(PDO::FETCH_CLASS, Candidate::class);
            $this->connection->commit();

            return $candidates;
        } catch (PDOException $e) {
            $this->connection->rollBack();
            throw $e;
        }
    }
}