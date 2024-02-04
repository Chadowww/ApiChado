<?php

namespace App\Tests;

use App\Controller\CandidateController;
use App\Entity\Candidate;
use App\Exceptions\DatabaseException;
use App\Exceptions\InvalidRequestException;
use App\Exceptions\ResourceNotFoundException;
use App\Repository\CandidateRepository;
use App\Services\EntityServices\EntityBuilder;
use App\Services\RequestValidator\RequestValidatorService\RequestValidatorService;
use PDOException;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Serializer\SerializerInterface;

class CandidateTest extends TestCase
{
    CONST array CANDIDATE_DATA = [
        'firstname' => 'John',
        'lastname' => 'Doe',
        'phone' => '1234567890',
        'address' => '123 Main St',
        'city' => 'New York',
        'country' => 'USA',
        'userId' => 1,
    ];
    private RequestValidatorService $requestValidatorService;
    private CandidateRepository $mockRepository;
    private CandidateController $mockController;

    protected function setUp(): void
    {
        parent::setUp();

        $this->entityBuilder = $this->createMock(EntityBuilder::class);
        $serializer = $this->createMock(SerializerInterface::class);
        $this->requestValidatorService = $this->createMock(RequestValidatorService::class);
        $this->mockRepository = $this->createMock(CandidateRepository::class);
        $this->mockController = new CandidateController(
            $this->requestValidatorService,
            $this->entityBuilder,
            $this->mockRepository,
            $serializer,
        );
    }

    /**
     * @throws DatabaseException
     * @throws InvalidRequestException
     * @throws \JsonException
     */
    public function testCandidateCreate(): void
    {
        $request = new Request([], [], [], [], [], [], json_encode(self::CANDIDATE_DATA, JSON_THROW_ON_ERROR));
        $request->setMethod('POST');
        $request->headers->set('Content-Type', 'application/json');

        $this->entityBuilder->expects($this->once())
            ->method('buildEntity')
            ->willReturn(new Candidate(self::CANDIDATE_DATA));

        $response = $this->mockController->create($request);

        $this->assertEquals(201, $response->getStatusCode());
    }

    /**
     * @throws InvalidRequestException
     * @throws \JsonException
     * @throws DatabaseException
     */
    public function testCandidateCreateError400(): void
    {
        $request = new Request([], [], [], [], [], [], json_encode(self::CANDIDATE_DATA, JSON_THROW_ON_ERROR));
        $request->setMethod('POST');
        $request->headers->set('Content-Type', 'application/json');

        $this->expectException(InvalidRequestException::class);
        $this->requestValidatorService->expects($this->atLeastOnce())
            ->method('getErrorsFromObject')
            ->willReturn(['error' => 'error message']);

        $this->expectException(InvalidRequestException::class);
        $this->expectExceptionCode(400);

        $this->mockController->create($request);
    }

    /**
     * @throws InvalidRequestException
     * @throws \JsonException
     */
    public function testCandidateCreateError500(): void
    {
        $request = new Request([], [], [], [], [], [], json_encode(self::CANDIDATE_DATA, JSON_THROW_ON_ERROR));
        $request->setMethod('POST');
        $request->headers->set('Content-Type', 'application/json');

        $this->entityBuilder->expects($this->once())
            ->method('buildEntity')
            ->willReturn(new Candidate(self::CANDIDATE_DATA));

        $this->mockRepository->expects($this->atLeastOnce())
            ->method('create')
            ->willThrowException(new PDOException());

        $this->expectException(DatabaseException::class);
        $this->expectExceptionCode(500);

        $this->mockController->create($request);
    }

    /**
     * @throws DatabaseException
     * @throws ResourceNotFoundException
     * @throws \JsonException
     */
    public function  testCandidateRead(): void
    {
        $candidate = new Candidate(self::CANDIDATE_DATA);
        $request = new Request(['id' => 18], [], [], [], [], [], null);
        $request->setMethod('GET');
        $request->headers->set('Content-Type', 'application/json');
        $this->mockRepository->expects($this->atLeastOnce())
            ->method('read')
            ->willReturn($candidate);

        $response = $this->mockController->read($request->get('id'));

        $this->assertEquals(200, $response->getStatusCode());
    }

    /**
     * @throws DatabaseException
     * @throws \JsonException
     */
    public function testCandidateReadError404(): void
    {
        $request = new Request(['id' => 18], [], [], [], [], [], null);
        $request->setMethod('GET');
        $request->headers->set('Content-Type', 'application/json');

        $this->mockRepository->expects($this->atLeastOnce())
            ->method('read')
            ->willReturn(false);

        $this->expectException(ResourceNotFoundException::class);
        $this->expectExceptionCode(404);

        $this->mockController->read($request->get('id'));
    }

    /**
     * @throws ResourceNotFoundException
     * @throws \JsonException
     */
    public function testCandidateReadError500(): void
    {
        $request = new Request(['id' => 18], [], [], [], [], [], null);
        $request->setMethod('GET');
        $request->headers->set('Content-Type', 'application/json');

        $this->mockRepository->expects($this->atLeastOnce())
            ->method('read')
            ->willThrowException(new DatabaseException('message d\'erreur', 500));

        $this->expectException(DatabaseException::class);
        $this->expectExceptionCode(500);

        $this->mockController->read($request->get('id'));
    }

