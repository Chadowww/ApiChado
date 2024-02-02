<?php

namespace App\Tests;

use App\Controller\CandidateController;
use App\Entity\Candidate;
use App\Exceptions\DatabaseException;
use App\Exceptions\InvalidRequestException;
use App\Exceptions\ResourceNotFoundException;
use App\Repository\CandidateRepository;
use App\Services\EntityServices\CandidateService;
use App\Services\ErrorService;
use App\Services\RequestValidator\RequestEntityValidators\CandidateRequestValidator;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Serializer\SerializerInterface;

class CandidateTest extends TestCase
{
    private CandidateService $candidateService;
    private SerializerInterface $serializer;
    private CandidateRequestValidator $CandidateRequestValidator;
    private CandidateRepository $mockRepository;
    private CandidateController $mockController;

    protected function setUp(): void
    {
        parent::setUp();

        $this->candidateService = $this->createMock(CandidateService::class);
        $this->serializer = $this->createMock(SerializerInterface::class);
        $this->CandidateRequestValidator = $this->createMock(CandidateRequestValidator::class);
        $this->mockRepository = $this->createMock(CandidateRepository::class);
        $this->mockController = new CandidateController(
            $this->CandidateRequestValidator,
            $this->candidateService,
            $this->mockRepository,
            $this->serializer,
        );
    }

    /**
     * @throws DatabaseException
     * @throws InvalidRequestException
     * @throws \JsonException
     */
    public function testCandidateCreate(): void
    {
        $data = [
            'firstname' => 'John',
            'lastname' => 'Doe',
            'phone' => '1234567890',
            'address' => '123 Main St',
            'city' => 'New York',
            'country' => 'USA',
            'userId' => '1',
        ];
        $request = new Request([], [], [], [], [], [], json_encode($data, JSON_THROW_ON_ERROR));
        $request->setMethod('POST');
        $request->headers->set('Content-Type', 'application/json');
        $response = $this->mockController->create($request);
        $this->assertEquals(201, $response->getStatusCode());
    }

