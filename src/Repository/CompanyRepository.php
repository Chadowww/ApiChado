<?php

namespace App\Repository;

use App\Entity\Company;
use App\Services\ConnectionDbService;
use PDO;
use PDOException;

class CompanyRepository
{
    private PDO $connection;

    public function __construct(ConnectionDbService $connection)
    {
        $this->connection = $connection->connection();
    }

    public function create(Company $company): bool
    {
        $this->connection->beginTransaction();

        try {
            $query = '
                INSERT INTO APICHADO.company 
                    (`name`,
                     `phone`,
                     `address`,
                     `city`,
                     `country`,
                     `siret`,
                     `slug`,
                     `user_id`)
                VALUES 
                    (:name,
                     :phone,
                     :address,
                     :city,
                     :country,
                     :siret,
                     :slug,
                     :user_id)';
            $statement = $this->connection->prepare($query);
            $statement->bindValue(':name', $company->getName());
            $statement->bindValue(':phone', $company->getPhone());
            $statement->bindValue(':address', $company->getAddress());
            $statement->bindValue(':city', $company->getCity());
            $statement->bindValue(':country', $company->getCountry());
            $statement->bindValue(':siret', $company->getSiret());
            $statement->bindValue(':slug', $company->getSlug());
            $statement->bindValue(':user_id', $company->getUserId());
            $statement->execute();
            $this->connection->commit();
            return true;
        } catch (PDOException $e) {
            $this->connection->rollBack();
            throw $e;
        }
    }

    public function read(int $id): Company | bool
    {
        $this->connection->beginTransaction();
        $query = 'SELECT c.*, u.* FROM APICHADO.company as c LEFT JOIN APICHADO.user as u ON c.user_id = u.id WHERE c.id = :id';
        $statement = $this->connection->prepare($query);
        $statement->bindValue(':id', $id);
        $statement->execute();
        $company = $statement->fetchObject(Company::class);
        $this->connection->commit();

        return $company;
    }

    public function update(Company $company): bool
    {
        try {
            $this->connection->beginTransaction();
            $query = '
                UPDATE APICHADO.company 
                SET 
                    `name` = :name,
                    `logo` = :logo,
                    `description` = :description,
                    `address` = :address,
                    `city` = :city,
                    `country` = :country,
                    `slug` = :slug
                WHERE 
                    `id` = :id';
            $statement = $this->connection->prepare($query);
            $statement->bindValue(':id', $company->getId());
            $statement->bindValue(':name', $company->getName());
            $statement->bindValue(':logo', $company->getLogo());
            $statement->bindValue(':description', $company->getDescription());
            $statement->bindValue(':address', $company->getAddress());
            $statement->bindValue(':city', $company->getCity());
            $statement->bindValue(':country', $company->getCountry());
            $statement->bindValue(':slug', $company->getSlug());
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
            $query = 'DELETE FROM APICHADO.company WHERE id = :id';
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

    public function list(): array
    {
        $this->connection->beginTransaction();
        $query = 'SELECT * FROM APICHADO.company';
        $statement = $this->connection->prepare($query);
        $statement->execute();
        $companies = $statement->fetchAll(PDO::FETCH_CLASS, Company::class);
        $this->connection->commit();

        return $companies;
    }
}