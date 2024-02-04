<?php

namespace App\Tests;

use App\Controller\ApplyController;
use App\Entity\Apply;
use App\Exceptions\DatabaseException;
use App\Exceptions\InvalidRequestException;
use App\Exceptions\ResourceNotFoundException;
use App\Repository\ApplyRepository;
use App\Services\EntityServices\ApplyService;
use App\Services\RequestValidator\RequestValidatorService\RequestValidatorService;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Serializer\SerializerInterface;

class ApplyTest extends TestCase
{
    private RequestValidatorService $requestValidatorService;
    private ApplyRepository $repository;
    private ApplyController $controller;

    protected function setUp(): void
    {
        parent::setUp();

        $applyService = $this->createMock(ApplyService::class);
        $serializer = $this->createMock(SerializerInterface::class);
        $this->requestValidatorService = $this->createMock(RequestValidatorService::class);
        $this->repository = $this->createMock(ApplyRepository::class);
        $this->controller = new ApplyController(
            $this->requestValidatorService,
            $applyService,
            $this->repository,
            $serializer
        );
    }

    public function testApplyCreate(): void
    {
        $data = [
            'applyId' => 1,
            'status' => 'pending',
            'message' => 'message',
            'candidateId' => 1,
            'resumeId' => 1,
            'jobofferId' => 1,
            'createdAt' => '2021-08-01T00:00:00+00:00',
            'updatedAt' => '2021-08-01T00:00:00+00:00'
        ];

        $request = new Request([], [], [], [], [], [], json_encode($data, JSON_THROW_ON_ERROR));
        $request->setMethod('POST');
        $request->headers->set('Content-Type', 'application/json');

        $response = $this->controller->create($request);
        $this->assertEquals(201, $response->getStatusCode());
    }

    public function testApplyCreateError400(): void
    {
        $data = [
            'applyId' => 1,
            'status' => 'pending',
            'message' => 'message',
            'candidateId' => 1,
            'resumeId' => 1,
            'jobofferId' => 1,
            'createdAt' => '2021-08-01T00:00:00+00:00',
            'updatedAt' => '2021-08-01T00:00:00+00:00'
        ];

        $request = new Request([], [], [], [], [], [], json_encode($data, JSON_THROW_ON_ERROR));
        $request->setMethod('POST');
        $request->headers->set('Content-Type', 'application/json');

        $this->expectException(InvalidRequestException::class);
        $this->requestValidatorService
            ->expects($this->once())
            ->method('getErrorsFromObject')
            ->willReturn(['error']);

        $this->expectException(InvalidRequestException::class);
        $this->expectExceptionCode(400);

        $this->controller->create($request);
    }

    public function testApplyCreateError500(): void
    {
        $data = [
            'applyId' => 1,
            'status' => 'pending',
            'message' => 'message',
            'candidateId' => 1,
            'resumeId' => 1,
            'jobofferId' => 1,
            'createdAt' => '2021-08-01T00:00:00+00:00',
            'updatedAt' => '2021-08-01T00:00:00+00:00'
        ];

        $request = new Request([], [], [], [], [], [], json_encode($data, JSON_THROW_ON_ERROR));
        $request->setMethod('POST');
        $request->headers->set('Content-Type', 'application/json');

        $this->repository
            ->expects($this->once())
            ->method('create')
            ->willThrowException(new DatabaseException('message d\'erreur', 500));

        $this->expectException(DatabaseException::class);
        $this->expectExceptionCode(500);

        $this->controller->create($request);
    }

    public function testApplyRead(): void
    {
        $data = [
            'applyId' => 1,
            'status' => 'pending',
            'message' => 'message',
            'candidateId' => 1,
            'resumeId' => 1,
            'jobofferId' => 1,
            'createdAt' => '2021-08-01T00:00:00+00:00',
            'updatedAt' => '2021-08-01T00:00:00+00:00'
        ];

        $apply = new Apply($data);

        $request = new Request(['id' => 1], [], [], [], [], [], null);
        $request->setMethod('GET');
        $request->headers->set('Content-Type', 'application/json');
        $this->repository
            ->expects($this->once())
            ->method('read')
            ->willReturn($apply);

        $response = $this->controller->read($request->get('id'));
        $this->assertEquals(200, $response->getStatusCode());
    }

    public function testApplyReadError404(): void
    {
        $request = new Request(['id' => 1], [], [], [], [], [], null);
        $request->setMethod('GET');
        $request->headers->set('Content-Type', 'application/json');
        $this->repository
            ->expects($this->once())
            ->method('read')
            ->willReturn(false);

        $this->expectException(ResourceNotFoundException::class);
        $this->expectExceptionCode(404);

        $this->controller->read($request->get('id'));
    }

    public function testApplyReadError500(): void
    {
        $request = new Request(['id' => 1], [], [], [], [], [], null);
        $request->setMethod('GET');
        $request->headers->set('Content-Type', 'application/json');
        $this->repository
            ->expects($this->once())
            ->method('read')
            ->willThrowException(new DatabaseException('message d\'erreur', 500));

        $this->expectException(DatabaseException::class);
        $this->expectExceptionCode(500);

        $this->controller->read($request->get('id'));
    }

