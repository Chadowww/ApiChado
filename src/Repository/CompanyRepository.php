<?php

namespace App\Repository;

use App\Entity\Company;
use App\Exceptions\DatabaseException;
use App\Services\DataBaseServices\ConnectionDbService;
use App\Services\DataBaseServices\TransactionDbService;
use PDO;

class CompanyRepository
{
    /**
     * @var array
     * list of values to be used in queries
     */
    const array VALUES = [
        'companyId' => ':companyId',
        'name' => ':name',
        'phone' => ':phone',
        'address' => ':address',
        'city' => ':city',
        'country' => ':country',
        'siret' => ':siret',
        'slug' => ':slug',
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
     * @param Company $company
     * @return bool
     * @throws DatabaseException
     */
    public function create(Company $company): bool
    {
        $this->transactionDbService->executeTransaction(function () use ($company){
            $query = '
            INSERT INTO company
            (name, phone, address, city, country, siret, slug, userId) 
            VALUES 
            (:name, :phone, :address, :city, :country, :siret, :slug, :userId)';

            $statement = $this->connection->prepare($query);

            foreach (self::VALUES as $key => $value) {
                if ($key === 'companyId' || $key === 'updatedAt') {
                    continue;
                }
                $method = "get" . ucfirst($key);
                $statement->bindValue($value, $company->$method());
            }
            
            $statement->execute();
        });

        return true;
    }

    /**
     * @param int $companyId
     * @return Company|bool
     * @throws DatabaseException
     */
    public function read(int $companyId): Company | bool
    {
        $this->transactionDbService->executeTransaction(function () use ($companyId, &$company) {
            $query = 'SELECT * FROM company WHERE companyId = :companyId';
            $statement = $this->connection->prepare($query);
            $statement->bindValue(':companyId', $companyId);
            $statement->execute();
            $company = $statement->fetchObject(Company::class);
        });

        return $company;
    }

    /**
     * @param Company $company
     * @return bool
     * @throws DatabaseException
     */
    public function update(Company $company): bool
    {
        $this->transactionDbService->executeTransaction(function () use ($company){
            $query = '
            UPDATE company
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
                $method = "get" . ucfirst($key);
                $statement->bindValue($value, $company->$method());
            }

            $statement->execute();
        });

        return true;
    }

    /**
     * @param int $companyId
     * @return bool
     * @throws DatabaseException
     */
    public function delete(int $companyId): bool
    {
        $this->transactionDbService->executeTransaction(function () use ($companyId, &$statement){
            $query = 'DELETE FROM company WHERE companyId = :companyId';
            $statement = $this->connection->prepare($query);
            $statement->bindValue(':companyId', $companyId);
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
        $this->transactionDbService->executeTransaction(function () use (&$companies){
            $query = 'SELECT * FROM company';
            $statement = $this->connection->query($query);
            $companies = $statement->fetchAll(PDO::FETCH_ASSOC);
        });

        return $companies;
    }

    /**
     * @return array
     * @throws DatabaseException
     */
    public function topOffers(): array
    {
        $this->transactionDbService->executeTransaction(function () use (&$companies) {
            $query = '
            SELECT c.companyId, c.name, c.slug, c.logo, c.cover, COUNT(jo.companyId) as offers_count
            FROM company c
            JOIN joboffer jo ON jo.companyId = c.companyId
            GROUP BY c.companyId, c.name
            ORDER BY offers_count DESC
            LIMIT 6';
            $statement = $this->connection->query($query);
            $companies = $statement->fetchAll(PDO::FETCH_ASSOC);
        });

        return $companies;
    }
}