    /**
     * @throws InvalidRequestException
     * @throws \JsonException
     */
    public function testCandidateCreateError400(): void
    {
        $data = [
            'firstname' => '',
            'lastname' => '',
            'phone' => 'a',
            'address' => '123 Main St',
            'city' => 'New York',
            'country' => 'USA',
            'userId' => '1',
        ];
        $request = new Request([], [], [], [], [], [], json_encode($data, JSON_THROW_ON_ERROR));
        $request->setMethod('POST');
        $request->headers->set('Content-Type', 'application/json');

        $this->expectException(InvalidRequestException::class);
        $this->CandidateRequestValidator->expects($this->atLeastOnce())
            ->method('getErrorsCandidateRequest')
            ->willThrowException(new InvalidRequestException('message d\'erreur', 400));

        $this->mockController = new CandidateController(
            $this->CandidateRequestValidator,
            $this->candidateService,
            $this->mockRepository,
            $this->serializer,
        );

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
        $data = [
            'firstname' => 'John',
            'lastname' => 'Doe',
            'phone' => '1234567890',
            'address' => '123 Main St',
            'city' => 'New York',
            'country' => 'USA',
            'userId' => '1',
        ];
        $request = new Request([], [], [], [], [], [], json_encode($data, JSON_THROW_ON_ERROR));
        $request->setMethod('POST');
        $request->headers->set('Content-Type', 'application/json');

        $this->mockRepository->expects($this->atLeastOnce())
            ->method('create')
            ->willThrowException(new \PDOException());

        $this->mockController = new CandidateController(
            $this->CandidateRequestValidator,
            $this->candidateService,
            $this->mockRepository,
            $this->serializer,
        );

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
        $candidate = new Candidate([
            'id' => 18,
            'firstname' => 'John',
            'lastname' => 'Doe',
            'phone' => '1234567890',
            'address' => '123 Main St',
            'city' => 'New York',
            'country' => 'USA',
            'userId' => '1',
        ]);
        $request = new Request(['id' => 18], [], [], [], [], [], null);
        $request->setMethod('GET');
        $request->headers->set('Content-Type', 'application/json');
        $this->mockRepository->expects($this->atLeastOnce())
            ->method('read')
            ->willReturn($candidate);
        $this->mockController = new CandidateController(
            $this->CandidateRequestValidator,
            $this->candidateService,
            $this->mockRepository,
            $this->serializer,
        );

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
        $this->mockController = new CandidateController(
            $this->CandidateRequestValidator,
            $this->candidateService,
            $this->mockRepository,
            $this->serializer,
        );
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
            ->willThrowException(new \PDOException());
        $this->mockController = new CandidateController(
            $this->CandidateRequestValidator,
            $this->candidateService,
            $this->mockRepository,
            $this->serializer,
        );
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
        $data = [
            'firstname' => 'John',
            'lastname' => 'Doe',
            'phone' => '1234567890',
            'address' => '123 Main St',
            'city' => 'New York',
            'country' => 'USA',
            'userId' => '1',
        ];
        $candidate = new Candidate($data);
        $request = new Request([], [], [], [], [], [], json_encode($data, JSON_THROW_ON_ERROR));
        $request->setMethod('PUT');
        $request->headers->set('Content-Type', 'application/json');
        $this->mockRepository->expects($this->atLeastOnce())
            ->method('read')
            ->willReturn($candidate);
        $this->mockRepository->expects($this->atLeastOnce())
            ->method('update')
            ->willReturn(true);
        $this->mockController = new CandidateController(
            $this->CandidateRequestValidator,
            $this->candidateService,
            $this->mockRepository,
            $this->serializer,
        );

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
        $data = [
            'firstname' => '',
            'lastname' => '',
            'phone' => 'a',
            'address' => '123 Main St',
            'city' => 'New York',
            'country' => 'USA',
            'userId' => '1',
        ];
        $candidate = new Candidate($data);
        $request = new Request([], [], [], [], [], [], json_encode($data, JSON_THROW_ON_ERROR));
        $request->setMethod('PUT');
        $request->headers->set('Content-Type', 'application/json');

        $this->expectException(InvalidRequestException::class);
        $this->CandidateRequestValidator->expects($this->atLeastOnce())
            ->method('getErrorsCandidateRequest')
            ->willThrowException(new InvalidRequestException('message d\'erreur', 400));

        $this->mockController = new CandidateController(
            $this->CandidateRequestValidator,
            $this->candidateService,
            $this->mockRepository,
            $this->serializer,
        );
        $this->expectException(InvalidRequestException::class);
        $this->expectExceptionCode(400);

        $this->mockController->update(18, $request);
    }