    /**
     * @throws DatabaseException
     * @throws InvalidRequestException
     * @throws ResourceNotFoundException
     * @throws \JsonException
     */
    public function testCandidateUpdate(): void
    {

        $candidate = new Candidate(self::CANDIDATE_DATA);
        $request = new Request([], [], [], [], [], [], json_encode(self::CANDIDATE_DATA, JSON_THROW_ON_ERROR));
        $request->setMethod('PUT');
        $request->headers->set('Content-Type', 'application/json');

        $this->entityBuilder->expects($this->once())
            ->method('buildEntity')
            ->willReturn(new Candidate(self::CANDIDATE_DATA));

        $this->mockRepository->expects($this->atLeastOnce())
            ->method('read')
            ->willReturn($candidate);

        $this->mockRepository->expects($this->atLeastOnce())
            ->method('update')
            ->willReturn(true);

        $response = $this->mockController->update(18, $request);
        $this->assertEquals(200, $response->getStatusCode());
    }

    /**
     * @throws DatabaseException
     * @throws ResourceNotFoundException
     * @throws \JsonException
     */
    public function testCandidateUpdateError400(): void
    {
        $candidate = new Candidate(self::CANDIDATE_DATA);
        $request = new Request([], [], [], [], [], [], json_encode(self::CANDIDATE_DATA, JSON_THROW_ON_ERROR));
        $request->setMethod('PUT');
        $request->headers->set('Content-Type', 'application/json');

        $this->mockRepository->expects($this->atLeastOnce())
            ->method('read')
            ->willReturn($candidate);

        $this->requestValidatorService->expects($this->atLeastOnce())
            ->method('getErrorsFromObject')
            ->willReturn(['error' => 'error message']);

        $this->expectException(InvalidRequestException::class);
        $this->expectExceptionCode(400);

        $this->mockController->update(1, $request);
    }

    /**
     * @throws DatabaseException
     * @throws InvalidRequestException
     * @throws \JsonException
     */
    public function testCandidateUpdateError404(): void
    {
        $request = new Request([], [], [], [], [], [], json_encode(self::CANDIDATE_DATA, JSON_THROW_ON_ERROR));
        $request->setMethod('PUT');
        $request->headers->set('Content-Type', 'application/json');

        $this->mockRepository->expects($this->atLeastOnce())
            ->method('read')
            ->willReturn(false);

        $this->expectException(ResourceNotFoundException::class);
        $this->expectExceptionCode(404);

        $this->mockController->update(18, $request);
    }

    /**
     * @throws ResourceNotFoundException
     * @throws InvalidRequestException
     * @throws \JsonException
     */
    public function testCandidateUpdateError500(): void
    {
        $candidate = new Candidate(self::CANDIDATE_DATA);
        $request = new Request([], [], [], [], [], [], json_encode(self::CANDIDATE_DATA, JSON_THROW_ON_ERROR));
        $request->setMethod('PUT');
        $request->headers->set('Content-Type', 'application/json');

        $this->entityBuilder->expects($this->once())
            ->method('buildEntity')
            ->willReturn(new Candidate(self::CANDIDATE_DATA));

        $this->mockRepository->expects($this->atLeastOnce())
            ->method('read')
            ->willReturn($candidate);

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
     * @throws \JsonException
     */
    public function testCandidateDelete(): void
    {
        $candidate = new Candidate(self::CANDIDATE_DATA);
        $request = new Request(['id' => 18], [], [], [], [], [], null);
        $request->setMethod('DELETE');
        $request->headers->set('Content-Type', 'application/json');

        $this->mockRepository->expects($this->atLeastOnce())
            ->method('read')
            ->willReturn($candidate);

        $this->mockRepository->expects($this->atLeastOnce())
            ->method('delete')
            ->willReturn(true);


        $response = $this->mockController->delete($request->get('id'));
        $this->assertEquals(200, $response->getStatusCode());
    }

    /**
     * @throws DatabaseException
     * @throws \JsonException
     */
    public function testCandidateDeleteError404(): void
    {
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
     * @throws \JsonException
     */
    public function testCandidateDeleteError500(): void
    {
        $candidate = new Candidate(self::CANDIDATE_DATA);
        $request = new Request(['id' => 18], [], [], [], [], [], null);
        $request->setMethod('DELETE');
        $request->headers->set('Content-Type', 'application/json');

        $this->mockRepository->expects($this->atLeastOnce())
            ->method('read')
            ->willReturn($candidate);

        $this->mockRepository->expects($this->atLeastOnce())
            ->method('delete')
            ->willThrowException(new \PDOException());

        $this->expectException(DatabaseException::class);
        $this->expectExceptionCode(500);

        $this->mockController->delete($request->get('id'));
    }

    /**
     * @throws DatabaseException
     * @throws \JsonException
     * @throws ResourceNotFoundException
     */
    public function testCandidateList(): void
    {
        $candidate = new Candidate(self::CANDIDATE_DATA);
        $request = new Request([], [], [], [], [], [], null);
        $request->setMethod('GET');
        $request->headers->set('Content-Type', 'application/json');
        $this->mockRepository->expects($this->atLeastOnce())
            ->method('list')
            ->willReturn([$candidate]);

        $response = $this->mockController->list();
        $this->assertEquals(200, $response->getStatusCode());
    }

    /**
     * @throws \JsonException
     * @throws ResourceNotFoundException
     */
    public function testCandidateListError500(): void
    {
        $request = new Request([], [], [], [], [], [], null);
        $request->setMethod('GET');
        $request->headers->set('Content-Type', 'application/json');

        $this->mockRepository->expects($this->atLeastOnce())
            ->method('list')
            ->willThrowException(new PDOException());

        $this->expectException(DatabaseException::class);
        $this->expectExceptionCode(500);

        $this->mockController->list();
    }
}
