<?php

namespace App\Tests;

use App\Controller\JobOfferController;
use App\Entity\JobOffer;
use App\Exceptions\DatabaseException;
use App\Exceptions\InvalidRequestException;
use App\Exceptions\ResourceNotFoundException;
use App\Repository\JobOfferRepository;
use App\Services\EntityServices\JobOfferService;
use App\Services\ErrorService;
use JsonException;
use PDOException;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;


class JobOfferTest extends TestCase
{
    private SerializerInterface $serializer;
    private ErrorService $errorService;
    private JobOfferRepository $mockRepository;
    private JobOfferController $mockController;
    private JobOfferService $jobOfferService;

    public function __construct()
    {
        parent::__construct();
        $this->serializer = $this->createMock(SerializerInterface::class);
        $this->errorService = $this->createMock(ErrorService::class);
        $this->mockRepository = $this->createMock(JobOfferRepository::class);
        $this->jobOfferService = $this->createMock(JobOfferService::class);
        $this->mockController = new JobOfferController(
            $this->mockRepository,
            $this->serializer,
            $this->errorService,
            $this->jobOfferService
        );
    }

    /**
     * @throws DatabaseException
     * @throws InvalidRequestException
     * @throws JsonException
     */
    public function testJobOfferCreate(): void
    {
       $data = [
           'title' => 'test21',
           'description' => 'test',
           'city' => 'test',
           'salaryMin' => 40000,
           'salaryMax' => 45000,
       ];
       $request = new Request([],[], [], [], [], [], json_encode($data, JSON_THROW_ON_ERROR));
         $request->setMethod('POST');
         $request->headers->set('Content-Type', 'application/json');
         $response = $this->mockController->create($request);
         $this->assertEquals(201, $response->getStatusCode());
    }

    /**
     * @throws DatabaseException
     * @throws JsonException
     */
    public function testJobOfferCreateError400(): void
    {
        $data = [
            'title' => '',
            'description' => 'test',
            'city' => 'test',
            'salaryMin' => 'test',
            'salaryMax' => 45000,
        ];
        $request = new Request([],[], [], [], [], [], json_encode($data, JSON_THROW_ON_ERROR));
        $request->setMethod('POST');
        $request->headers->set('Content-Type', 'application/json');

        $this->expectException(InvalidRequestException::class);
        $this->errorService->expects($this->atLeastOnce())
            ->method('getErrorsJobOfferRequest')
            ->willThrowException(new InvalidRequestException('message d\'erreur', 400));

        $this->expectException(InvalidRequestException::class);
        $this->expectExceptionCode(400);
        $this->mockController->create($request);

    }

    /**
     * @throws InvalidRequestException
     * @throws JsonException
     */
    public function testJobOfferCreateError500(): void
    {

        $this->mockRepository->method('create')
            ->willThrowException(new DatabaseException('Erreur de connexion a la base de donnees', 500));

        $request = new Request();
        $request->setMethod('POST');
        $request->request->replace([
            'title' => 'test21',
            'description' => 'test',
            'city' => 'test',
            'salaryMin' => 40000,
            'salaryMax' => 45000,
        ]);
        $this->expectException(DatabaseException::class);
        $this->expectExceptionCode(500);
        $this->expectExceptionMessage('Erreur de connexion a la base de donnees');

        $this->mockController->create($request);
    }

    /**
     * @throws ResourceNotFoundException
     * @throws DatabaseException
     * @throws JsonException
     */
    public function testJobOfferRead(): void
    {
        $mockRepository = $this->createMock(JobOfferRepository::class);
        $mockRepository->method('read')->willReturn(true);

        $mockController = new JobOfferController(
            $mockRepository,
            $this->serializer,
            $this->errorService,
            $this->jobOfferService
        );
        $response = $mockController->read(2);

        $this->assertEquals(200, $response->getStatusCode());
    }

    /**
     * @throws DatabaseException
     * @throws JsonException
     */
    public function testJobOfferReadError404(): void
    {
        $mockRepository = $this->createMock(JobOfferRepository::class);
        $mockRepository->method('read')->willReturn(false);

        $mockController = new JobOfferController(
            $mockRepository,
            $this->serializer,
            $this->errorService,
            $this->jobOfferService
        );

        $this->expectException(ResourceNotFoundException::class);

        $response = $mockController->read(2);

        $this->assertEquals(404, $response->getStatusCode());
    }

    /**
     * @throws ResourceNotFoundException
     * @throws JsonException
     */
    public function testJobOfferReadError500(): void
    {
        $jobOfferRepository = $this->createMock(JobOfferRepository::class);

        $jobOfferRepository->method('read')
            ->willThrowException(new PDOException('Erreur de connexion a la base de donnees', 500));

        $controller = new JobOfferController(
            $jobOfferRepository,
            $this->serializer,
            $this->errorService,
            $this->jobOfferService
        );

        $this->expectException(DatabaseException::class);
        $this->expectExceptionCode(500);
        $this->expectExceptionMessage('Erreur de connexion a la base de donnees');
        $controller->read(1);
    }

