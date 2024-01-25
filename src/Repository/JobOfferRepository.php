<?php

namespace App\Repository;

use App\Entity\JobOffer;
use App\Services\ConnectionDbService;
use PDO;

class JobOfferRepository
{
    private PDO $connection;

    public function __construct(ConnectionDbService $connection)
    {
        $this->connection = $connection->connection();
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

    public function read(int $joboffer_id): Array | bool
    {
        $this->connection->beginTransaction();
        $query = '
            SELECT joboffer.*, c.*, co.name, co.city, co.cover, co.description AS company_description, co.logo, ct.* FROM APICHADO.joboffer
            LEFT JOIN APICHADO.category AS c ON c.category_id = joboffer.category_id
            LEFT JOIN APICHADO.company AS co ON co.company_id = joboffer.company_id
            LEFT JOIN APICHADO.contract AS ct ON ct.contract_id = joboffer.contract_id
            WHERE joboffer.joboffer_id = :joboffer_id';
        $statement = $this->connection->prepare($query);
        $statement->bindValue(':joboffer_id', $joboffer_id);
        $statement->execute();
        $jobOffer = $statement->fetch(PDO::FETCH_ASSOC);
        $this->connection->commit();

        if($jobOffer === false){
            return false;
        }

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
            WHERE joboffer_id = :joboffer_id
        ';
            $statement = $this->connection->prepare($query);
            $statement->bindValue(':title', $jobOffer->getTitle());
            $statement->bindValue(':description', $jobOffer->getDescription());
            $statement->bindValue(':city', $jobOffer->getCity());
            $statement->bindValue(':salaryMin', $jobOffer->getSalaryMin());
            $statement->bindValue(':salaryMax', $jobOffer->getSalaryMax());
            $statement->bindValue(':joboffer_id', $jobOffer->getJoboffer_id());
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
            $query = 'DELETE FROM APICHADO.joboffer WHERE joboffer_id = :joboffer_id';
            $statement = $this->connection->prepare($query);
            $statement->bindValue(':joboffer_id', $jobOffer->getJoboffer_id());
            $statement->execute();
            $this->connection->commit();
            return true;
        } catch (\PDOException $e) {
            $this->connection->rollBack();
            throw $e;
        }
    }

    public function list(): array
    {
        try {
            $this->connection->beginTransaction();
            $query = 'SELECT * FROM APICHADO.joboffer';
            $statement = $this->connection->query($query);
            $jobOffers = $statement->fetchAll(PDO::FETCH_CLASS, JobOffer::class);
            $this->connection->commit();
            return $jobOffers;
        } catch (\PDOException $e) {
            $this->connection->rollBack();
            throw $e;
        }
    }
}