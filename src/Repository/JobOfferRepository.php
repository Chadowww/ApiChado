<?php

namespace App\Repository;

use App\Entity\JobOffer;
use App\Services\ConnectionDbService;

class JobOfferRepository
{
    public function __construct(ConnectionDbService $connection)
    {
        $this->connection = $connection->getConnection();
    }
    public function create(JobOffer $jobOffer): bool
    {
        $this->connection->beginTransaction();

        try {
            $query = '
                INSERT INTO APICHADO.joboffer 
                    (`title`,
                     `description`,
                     `city`,
                     `salaryMin`,
                     `salaryMax`)
                VALUES 
                    (:title,
                     :description,
                     :city,
                     :salaryMin,
                     :salaryMax)';
            $statement = $this->connection->prepare($query);
            $statement->bindValue(':title', $jobOffer->getTitle());
            $statement->bindValue(':description', $jobOffer->getDescription());
            $statement->bindValue(':city', $jobOffer->getCity());
            $statement->bindValue(':salaryMin', $jobOffer->getSalaryMin());
            $statement->bindValue(':salaryMax', $jobOffer->getSalaryMax());
            $statement->execute();
            $this->connection->commit();
            return true;
        } catch (\PDOException $e) {
            $this->connection->rollBack();
            throw $e;
        }

    }

    public function read(int $id): JobOffer
    {
        $this->connection->beginTransaction();
        $query = 'SELECT * FROM APICHADO.joboffer WHERE id = :id';
        $statement = $this->connection->prepare($query);
        $statement->bindValue(':id', $id);
        $statement->execute();
        $jobOffer = $statement->fetchObject(JobOffer::class);
        $this->connection->commit();
        return $jobOffer;
    }

    public function update(JobOffer $jobOffer): bool
    {
        try {
            $this->connection->beginTransaction();
            $query = '
            UPDATE APICHADO.joboffer 
            SET 
                `title` = :title,
                `description` = :description,
                `city` = :city,
                `salaryMin` = :salaryMin,
                `salaryMax` = :salaryMax
            WHERE id = :id
        ';
            $statement = $this->connection->prepare($query);
            $statement->bindValue(':title', $jobOffer->getTitle());
            $statement->bindValue(':description', $jobOffer->getDescription());
            $statement->bindValue(':city', $jobOffer->getCity());
            $statement->bindValue(':salaryMin', $jobOffer->getSalaryMin());
            $statement->bindValue(':salaryMax', $jobOffer->getSalaryMax());
            $statement->bindValue(':id', $jobOffer->getId());
            $statement->execute();
            $this->connection->commit();
            return true;
        } catch (\PDOException $e) {
            $this->connection->rollBack();
            throw $e;
        }
    }

    public function delete(JobOffer $jobOffer): bool
    {
        try {
            $this->connection->beginTransaction();
            $query = 'DELETE FROM APICHADO.joboffer WHERE id = :id';
            $statement = $this->connection->prepare($query);
            $statement->bindValue(':id', $jobOffer->getId());
            $statement->execute();
            $this->connection->commit();
            return true;
        } catch (\PDOException $e) {
            $this->connection->rollBack();
            throw $e;
        }
    }

//    public function list(): array
//    {
//    }
}