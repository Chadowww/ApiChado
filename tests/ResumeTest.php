<?php

namespace App\Tests;

use App\Controller\ImageController;
use App\Controller\ResumeController;
use App\Entity\Resume;
use App\Exceptions\DatabaseException;
use App\Exceptions\InvalidRequestException;
use App\Exceptions\ResourceNotFoundException;
use App\Repository\ResumeRepository;
use App\Services\EntityServices\EntityBuilder;
use App\Services\FileManagerService\FileManagerService;
use App\Services\RequestValidator\RequestValidatorService;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Serializer\SerializerInterface;

class ResumeTest extends TestCase

{
    CONST array RESUME_DATA = [
        'resumeId' => 1,
        'title' => 'title',
        'filename' => 'filename',
        'createdAt' => '2021-01-01 00:00:00',
        'updatedAt' => '2021-01-01 00:00:00',
        'candidateId' => 1,
    ];

    private RequestValidatorService $requestValidatorService;
    private EntityBuilder $entityBuilder;
    private ResumeRepository $resumeRepository;
    private ResumeController $resumeController;
    private ImageController $imageController;
    private FileManagerService $fileManagerService;

    public function setUp(): void
    {
        $this->requestValidatorService = $this->createMock(RequestValidatorService::class);
        $this->entityBuilder = $this->createMock(EntityBuilder::class);
        $this->resumeRepository = $this->createMock(ResumeRepository::class);
        $serializer = $this->createMock(SerializerInterface::class);
        $this->imageController = $this->createMock(ImageController::class);
        $this->fileManagerService = $this->createMock(FileManagerService::class);
        $this->resumeController = new ResumeController(
            $this->requestValidatorService,
            $this->entityBuilder,
            $this->resumeRepository,
            $serializer,
            $this->fileManagerService
        );
    }

    public function testResumeCreate(): void
    {
        $request = new Request([], [], [], [], [], [], json_encode(self::RESUME_DATA, JSON_THROW_ON_ERROR));
        $this->entityBuilder->method('buildEntity')->willReturn($this->createMock(Resume::class));
        $this->resumeRepository->method('create')->willReturn(true);

        $response = $this->resumeController->create($request, $this->imageController);

        $this->assertEquals(201, $response->getStatusCode());
    }

    public function testResumeCreateError400(): void
    {
        $request = new Request([], [], [], [], [], [], json_encode(self::RESUME_DATA, JSON_THROW_ON_ERROR));

        $this->expectException(InvalidRequestException::class);
        $this->requestValidatorService->expects($this->once())
            ->method('throwError400FromData')
            ->willThrowException(new InvalidRequestException('Invalid request', 400));

        $this->entityBuilder->method('buildEntity')->willReturn($this->createMock(Resume::class));
        $this->resumeRepository->method('create')->willReturn(false);

        $this->expectException(InvalidRequestException::class);
        $this->expectExceptionCode(400);

        $response = $this->resumeController->create($request, $this->imageController);
    }

    public function testResumeCreateError500(): void
    {
        $request = new Request([], [], [], [], [], [], json_encode(self::RESUME_DATA, JSON_THROW_ON_ERROR));
        $request->setMethod('POST');
        $request->headers->set('Content-Type', 'application/json');

        $this->entityBuilder->method('buildEntity')->willReturn(new Resume(self::RESUME_DATA));
        $this->resumeRepository
            ->expects($this->once())
            ->method('create')
            ->willThrowException(new DatabaseException('message d\'erreur', 500));

        $this->expectException(DatabaseException::class);
        $this->expectExceptionCode(500);

        $response = $this->resumeController->create($request, $this->imageController);
    }

    public function testResumeRead(): void
    {
        $resume = new Resume(self::RESUME_DATA);

        $this->resumeRepository
            ->expects($this->once())
            ->method('read')
            ->willReturn($resume);

        $response = $this->resumeController->read(1);
        $this->assertEquals(200, $response->getStatusCode());
    }

    public function testResumeReadError404(): void
    {
        $this->resumeRepository
            ->expects($this->once())
            ->method('read')
            ->willReturn(false);

        $this->expectException(ResourceNotFoundException::class);
        $this->expectExceptionCode(404);

        $response = $this->resumeController->read(1);
        $this->assertEquals(404, $response->getStatusCode());
    }

    public function testResumeReadError500(): void
    {
        $this->resumeRepository
            ->expects($this->once())
            ->method('read')
            ->willThrowException(new DatabaseException('An error was throw', 500));

        $this->expectException(DatabaseException::class);
        $this->expectExceptionCode(500);

        $response = $this->resumeController->read(1);
        $this->assertEquals(500, $response->getStatusCode());
    }

    public function testResumeUpdate(): void
    {
        $resume = new Resume(self::RESUME_DATA);
        $request = new Request([], [], [], [], [], [], json_encode(self::RESUME_DATA, JSON_THROW_ON_ERROR));

        $this->resumeRepository
            ->expects($this->once())
            ->method('read')
            ->willReturn($resume);

        $this->imageController
            ->expects($this->once())
            ->method('create')
            ->willReturn(new JsonResponse(['filename' => 'filename'], 201));

        $this->entityBuilder
            ->expects($this->once())
            ->method('buildEntity')
            ->willReturn($resume);

        $this->resumeRepository
            ->expects($this->once())
            ->method('update')
            ->willReturn(true);

        $response = $this->resumeController->update(1, $request, $this->imageController);
        $this->assertEquals(200, $response->getStatusCode());
    }