    /**
     * @throws DatabaseException
     * @throws InvalidRequestException
     * @throws \JsonException
     */
    public function testCandidateUpdateError404(): void
    {
        $data = [
            'firstname' => 'John',
            'lastname' => 'Doe',
            'phone' => '1234567890',
            'address' => '123 Main St',
            'city' => 'New York',
            'country' => 'USA',
            'userId' => '1',
        ];
        $request = new Request([], [], [], [], [], [], json_encode($data));
        $request->setMethod('PUT');
        $request->headers->set('Content-Type', 'application/json');
        $this->mockRepository->expects($this->atLeastOnce())
            ->method('read')
            ->willReturn(false);
        $this->mockController = new CandidateController(
            $this->CandidateRequestValidator,
            $this->candidateService,
            $this->mockRepository,
            $this->serializer,
        );
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
        $data = [
            'firstname' => 'John',
            'lastname' => 'Doe',
            'phone' => '1234567890',
            'address' => '123 Main St',
            'city' => 'New York',
            'country' => 'USA',
            'userId' => '1',
        ];
        $candidate = new Candidate($data);
        $request = new Request([], [], [], [], [], [], json_encode($data, JSON_THROW_ON_ERROR));
        $request->setMethod('PUT');
        $request->headers->set('Content-Type', 'application/json');
        $this->mockRepository->expects($this->atLeastOnce())
            ->method('read')
            ->willReturn($candidate);
        $this->mockRepository->expects($this->atLeastOnce())
            ->method('update')
            ->willThrowException(new \PDOException());
        $this->mockController = new CandidateController(
            $this->CandidateRequestValidator,
            $this->candidateService,
            $this->mockRepository,
            $this->serializer,
        );
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
        $candidate = new Candidate([
            'id' => 18,
            'firstname' => 'John',
            'lastname' => 'Doe',
            'phone' => '1234567890',
            'address' => '123 Main St',
            'city' => 'New York',
            'country' => 'USA',
            'userId' => '1',
        ]);
        $request = new Request(['id' => 18], [], [], [], [], [], null);
        $request->setMethod('DELETE');
        $request->headers->set('Content-Type', 'application/json');
        $this->mockRepository->expects($this->atLeastOnce())
            ->method('read')
            ->willReturn($candidate);
        $this->mockRepository->expects($this->atLeastOnce())
            ->method('delete')
            ->willReturn(true);
        $this->mockController = new CandidateController(
            $this->CandidateRequestValidator,
            $this->candidateService,
            $this->mockRepository,
            $this->serializer
        );

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
        $this->mockController = new CandidateController(
            $this->CandidateRequestValidator,
            $this->candidateService,
            $this->mockRepository,
            $this->serializer
        );
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
        $candidate = new Candidate([
            'id' => 18,
            'firstname' => 'John',
            'lastname' => 'Doe',
            'phone' => '1234567890',
            'address' => '123 Main St',
            'city' => 'New York',
            'country' => 'USA',
            'userId' => '1',
        ]);
        $request = new Request(['id' => 18], [], [], [], [], [], null);
        $request->setMethod('DELETE');
        $request->headers->set('Content-Type', 'application/json');
        $this->mockRepository->expects($this->atLeastOnce())
            ->method('read')
            ->willReturn($candidate);
        $this->mockRepository->expects($this->atLeastOnce())
            ->method('delete')
            ->willThrowException(new \PDOException());
        $this->mockController = new CandidateController(
            $this->CandidateRequestValidator,
            $this->candidateService,
            $this->mockRepository,
            $this->serializer
        );
        $this->expectException(DatabaseException::class);
        $this->expectExceptionCode(500);

        $this->mockController->delete($request->get('id'));
    }

    /**
     * @throws DatabaseException
     * @throws \JsonException
     */
    public function testCandidateList(): void
    {
        $candidate = new Candidate([
            'id' => 18,
            'firstname' => 'John',
            'lastname' => 'Doe',
            'phone' => '1234567890',
            'address' => '123 Main St',
            'city' => 'New York',
            'country' => 'USA',
            'userId' => '1',
        ]);
        $request = new Request([], [], [], [], [], [], null);
        $request->setMethod('GET');
        $request->headers->set('Content-Type', 'application/json');
        $this->mockRepository->expects($this->atLeastOnce())
            ->method('list')
            ->willReturn([$candidate]);
        $this->mockController = new CandidateController(
            $this->CandidateRequestValidator,
            $this->candidateService,
            $this->mockRepository,
            $this->serializer
        );

        $response = $this->mockController->list();
        $this->assertEquals(200, $response->getStatusCode());
    }

    /**
     * @throws \JsonException
     */
    public function testCandidateListError500(): void
    {
        $request = new Request([], [], [], [], [], [], null);
        $request->setMethod('GET');
        $request->headers->set('Content-Type', 'application/json');
        $this->mockRepository->expects($this->atLeastOnce())
            ->method('list')
            ->willThrowException(new \PDOException());
        $this->mockController = new CandidateController(
            $this->CandidateRequestValidator,
            $this->candidateService,
            $this->mockRepository,
            $this->serializer
        );
        $this->expectException(DatabaseException::class);
        $this->expectExceptionCode(500);

        $this->mockController->list();
    }
}
