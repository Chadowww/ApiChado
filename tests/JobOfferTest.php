<?php

namespace App\Tests;

use App\Services\ConnectionDbService;
use App\Services\ConnectionDbTestService;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpClient\HttpClient;
use Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface;

class JobOfferTest extends TestCase
{
    private ConnectionDbService $dbConnection;
    private \Symfony\Contracts\HttpClient\HttpClientInterface $client;

    public function __construct()
    {
        parent::__construct();
        $this->dbConnection = new ConnectionDbService();
        $this->client = HttpClient::create();
    }
    /**
     * @before
     */
    public function setUpDatabase(): void
    {
        $this->dbConnection->getConnection()->exec('
            CREATE TABLE IF NOT EXISTS `job_offer` (
                `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
                `title` VARCHAR(255) NOT NULL,
                `description` TEXT NOT NULL,
                `city` VARCHAR(255) NOT NULL,
                `salary_min` INT NOT NULL,
                `salary_max` INT NOT NULL,
                PRIMARY KEY (`id`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
        ');
    }

    /**
     * @after
     */
    public function tearDownDatabase()
    {
        $this->dbConnection->getConnection()->exec('
            DROP TABLE IF EXISTS `job_offer`;
        ');
    }

    public function setUp(): void
    {
        parent::setUp();
    }

    /**
     * @throws TransportExceptionInterface
     */
    public function testJobOfferCreate(): void
    {
        $response = $this->client->request('POST', 'https://127.0.0.1:8000/job-offer/create', [
            'body' => [
                'title' => 'test21',
                'description' => 'test',
                'city' => 'test',
                'salaryMin' => 30000,
                'salaryMax' => 40000,
            ],
        ]);

        $this->assertEquals(201, $response->getStatusCode());
    }

    /**
     * @throws TransportExceptionInterface
     */
    public function testJobOfferRead(): void
    {
        $response = $this->client->request('GET', 'https://127.0.0.1:8000/job-offer/read/11');
        $this->assertEquals(200, $response->getStatusCode());
    }

    /**
     * @throws TransportExceptionInterface
     */
    public function testJobOfferUpdate(): void
    {
        $response = $this->client->request('PUT', 'https://127.0.0.1:8000/job-offer/update/11?title=NouveauTest&description=Nouvelle Description&city=Nouvelle ville Libourne&salaryMin=40000&salaryMax=45000');
        $this->assertEquals(201, $response->getStatusCode());
    }

    /**
     * @throws TransportExceptionInterface
     */
    public function testJobOfferDelete(): void
    {
        $response = $this->client->request('DELETE', 'https://127.0.0.1:8000/job-offer/delete/11');
        $this->assertEquals(200, $response->getStatusCode());
    }

    public function testJobOfferList()
    {
        $response = $this->client->request('GET', 'https://127.0.0.1:8000/job-offer/list');
        $this->assertEquals(200, $response->getStatusCode());
    }
}
