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
//
//    public function update(int $id): void
//    {
//    }
//
//    public function delete(JobOffer $jobOffer)
//    {
//    }
//
//    public function list(): array
//    {
//    }
}