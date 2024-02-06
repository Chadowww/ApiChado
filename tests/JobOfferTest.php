<?php

namespace App\Tests;

use App\Controller\JobOfferController;
use App\Entity\JobOffer;
use App\Exceptions\DatabaseException;
use App\Exceptions\InvalidRequestException;
use App\Exceptions\ResourceNotFoundException;
use App\Repository\JobOfferRepository;
use App\Services\EntityServices\EntityBuilder;
use App\Services\RequestValidator\RequestValidatorService;
use PDOException;
use PHPUnit\Framework\TestCase;
use \JsonException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Serializer\SerializerInterface;


class JobOfferTest extends TestCase
{
    CONST array JOB_OFFER_DATA = [
        'title' => 'test21',
        'description' => 'test',
        'city' => 'test',
        'salaryMin' => 40000,
        'salaryMax' => 45000,
    ];
    private RequestValidatorService $requestValidatorService;
    private JobOfferRepository $mockRepository;
    private JobOfferController $mockController;
    private EntityBuilder $entityBuilder;

    protected function setUp(): void
    {
        $serializer = $this->createMock(SerializerInterface::class);
        $this->requestValidatorService = $this->createMock(RequestValidatorService::class);
        $this->mockRepository = $this->createMock(JobOfferRepository::class);
        $this->entityBuilder = $this->createMock(EntityBuilder::class);
        $this->mockController = new JobOfferController(
            $this->requestValidatorService,
            $this->mockRepository,
            $serializer,
            $this->entityBuilder
        );
    }

    /**
     * @throws DatabaseException
     * @throws InvalidRequestException
     * @throws JsonException
     */
    public function testJobOfferCreate(): void
    {
        $request = new Request([],[], [], [], [], [], json_encode(self::JOB_OFFER_DATA, JSON_THROW_ON_ERROR));
        $request->setMethod('POST');
        $request->headers->set('Content-Type', 'application/json');

        $this->entityBuilder->expects($this->once())
            ->method('buildEntity')
            ->willReturn(new JobOffer(self::JOB_OFFER_DATA));

        $response = $this->mockController->create($request);
         $this->assertEquals(201, $response->getStatusCode());
    }

    /**
     * @throws DatabaseException
     * @throws JsonException
     */
    public function testJobOfferCreateError400(): void
    {
        $request = new Request([],[], [], [], [], [], json_encode(self::JOB_OFFER_DATA, JSON_THROW_ON_ERROR));
        $request->setMethod('POST');
        $request->headers->set('Content-Type', 'application/json');

        $this->expectException(InvalidRequestException::class);
        $this->requestValidatorService->expects($this->atLeastOnce())
            ->method('throwError400FromData')
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
        $request = new Request([], [], [], [], [], [], json_encode(self::JOB_OFFER_DATA, JSON_THROW_ON_ERROR));
        $request->setMethod('POST');
        $request->headers->set('Content-Type', 'application/json');

        $this->entityBuilder->expects($this->once())
            ->method('buildEntity')
            ->willReturn(new JobOffer(self::JOB_OFFER_DATA));

        $this->mockRepository
            ->method('create')
            ->willThrowException(new DatabaseException('Erreur de connexion a la base de donnees', 500));

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
        $jobOffer = new JobOffer(self::JOB_OFFER_DATA);

        $this->mockRepository->expects($this->atLeastOnce())
            ->method('read')
            ->willReturn($jobOffer);

        $response = $this->mockController->read(2);

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

        $this->expectException(ResourceNotFoundException::class);

        $response = $this->mockController->read(2);

        $this->assertEquals(404, $response->getStatusCode());
    }

    /**
     * @throws ResourceNotFoundException
     * @throws JsonException
     */
    public function testJobOfferReadError500(): void
    {

        $this->mockRepository
            ->method('read')
            ->willThrowException(new DatabaseException('Erreur de connexion a la base de donnees', 500));

        $this->expectException(DatabaseException::class);
        $this->expectExceptionCode(500);
        $this->expectExceptionMessage('Erreur de connexion a la base de donnees');
        $this->mockController->read(1);
    }

