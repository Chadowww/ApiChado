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
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class CandidateTest extends TestCase
{
    private CandidateService $candidateService;
    private SerializerInterface $serializer;
    private ValidatorInterface $validator;
    private ErrorService $errorService;
    private CandidateRepository $mockRepository;
    private CandidateController $mockController;

    public function __construct(?string $name = null, array $data = [], $dataName = '')
    {
        parent::__construct($name, $data, $dataName);
        $this->candidateService = $this->createMock(CandidateService::class);
        $this->serializer = $this->createMock(SerializerInterface::class);
        $this->validator = $this->createMock(ValidatorInterface::class);
        $this->errorService = $this->createMock(ErrorService::class);
        $this->mockRepository = $this->createMock(CandidateRepository::class);
        $this->mockController = new CandidateController(
            $this->errorService,
            $this->candidateService,
            $this->mockRepository,
            $this->serializer,
        );
    }

    public function testCandidateCreate(): void
    {
        $data = [
            'firstname' => 'John',
            'lastname' => 'Doe',
            'phone' => '1234567890',
            'address' => '123 Main St',
            'city' => 'New York',
            'country' => 'USA',
            'user_id' => '1',
        ];
        $request = new Request([], [], [], [], [], [], json_encode($data));
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
            'user_id' => '1',
        ];
        $request = new Request([], [], [], [], [], [], json_encode($data, JSON_THROW_ON_ERROR));
        $request->setMethod('POST');
        $request->headers->set('Content-Type', 'application/json');

        $this->errorService->expects($this->atLeastOnce())
            ->method('getErrorsCandidateRequest')
            ->willReturn(['This value should not be blank.']);

        $this->mockController = new CandidateController(
            $this->errorService,
            $this->candidateService,
            $this->mockRepository,
            $this->serializer,
        );

        $this->expectException(InvalidRequestException::class);
        $this->expectExceptionCode(400);

        $this->mockController->create($request);
    }

    public function testCandidateCreateError500(): void
    {
        $data = [
            'firstname' => 'John',
            'lastname' => 'Doe',
            'phone' => '1234567890',
            'address' => '123 Main St',
            'city' => 'New York',
            'country' => 'USA',
            'user_id' => '1',
        ];
        $request = new Request([], [], [], [], [], [], json_encode($data));
        $request->setMethod('POST');
        $request->headers->set('Content-Type', 'application/json');

        $this->mockRepository->expects($this->atLeastOnce())
            ->method('create')
            ->willThrowException(new \PDOException());

        $this->mockController = new CandidateController(
            $this->errorService,
            $this->candidateService,
            $this->mockRepository,
            $this->serializer,
        );

        $this->expectException(DatabaseException::class);
        $this->expectExceptionCode(500);

        $this->mockController->create($request);
    }

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
            'user_id' => '1',
        ]);
        $request = new Request(['id' => 18], [], [], [], [], [], null);
        $request->setMethod('GET');
        $request->headers->set('Content-Type', 'application/json');
        $this->mockRepository->expects($this->atLeastOnce())
            ->method('read')
            ->willReturn($candidate);
        $this->mockController = new CandidateController(
            $this->errorService,
            $this->candidateService,
            $this->mockRepository,
            $this->serializer,
        );

        $response = $this->mockController->read($request->get('id'));
        $this->assertEquals(200, $response->getStatusCode());
    }

    public function testCandidateReadError404(): void
    {
        $request = new Request(['id' => 18], [], [], [], [], [], null);
        $request->setMethod('GET');
        $request->headers->set('Content-Type', 'application/json');
        $this->mockRepository->expects($this->atLeastOnce())
            ->method('read')
            ->willReturn(false);
        $this->mockController = new CandidateController(
            $this->errorService,
            $this->candidateService,
            $this->mockRepository,
            $this->serializer,
        );
        $this->expectException(ResourceNotFoundException::class);
        $this->expectExceptionCode(404);

        $this->mockController->read($request->get('id'));
    }

    public function testCandidateReadError500(): void
    {
        $request = new Request(['id' => 18], [], [], [], [], [], null);
        $request->setMethod('GET');
        $request->headers->set('Content-Type', 'application/json');
        $this->mockRepository->expects($this->atLeastOnce())
            ->method('read')
            ->willThrowException(new \PDOException());
        $this->mockController = new CandidateController(
            $this->errorService,
            $this->candidateService,
            $this->mockRepository,
            $this->serializer,
        );
        $this->expectException(DatabaseException::class);
        $this->expectExceptionCode(500);

        $this->mockController->read($request->get('id'));
    }

    public function testCandidateUpdate(): void
    {
        $data = [
            'firstname' => 'John',
            'lastname' => 'Doe',
            'phone' => '1234567890',
            'address' => '123 Main St',
            'city' => 'New York',
            'country' => 'USA',
            'user_id' => '1',
        ];
        $candidate = new Candidate($data);
        $request = new Request([], [], [], [], [], [], json_encode($data));
        $request->setMethod('PUT');
        $request->headers->set('Content-Type', 'application/json');
        $this->mockRepository->expects($this->atLeastOnce())
            ->method('read')
            ->willReturn($candidate);
        $this->mockRepository->expects($this->atLeastOnce())
            ->method('update')
            ->willReturn(true);
        $this->mockController = new CandidateController(
            $this->errorService,
            $this->candidateService,
            $this->mockRepository,
            $this->serializer,
        );

        $response = $this->mockController->update(18, $request);
        $this->assertEquals(200, $response->getStatusCode());
    }

    public function testCandidateUpdateError400(): void
    {
        $data = [
            'firstname' => '',
            'lastname' => '',
            'phone' => 'a',
            'address' => '123 Main St',
            'city' => 'New York',
            'country' => 'USA',
            'user_id' => '1',
        ];
        $candidate = new Candidate($data);
        $request = new Request([], [], [], [], [], [], json_encode($data));
        $request->setMethod('PUT');
        $request->headers->set('Content-Type', 'application/json');
        $this->errorService->expects($this->atLeastOnce())
            ->method('getErrorsCandidateRequest')
            ->willReturn(['This value should not be blank.']);
        $this->mockController = new CandidateController(
            $this->errorService,
            $this->candidateService,
            $this->mockRepository,
            $this->serializer,
        );
        $this->expectException(InvalidRequestException::class);
        $this->expectExceptionCode(400);

        $this->mockController->update(18, $request);
    }

    public function testCandidateUpdateError404(): void
    {
        $data = [
            'firstname' => 'John',
            'lastname' => 'Doe',
            'phone' => '1234567890',
            'address' => '123 Main St',
            'city' => 'New York',
            'country' => 'USA',
            'user_id' => '1',
        ];
        $request = new Request([], [], [], [], [], [], json_encode($data));
        $request->setMethod('PUT');
        $request->headers->set('Content-Type', 'application/json');
        $this->mockRepository->expects($this->atLeastOnce())
            ->method('read')
            ->willReturn(false);
        $this->mockController = new CandidateController(
            $this->errorService,
            $this->candidateService,
            $this->mockRepository,
            $this->serializer,
        );
        $this->expectException(ResourceNotFoundException::class);
        $this->expectExceptionCode(404);

        $this->mockController->update(18, $request);
    }

    public function testCandidateUpdateError500(): void
    {
        $data = [
            'firstname' => 'John',
            'lastname' => 'Doe',
            'phone' => '1234567890',
            'address' => '123 Main St',
            'city' => 'New York',
            'country' => 'USA',
            'user_id' => '1',
        ];
        $candidate = new Candidate($data);
        $request = new Request([], [], [], [], [], [], json_encode($data));
        $request->setMethod('PUT');
        $request->headers->set('Content-Type', 'application/json');
        $this->mockRepository->expects($this->atLeastOnce())
            ->method('read')
            ->willReturn($candidate);
        $this->mockRepository->expects($this->atLeastOnce())
            ->method('update')
            ->willThrowException(new \PDOException());
        $this->mockController = new CandidateController(
            $this->errorService,
            $this->candidateService,
            $this->mockRepository,
            $this->serializer,
        );
        $this->expectException(DatabaseException::class);
        $this->expectExceptionCode(500);

        $this->mockController->update(18, $request);
    }

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
            'user_id' => '1',
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
            $this->errorService,
            $this->candidateService,
            $this->mockRepository,
            $this->serializer
        );

        $response = $this->mockController->delete($request->get('id'));
        $this->assertEquals(200, $response->getStatusCode());
    }

    public function testCandidateDeleteError404(): void
    {
        $request = new Request(['id' => 18], [], [], [], [], [], null);
        $request->setMethod('DELETE');
        $request->headers->set('Content-Type', 'application/json');
        $this->mockRepository->expects($this->atLeastOnce())
            ->method('read')
            ->willReturn(false);
        $this->mockController = new CandidateController(
            $this->errorService,
            $this->candidateService,
            $this->mockRepository,
            $this->serializer
        );
        $this->expectException(ResourceNotFoundException::class);
        $this->expectExceptionCode(404);

        $this->mockController->delete($request->get('id'));
    }

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
            'user_id' => '1',
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
            $this->errorService,
            $this->candidateService,
            $this->mockRepository,
            $this->serializer
        );
        $this->expectException(DatabaseException::class);
        $this->expectExceptionCode(500);

        $this->mockController->delete($request->get('id'));
    }
}
