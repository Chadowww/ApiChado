<?php

namespace App\Tests;

use App\Controller\CandidateController;
use App\Exceptions\DatabaseException;
use App\Exceptions\InvalidRequestException;
use App\Repository\CandidateRepository;
use App\Services\EntityServices\CandidateService;
use App\Services\ErrorService;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Validator\ConstraintViolation;
use Symfony\Component\Validator\ConstraintViolationList;
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

}