    /**
     * @throws DatabaseException
     * @throws InvalidRequestException
     * @throws ResourceNotFoundException
     * @throws JsonException
     */
    public function testJobOfferUpdate(): void
    {
        $jobOffer = new JobOffer(self::JOB_OFFER_DATA);

        $request = new Request([],[], [], [], [], [], json_encode(self::JOB_OFFER_DATA, JSON_THROW_ON_ERROR));
        $request->setMethod('PUT');
        $request->headers->set('Content-Type', 'application/json');
        $this->entityBuilder->expects($this->atLeastOnce())
            ->method('buildEntity')
            ->willReturn($jobOffer);

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
        $jobOffer = new JobOffer(self::JOB_OFFER_DATA);
        $request = new Request([], [], [], [], [], [], json_encode(self::JOB_OFFER_DATA, JSON_THROW_ON_ERROR));

        $request->setMethod('PUT');
        $request->headers->set('Content-Type', 'application/json');

        $this->mockRepository->expects($this->atLeastOnce())
            ->method('read')
            ->willReturn($jobOffer);

        $this->requestValidatorService->expects($this->atLeastOnce())
            ->method('throwError400FromData')
            ->willThrowException(new InvalidRequestException('Invalid request', 400));

        $this->expectException(InvalidRequestException::class);
        $this->expectExceptionCode(400);

        $this->mockController->update(1, $request);
    }

    /**
     * @throws ResourceNotFoundException
     * @throws InvalidRequestException
     * @throws JsonException
     * @throws DatabaseException
     */
    public function testJobOfferUpdateError500(): void
    {
        $jobOffer = new JobOffer(self::JOB_OFFER_DATA);
        $request = new Request([], [], [], [], [], [], json_encode(self::JOB_OFFER_DATA, JSON_THROW_ON_ERROR));
        $request->setMethod('PUT');
        $request->headers->set('Content-Type', 'application/json');

        $this->entityBuilder->expects($this->once())
            ->method('buildEntity')
            ->willReturn(new JobOffer(self::JOB_OFFER_DATA));

        $this->mockRepository->expects($this->atLeastOnce())
            ->method('read')
            ->willReturn($jobOffer);

        $this->mockRepository->expects($this->atLeastOnce())
            ->method('update')
            ->willThrowException(new PDOException());

        $this->expectException(DatabaseException::class);
        $this->expectExceptionCode(500);

        $this->mockController->update(18, $request);
    }

    /**
     * @throws DatabaseException
     * @throws ResourceNotFoundException
     * @throws JsonException
     */
    public function testJobOfferDelete(): void
    {
        $jobOffer = new JobOffer(self::JOB_OFFER_DATA);
        $request = new Request(['id' => 18], [], [], [], [], [], null);
        $request->setMethod('DELETE');
        $request->headers->set('Content-Type', 'application/json');

        $this->mockRepository->expects($this->atLeastOnce())
            ->method('read')
            ->willReturn($jobOffer);

        $this->mockRepository->expects($this->atLeastOnce())
            ->method('delete')
            ->willReturn(true);

        $response = $this->mockController->delete($request->get('id'));
        $this->assertEquals(200, $response->getStatusCode());
    }

    /**
     * @throws DatabaseException
     * @throws JsonException
     */
    public function testJobOfferDeleteError404(): void
    {
        $jobOffer = new JobOffer(self::JOB_OFFER_DATA);
        $request = new Request(['id' => 18], [], [], [], [], [], null);
        $request->setMethod('DELETE');
        $request->headers->set('Content-Type', 'application/json');

        $this->mockRepository->expects($this->atLeastOnce())
            ->method('read')
            ->willReturn(false);

        $this->expectException(ResourceNotFoundException::class);
        $this->expectExceptionCode(404);

        $this->mockController->delete($request->get('id'));
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
            ->willThrowException(new DatabaseException('Erreur de connexion à la base de données', 500));

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