    public function testResumeUpdateError400(): void
    {
        $resume = new Resume(self::RESUME_DATA);
        $request = new Request([], [], [], [], [], [], json_encode(self::RESUME_DATA, JSON_THROW_ON_ERROR));

        $this->expectException(InvalidRequestException::class);
        $this->expectExceptionCode(400);

        $this->resumeRepository
            ->expects($this->once())
            ->method('read')
            ->willReturn($resume);

        $this->requestValidatorService
            ->expects($this->once())
            ->method('throwError400FromData')
            ->willThrowException(new InvalidRequestException('Bad request', 400));

        $response = $this->resumeController->update(1, $request, $this->imageController);
        $this->assertEquals(400, $response->getStatusCode());
    }

    public function testResumeUpdateError404(): void
    {
        $request = new Request([], [], [], [], [], [], json_encode(self::RESUME_DATA, JSON_THROW_ON_ERROR));

        $this->expectException(ResourceNotFoundException::class);
        $this->expectExceptionCode(404);

        $this->resumeRepository
            ->expects($this->once())
            ->method('read')
            ->willReturn(false);

        $response = $this->resumeController->update(1, $request, $this->imageController);
        $this->assertEquals(404, $response->getStatusCode());
    }

    public function testResumeUpdateError500(): void
    {
        $resume = new Resume(self::RESUME_DATA);
        $request = new Request([], [], [], [], [], [], json_encode(self::RESUME_DATA, JSON_THROW_ON_ERROR));
        $this->expectException(DatabaseException::class);
        $this->expectExceptionCode(500);

        $this->resumeRepository
            ->expects($this->once())
            ->method('read')
            ->willReturn($resume);

        $this->imageController
            ->expects($this->once())
            ->method('create')
            ->willReturn(new JsonResponse(['filename' => 'filename'], 201));

        $this->entityBuilder
            ->expects($this->once())
            ->method('buildEntity')
            ->willReturn($resume);

        $this->resumeRepository
            ->expects($this->once())
            ->method('update')
            ->willThrowException(new DatabaseException('An error was throw', 500));

        $response = $this->resumeController->update(1, $request, $this->imageController);
        $this->assertEquals(500, $response->getStatusCode());
    }

    public function testResumeDelete(): void
    {
        imagejpeg(imagecreatetruecolor(100, 100), '/Users/chado/Desktop/JobItBetter/ApiChado/public/cv/filename.jpg');
        $resume = new Resume(self::RESUME_DATA);
        $resume->setResumeId(1);
        $resume->setFilename('filename.jpg');
        $resume->setCandidateId(1);
        $resume->setCreatedAt('2021-01-01 00:00:00');
        $resume->setUpdatedAt('2021-01-01 00:00:00');

        $this->resumeRepository
            ->expects($this->once())
            ->method('read')
            ->willReturn($resume);

        $this->fileManagerService
            ->expects($this->once())
            ->method('verifyExistFile')
            ->willReturn(true);

        $this->resumeRepository
            ->expects($this->once())
            ->method('delete')
            ->willReturn(true);

        $response = $this->resumeController->delete(1);
        $this->assertEquals(200, $response->getStatusCode());
    }

    public function testResumeDeleteError404(): void
    {
        $this->expectException(ResourceNotFoundException::class);
        $this->resumeRepository
            ->expects($this->once())
            ->method('read')
            ->willReturn(false);

        $response = $this->resumeController->delete(1);
        $this->assertEquals(404, $response->getStatusCode());
    }

    public function testResumeDeleteError500(): void
    {
        imagejpeg(imagecreatetruecolor(100, 100), '/Users/chado/Desktop/JobItBetter/ApiChado/public/cv/filename.jpg');
        $resume = new Resume(self::RESUME_DATA);
        $resume->setResumeId(1);
        $resume->setFilename('filename.jpg');
        $resume->setCandidateId(1);
        $resume->setCreatedAt('2021-01-01 00:00:00');
        $resume->setUpdatedAt('2021-01-01 00:00:00');

        $this->resumeRepository
            ->expects($this->once())
            ->method('read')
            ->willReturn($resume);

        $this->fileManagerService
            ->expects($this->once())
            ->method('verifyExistFile')
            ->willReturn(true);

        $this->expectException(DatabaseException::class);
        $this->resumeRepository
            ->expects($this->once())
            ->method('delete')
            ->willThrowException(new DatabaseException('An execption was throw', 500));

        $response = $this->resumeController->delete(1);
        $this->assertEquals(500, $response->getStatusCode());
    }

    public function testResumeList(): void
    {
        $resume = new Resume(self::RESUME_DATA);

        $this->resumeRepository
            ->expects($this->once())
            ->method('list')
            ->willReturn([$resume]);

        $response = $this->resumeController->list();
        $this->assertEquals(200, $response->getStatusCode());
    }

    public function testResumeListError404(): void
    {
        $resume = new Resume(self::RESUME_DATA);

        $this->resumeRepository
            ->expects($this->once())
            ->method('list')
            ->willReturn([]);

        $this->expectException(ResourceNotFoundException::class);
        $response = $this->resumeController->list();
        $this->assertEquals(404, $response->getStatusCode());
    }

    public function testResumeList500(): void
    {
        $resume = new Resume(self::RESUME_DATA);

        $this->expectException(DatabaseException::class);
        $this->resumeRepository
            ->expects($this->once())
            ->method('list')
            ->willThrowException(new DatabaseException('An error was throw', 500));

        $response = $this->resumeController->list();
        $this->assertEquals(500, $response->getStatusCode());
    }
}
