<?php

namespace App\Repository;

use App\Entity\Company;
use App\Services\ConnectionDbService;
use App\Services\DataBaseServices\BindValueService;
use PDO;
use PDOException;

class CompanyRepository
{
    private PDO $connection;
    private BindValueService $bindValueService;

    CONST array VALUES = [
        'name' => ':name',
        'phone' => ':phone',
        'address' => ':address',
        'city' => ':city',
        'country' => ':country',
        'siret' => ':siret',
        'slug' => ':slug',
        'userId' => ':userId',
    ];

    public function __construct(ConnectionDbService $connection, BindValueService $bindValueService)
    {
        $this->connection = $connection->connection();
        $this->bindValueService = $bindValueService;
    }

    public function create(Company $company): bool
    {
        $companyAttributes = [];

        $this->executeTransaction(function () use ($company, &$companyAttributes){
            $query = '
            INSERT INTO APICHADO.company
            (name, phone, address, city, country, siret, slug, userId) 
            VALUES 
            (:name, :phone, :address, :city, :country, :siret, :slug, :userId)';

            $statement = $this->connection->prepare($query);

            foreach (self::VALUES as $key => $value) {
                $companyAttributes[$value] = $company->{"get" . ucfirst($key)}();
            }
            $this->bindValueService->bindValuesToStatement($statement, $companyAttributes);
            $statement->execute();
        });

        return true;
    }

    public function read(int $companyId): Company | bool
    {
        $this->connection->beginTransaction();
        $query = '
                SELECT c.*, u.* 
                FROM APICHADO.company as c LEFT JOIN APICHADO.user as u ON c.userId = u.userId
                WHERE c.companyId = :companyId;
                ';
        $statement = $this->connection->prepare($query);
        $statement->bindValue(':companyId', $companyId);
        $statement->execute();
        $company = $statement->fetchObject(Company::class);
        $this->connection->commit();

        return $company;
    }

    public function update(Company $company): bool
    {
        $companyAttributes = [];

        $this->executeTransaction(function () use ($company, &$companyAttributes){
            $query = '
            UPDATE APICHADO.company
            SET 
                name = :name,
                phone = :phone,
                address = :address,
                city = :city,
                country = :country,
                siret = :siret,
                slug = :slug,
                userId = :userId
            WHERE companyId = :companyId';

            $statement = $this->connection->prepare($query);

            foreach (self::VALUES as $key => $value) {
                $companyAttributes[$value] = $company->{"get" . ucfirst($key)}();
            }
            $companyAttributes[':companyId'] = $company->getCompanyId();
            $this->bindValueService->bindValuesToStatement($statement, $companyAttributes);
            $statement->execute();
        });

        return true;
    }

    public function delete(int $companyId): bool
    {
        try {
            $this->connection->beginTransaction();
            $query = 'DELETE FROM APICHADO.company WHERE companyId = :companyId';
            $statement = $this->connection->prepare($query);
            $statement->bindValue(':companyId', $companyId);
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
        $query = 'SELECT c.*, u.* FROM APICHADO.company as c LEFT JOIN APICHADO.user as u ON c.userId = u.userId';
        $statement = $this->connection->query($query);
        $companies = $statement->fetchAll(PDO::FETCH_CLASS, Company::class);
        $this->connection->commit();

        return $companies;
    }

    public function topOffers(): array
    {
        $this->connection->beginTransaction();
        $query = '
           SELECT c.companyId, c.name, c.slug, c.logo, c.cover, COUNT(jo.companyId) as offers_count
            FROM APICHADO.company c
            JOIN APICHADO.joboffer jo ON jo.companyId = c.companyId
            GROUP BY c.companyId, c.name
            ORDER BY offers_count DESC
            LIMIT 6';
        $statement = $this->connection->query($query);
        $companies = $statement->fetchAll(PDO::FETCH_ASSOC);
        $this->connection->commit();

        return $companies;
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