    public function testApplyUpdate(): void
    {
        $data = [
            'applyId' => 1,
            'status' => 'pending',
            'message' => 'message',
            'candidateId' => 1,
            'resumeId' => 1,
            'jobofferId' => 1,
            'createdAt' => '2021-08-01T00:00:00+00:00',
            'updatedAt' => '2021-08-01T00:00:00+00:00'
        ];

        $apply = new Apply($data);

        $request = new Request([], [], [], [], [], [], json_encode($data, JSON_THROW_ON_ERROR));
        $request->setMethod('PUT');
        $request->headers->set('Content-Type', 'application/json');

        $this->repository
            ->expects($this->once())
            ->method('read')
            ->willReturn($apply);

        $this->repository
            ->expects($this->once())
            ->method('update')
            ->willReturn(true);

        $response = $this->controller->update(1, $request);
        $this->assertEquals(200, $response->getStatusCode());
    }

    public function testApplyUpdateError404(): void
    {
        $data = [
            'applyId' => 1,
            'status' => 'pending',
            'message' => 'message',
            'candidateId' => 1,
            'resumeId' => 1,
            'jobofferId' => 1,
            'createdAt' => '2021-08-01T00:00:00+00:00',
            'updatedAt' => '2021-08-01T00:00:00+00:00'
        ];

        $request = new Request([], [], [], [], [], [], json_encode($data, JSON_THROW_ON_ERROR));
        $request->setMethod('PUT');
        $request->headers->set('Content-Type', 'application/json');

        $this->repository
            ->expects($this->once())
            ->method('read')
            ->willReturn(false);

        $this->expectException(ResourceNotFoundException::class);
        $this->expectExceptionCode(404);

        $this->controller->update(1, $request);
    }

    public function testApplyUpdateError500(): void
    {
        $data = [
            'applyId' => 1,
            'status' => 'pending',
            'message' => 'message',
            'candidateId' => 1,
            'resumeId' => 1,
            'jobofferId' => 1,
            'createdAt' => '2021-08-01T00:00:00+00:00',
            'updatedAt' => '2021-08-01T00:00:00+00:00'
        ];

        $apply = new Apply($data);

        $request = new Request([], [], [], [], [], [], json_encode($data, JSON_THROW_ON_ERROR));
        $request->setMethod('PUT');
        $request->headers->set('Content-Type', 'application/json');

        $this->repository
            ->expects($this->once())
            ->method('read')
            ->willReturn($apply);

        $this->repository
            ->expects($this->once())
            ->method('update')
            ->willThrowException(new DatabaseException('message d\'erreur', 500));

        $this->expectException(DatabaseException::class);
        $this->expectExceptionCode(500);

        $this->controller->update(1, $request);
    }

    public function testApplyDelete(): void
    {
        $request = new Request([], [], [], [], [], [], null);
        $request->setMethod('DELETE');
        $request->headers->set('Content-Type', 'application/json');

        $this->repository
            ->expects($this->once())
            ->method('read')
            ->willReturn(new Apply());

        $this->repository
            ->expects($this->once())
            ->method('delete')
            ->willReturn(true);

        $response = $this->controller->delete(1);
        $this->assertEquals(200, $response->getStatusCode());
    }

    public function testApplyDeleteError404(): void
    {
        $request = new Request([], [], [], [], [], [], null);
        $request->setMethod('DELETE');
        $request->headers->set('Content-Type', 'application/json');

        $this->repository
            ->expects($this->once())
            ->method('read')
            ->willReturn(false);

        $this->expectException(ResourceNotFoundException::class);
        $this->expectExceptionCode(404);

        $this->controller->delete(1);
    }

    public function testApplyDeleteError500(): void
    {
        $request = new Request([], [], [], [], [], [], null);
        $request->setMethod('DELETE');
        $request->headers->set('Content-Type', 'application/json');

        $this->repository
            ->expects($this->once())
            ->method('read')
            ->willReturn(new Apply());

        $this->repository
            ->expects($this->once())
            ->method('delete')
            ->willThrowException(new DatabaseException('message d\'erreur', 500));

        $this->expectException(DatabaseException::class);
        $this->expectExceptionCode(500);

        $this->controller->delete(1);
    }

    public function testApplyList(): void
    {
        $request = new Request([], [], [], [], [], [], null);
        $request->setMethod('GET');
        $request->headers->set('Content-Type', 'application/json');

        $this->repository
            ->expects($this->once())
            ->method('list')
            ->willReturn([new Apply()]);

        $response = $this->controller->list();
        $this->assertEquals(200, $response->getStatusCode());
    }

    public function testApplyListError404(): void
    {
        $request = new Request([], [], [], [], [], [], null);
        $request->setMethod('GET');
        $request->headers->set('Content-Type', 'application/json');

        $this->repository
            ->expects($this->once())
            ->method('list')
            ->willReturn(false);

        $this->expectException(ResourceNotFoundException::class);
        $this->expectExceptionCode(404);

        $this->controller->list();
    }

    public function testApplyListError500(): void
    {
        $request = new Request([], [], [], [], [], [], null);
        $request->setMethod('GET');
        $request->headers->set('Content-Type', 'application/json');

        $this->repository
            ->expects($this->once())
            ->method('list')
            ->willThrowException(new DatabaseException('message d\'erreur', 500));

        $this->expectException(DatabaseException::class);
        $this->expectExceptionCode(500);

        $this->controller->list();
    }
}