    /**
     * @throws DatabaseException
     * @throws InvalidRequestException
     * @throws ResourceNotFoundException
     * @throws JsonException
     */
    public function testJobOfferUpdate(): void
    {
        $data =[
            'title' => 'test21',
            'description' => 'test',
            'city' => 'test',
            'salaryMin' => 40000,
            'salaryMax' => 45000,
        ];
        $jobOffer = new JobOffer($data);

        $request = new Request([],[], [], [], [], [], json_encode($data, JSON_THROW_ON_ERROR));
        $request->setMethod('PUT');
        $request->headers->set('Content-Type', 'application/json');
        $this->mockRepository->expects($this->atLeastOnce())->method('read')->willReturn($jobOffer);
        $this->mockRepository->expects($this->atLeastOnce())->method('update')->willReturn(true);
        $this->mockController->update(2, $request);
    }

    /**
     * @throws DatabaseException
     * @throws ResourceNotFoundException
     * @throws JsonException
     */
    public function testJobOfferUpdateError400(): void
    {
        $request = new Request();
        $request->setMethod('PUT');
        $request->query->add([
            'title' => 'test21',
            'description' => 'test',
            'city' => 'test',
            'salaryMin' => 'test',
            'salaryMax' => 45000,
        ]);

        $this->expectException(InvalidRequestException::class);
        $this->errorService->expects($this->atLeastOnce())
            ->method('getErrorsJobOfferRequest')
            ->willThrowException(new InvalidRequestException('message d\'erreur', 400));

        $this->expectException(InvalidRequestException::class);
        $this->expectExceptionCode(400);

        $this->mockController->update(2, $request);

    }

    /**
     * @throws ResourceNotFoundException
     * @throws InvalidRequestException
     * @throws JsonException
     */
    public function testJobOfferUpdateError500(): void
    {
        $this->mockRepository->method('read')->willReturn(new JobOffer());

        $this->mockRepository->method('update')
            ->willThrowException(new DatabaseException('Erreur de connexion à la base de données', 500));

        $controller = new JobOfferController(
            $this->mockRepository,
            $this->serializer,
            $this->errorService,
            $this->jobOfferService
        );

        $request = new Request();
        $request->setMethod('PUT');
        $request->query->add([
            'title' => 'test21',
            'description' => 'test',
            'city' => 'test',
            'salaryMin' => 40000,
            'salaryMax' => 45000,
        ]);
        $this->expectException(DatabaseException::class);
        $response = $controller->update(3, $request);

        $this->assertEquals(500, $response->getStatusCode());
        $this->assertJson($response->getContent());
    }

    /**
     * @throws DatabaseException
     * @throws ResourceNotFoundException
     * @throws JsonException
     */
    public function testJobOfferDelete(): void
    {
       $this->mockRepository->method('read')->willReturn(new JobOffer());
       $this->mockRepository->method('delete')->willReturn(true);
       $this->mockController->delete(2);

       $this->assertEquals(200, $this->mockController->delete(2)->getStatusCode());
    }

    /**
     * @throws DatabaseException
     * @throws JsonException
     */
    public function testJobOfferDeleteError404(): void
    {
        $this->mockRepository->method('read')->willReturn(false);

        $this->expectException(ResourceNotFoundException::class);

        $this->mockController->delete(2);

        $this->assertEquals(404, $this->mockController->delete(2)->getStatusCode());
    }

    /**
     * @throws ResourceNotFoundException
     * @throws JsonException
     */
    public function testJobOfferDeleteError500(): void
    {
        $this->mockRepository->method('read')->willReturn(new JobOffer());

        $this->mockRepository->method('delete')
            ->willThrowException(new DatabaseException('Erreur de connexion à la base de données', 500));


        $this->expectException(DatabaseException::class);
        $this->expectExceptionCode(500);
        $this->expectExceptionMessage('Erreur de connexion à la base de données');

        $this->mockController->delete(1);
    }

    /**
     * @throws DatabaseException
     * @throws ResourceNotFoundException
     * @throws JsonException
     */
    public function testJobOfferList(): void
    {
        $this->mockRepository->method('list')->willReturn([new JobOffer(), new JobOffer(), new JobOffer()]);

        $response = $this->mockController->list();
        $this->assertEquals(200, $response->getStatusCode());
    }

    /**
     * @throws ResourceNotFoundException
     * @throws JsonException
     */
    public function testJobOfferListError500(): void
    {

        $this->mockRepository->method('list')
            ->willThrowException(new PDOException('Erreur de connexion à la base de données', 500));


        $this->expectException(DatabaseException::class);
        $response = $this->mockController->list();

        $this->assertEquals(500, $response->getStatusCode());
    }

    /**
     * @throws DatabaseException
     * @throws JsonException
     */
    public function testJobOfferListError404(): void
    {
        $this->mockRepository->method('list')->willReturn([]);

        $this->expectException(ResourceNotFoundException::class);
        $response = $this->mockController->list();

        $this->assertEquals(404, $response->getStatusCode());
    }
}
