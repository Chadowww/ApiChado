<?php

namespace App\Tests;

use App\Controller\JobOfferController;
use App\Repository\JobOfferRepository;
use App\Services\ErrorService;
use HttpException;
use PDOException;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpClient\HttpClient;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Contracts\HttpClient\Exception\{
    ClientExceptionInterface,
    RedirectionExceptionInterface,
    ServerExceptionInterface,
    TransportExceptionInterface
};
use Symfony\Contracts\HttpClient\HttpClientInterface;

class JobOfferTest extends TestCase
{
    private HttpClientInterface $client;
    private SerializerInterface $serializer;
    private ErrorService $errorService;

    public function __construct()
    {
        parent::__construct();
        $this->client = HttpClient::create();
        $this->serializer = $this->createMock(SerializerInterface::class);
        $this->errorService = new ErrorService($this->createMock(ValidatorInterface::class));

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
    public function testJobOfferCreateError400(): void
    {
        $response = $this->client->request('POST', 'https://127.0.0.1:8000/job-offer/create', [
            'body' => [
                'title' => 'test21',
                'description' => 'test',
                'city' => 'test',
                'salaryMin' => 'test',
                'salaryMax' => 40000,
            ],
        ]);
        $this->assertEquals(400, $response->getStatusCode());
    }

    public function testJobOfferCreateError500(): void
    {
        $jobOfferRepository = $this->createMock(JobOfferRepository::class);

        $jobOfferRepository->method('create')
            ->willThrowException(new PDOException('Erreur de connexion a la base de donnees', 500));

        $controller = new JobOfferController($jobOfferRepository, $this->serializer, $this->errorService);

        $request = new Request();
        $request->query->add([
            'title' => 'test21',
            'description' => 'test',
            'city' => 'test',
            'salaryMin' => 4000,
            'salaryMax' => 40000,
        ]);

        $response = $controller->create($request);

        $this->assertEquals(500, $response->getStatusCode());

        $this->assertEquals('"Erreur de connexion a la base de donnees"', $response->getContent());
    }

    /**
     * @throws TransportExceptionInterface
     */
    public function testJobOfferRead(): void
    {
        $response = $this->client->request('GET', 'https://127.0.0.1:8000/job-offer/read/7');
        $this->assertEquals(200, $response->getStatusCode());
    }

    /**
     * @throws TransportExceptionInterface
     */
    public function testJobOfferReadError404(): void
    {
        $falseId = 238;
        $response = $this->client->request('GET', 'https://127.0.0.1:8000/job-offer/read/' . $falseId);

        $this->assertEquals(404, $response->getStatusCode());
    }

    public function testJobOfferReadError500(): void
    {
        $jobOfferRepository = $this->createMock(JobOfferRepository::class);

        $jobOfferRepository->method('read')
            ->willThrowException(new PDOException('Erreur de connexion à la base de données', 500));

        $controller = new JobOfferController($jobOfferRepository, $this->serializer, $this->errorService);

        $response = $controller->read(1);

        $this->assertEquals(500, $response->getStatusCode());
        $this->assertJson($response->getContent());
    }

    /**
     * @throws TransportExceptionInterface
     */
    public function testJobOfferUpdate(): void
    {
        $response = $this->client->request('PUT', 'https://127.0.0.1:8000/job-offer/update/9?title=NouveauTest&description=Nouvelle Description&city=NouvellevilleLibourne&salaryMin=40000&salaryMax=45000');
        $this->assertEquals(201, $response->getStatusCode());
    }

    /**
     * @throws TransportExceptionInterface
     */
    public function testJobOfferUpdateError400(): void
    {
        $response = $this->client->request('PUT', 'https://127.0.0.1:8000/job-offer/update/11?title=NouveauTest&description=Nouvelle Description&city=Nouvelle ville Libourne&salaryMin=test&salaryMax=45000');
        $this->assertEquals(400, $response->getStatusCode());
    }

    public function testJobOfferUpdateError500(): void
    {
        $jobOfferRepository = $this->createMock(JobOfferRepository::class);

        $jobOfferRepository->method('update')
            ->willThrowException(new PDOException('Erreur de connexion à la base de données', 500));

        $controller = new JobOfferController($jobOfferRepository, $this->serializer, $this->errorService);

        $request = new Request();
        $request->query->add([
            'title' => 'test21',
            'description' => 'test',
            'city' => 'test',
            'salaryMin' => 40000,
            'salaryMax' => 45000,
        ]);

        $response = $controller->update(3, $request);

        $this->assertEquals(500, $response->getStatusCode());
        $this->assertJson($response->getContent());
    }

    /**
     * @throws TransportExceptionInterface
     */
    public function testJobOfferDelete(): void
    {
        $response = $this->client->request('DELETE', 'https://127.0.0.1:8000/job-offer/delete/8');
        $this->assertEquals(200, $response->getStatusCode());
    }

    /**
     * @throws TransportExceptionInterface
     */
    public function testJobOfferDeleteError404(): void
    {
        $falseId = 238;
        $response = $this->client->request('DELETE', 'https://127.0.0.1:8000/job-offer/delete/' . $falseId);
        $this->assertEquals(404, $response->getStatusCode());
    }

    public function testJobOfferDeleteError500(): void
    {
        $jobOfferRepository = $this->createMock(JobOfferRepository::class);

        $jobOfferRepository->method('delete')
            ->willThrowException(new PDOException('Erreur de connexion à la base de données', 500));

        $controller = new JobOfferController($jobOfferRepository, $this->serializer, $this->errorService);

        $this->expectException(PDOException::class);
        $this->expectExceptionCode(500);
        $this->expectExceptionMessage('Erreur de connexion à la base de données');

        $controller->delete(1);
    }

    /**
     * @throws TransportExceptionInterface
     * @throws ServerExceptionInterface
     * @throws RedirectionExceptionInterface
     * @throws ClientExceptionInterface
     * @throws \JsonException
     */
    public function testJobOfferList(): void
    {
        $response = $this->client->request('GET', 'https://127.0.0.1:8000/job-offer/list');

        $this->assertEquals(200, $response->getStatusCode());

        $content = $response->getContent();
        $this->assertJson($content);

        $data = json_decode($content, true, 512, JSON_THROW_ON_ERROR);
        $this->assertIsArray($data);

        foreach ($data as $offer) {
            $this->assertArrayHasKey('id', $offer);
            $this->assertArrayHasKey('title', $offer);
            $this->assertArrayHasKey('description', $offer);
            $this->assertArrayHasKey('city', $offer);
            $this->assertArrayHasKey('salaryMin', $offer);
            $this->assertArrayHasKey('salaryMax', $offer);
        }
    }

    public function testJobOfferListError500(): void
    {
        $jobOfferRepository = $this->createMock(JobOfferRepository::class);

        $jobOfferRepository->method('list')
            ->willThrowException(new PDOException('Erreur de connexion à la base de données', 500));

        $controller = new JobOfferController($jobOfferRepository, $this->serializer, $this->errorService);

        $response = $controller->list();

        $this->assertEquals(500, $response->getStatusCode());
    }


}
