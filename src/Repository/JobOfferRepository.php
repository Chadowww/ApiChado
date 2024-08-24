<?php

namespace App\Repository;

use App\Entity\JobOffer;
use App\Services\DataBaseServices\ConnectionDbService;
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

    public function read(int $jobofferId): JobOffer | bool
    {
        $this->connection->beginTransaction();
        $query = '
            SELECT * FROM APICHADO.joboffer WHERE jobofferId = :jobofferId';
        $statement = $this->connection->prepare($query);
        $statement->bindValue(':jobofferId', $jobofferId);
        $statement->execute();
        $jobOffer = $statement->fetch(PDO::FETCH_CLASS, JobOffer::class);
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
            WHERE jobofferId = :jobofferId
        ';
            $statement = $this->connection->prepare($query);
            $statement->bindValue(':title', $jobOffer->getTitle());
            $statement->bindValue(':description', $jobOffer->getDescription());
            $statement->bindValue(':city', $jobOffer->getCity());
            $statement->bindValue(':salaryMin', $jobOffer->getSalaryMin());
            $statement->bindValue(':salaryMax', $jobOffer->getSalaryMax());
            $statement->bindValue(':jobofferId', $jobOffer->getJobofferId());
            $statement->execute();
            $this->connection->commit();
            return true;
        } catch (\PDOException $e) {
            $this->connection->rollBack();
            throw $e;
        }
    }

    public function delete($id): bool
    {
        try {
            $this->connection->beginTransaction();
            $query = 'DELETE FROM APICHADO.joboffer WHERE jobofferId = :jobofferId';
            $statement = $this->connection->prepare($query);
            $statement->bindValue(':jobofferId', $id);
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

    public function getJobOfferWithAllData(int $jobofferId): Array | bool
    {
        $this->connection->beginTransaction();
        $query = '
            SELECT joboffer.*, c.*, co.name, co.city, co.cover, co.description AS company_description, co.logo, ct.* FROM APICHADO.joboffer
            LEFT JOIN APICHADO.category AS c ON c.categoryId = joboffer.categoryId
            LEFT JOIN APICHADO.company AS co ON co.companyId = joboffer.companyId
            LEFT JOIN APICHADO.contract AS ct ON ct.contractId = joboffer.contractId
            WHERE joboffer.jobofferId = :jobofferId';
        $statement = $this->connection->prepare($query);
        $statement->bindValue(':jobofferId', $jobofferId);
        $statement->execute();
        $jobOffer = $statement->fetch(PDO::FETCH_ASSOC);
        $this->connection->commit();

        if($jobOffer === false){
            return false;
        }

        return $jobOffer;
    }}