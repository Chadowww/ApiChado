<?php

namespace App\Tests;

use App\Controller\CompanyController;
use App\Entity\Company;
use App\Exceptions\DatabaseException;
use App\Exceptions\InvalidRequestException;
use App\Exceptions\ResourceNotFoundException;
use App\Repository\CompanyRepository;
use App\Services\EntityServices\EntityBuilder;
use App\Services\RequestValidator\RequestValidatorService;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Serializer\SerializerInterface;

class CompanyTest extends TestCase
{
    CONST array COMPANY_DATA = [
        'name' => 'Test Company',
        'phone' => '0123456789',
        'address' => 'Test Address',
        'city' => 'Test City',
        'country' => 'Test Country',
        'description' => 'description de base de test',
        'siret' => '12345678912345',
        'logo' => 'Test Logo',
        'slug' => 'test-company',
        'cover' => 'Test Cover',
        'userId' => 1
    ];

    private RequestValidatorService $requestValidatorService;
    private EntityBuilder $entityBuilder;
    private CompanyRepository $companyRepository;
    private CompanyController $companyController;

    public function setUp(): void
    {
        $this->requestValidatorService = $this->createMock(RequestValidatorService::class);
        $this->entityBuilder = $this->createMock(EntityBuilder::class);
        $this->companyRepository = $this->createMock(CompanyRepository::class);
        $serializer = $this->createMock(SerializerInterface::class);
        $this->companyController = new CompanyController(
            $this->requestValidatorService,
            $this->entityBuilder,
            $this->companyRepository,
            $serializer
        );
    }

    public function testCompanyCreate(): void
    {
        $request = new Request([], [], [], [], [], [], json_encode(self::COMPANY_DATA, JSON_THROW_ON_ERROR));
        $this->entityBuilder->method('buildEntity')->willReturn($this->createMock(Company::class));
        $this->companyRepository->method('create')->willReturn(true);

        $response = $this->companyController->create($request);

        $this->assertEquals(201, $response->getStatusCode());
    }

    public function testCompanyCreateError400(): void
    {
        $request = new Request([], [], [], [], [], [], json_encode(self::COMPANY_DATA, JSON_THROW_ON_ERROR));

        $this->expectException(InvalidRequestException::class);
        $this->requestValidatorService->expects($this->once())
            ->method('throwError400FromData')
            ->willThrowException(new InvalidRequestException('Invalid request', 400));

        $this->entityBuilder->method('buildEntity')->willReturn($this->createMock(Company::class));
        $this->companyRepository->method('create')->willReturn(false);

        $this->expectException(InvalidRequestException::class);
        $this->expectExceptionCode(400);

        $response = $this->companyController->create($request);
    }

    public function testCompanyCreateError500(): void
    {
        $request = new Request([], [], [], [], [], [], json_encode(self::COMPANY_DATA, JSON_THROW_ON_ERROR));
        $request->setMethod('POST');
        $request->headers->set('Content-Type', 'application/json');

        $this->entityBuilder->method('buildEntity')->willReturn(new Company(self::COMPANY_DATA));
        $this->companyRepository
            ->expects($this->once())
            ->method('create')
            ->willThrowException(new DatabaseException('message d\'erreur', 500));

        $this->expectException(DatabaseException::class);
        $this->expectExceptionCode(500);

        $response = $this->companyController->create($request);
    }

    public function testCompanyRead(): void
    {
        $company = new Company(self::COMPANY_DATA);

        $this->companyRepository
            ->expects($this->once())
            ->method('read')
            ->willReturn($company);

        $response = $this->companyController->read(1);
        $this->assertEquals(200, $response->getStatusCode());
    }

    public function testCompanyReadError404(): void
    {
        $this->companyRepository
            ->expects($this->once())
            ->method('read')
            ->willReturn(false);

        $this->expectException(ResourceNotFoundException::class);
        $this->expectExceptionCode(404);

        $response = $this->companyController->read(1);
        $this->assertEquals(404, $response->getStatusCode());
    }

    public function testCompanyReadError500(): void
    {
        $this->companyRepository
            ->expects($this->once())
            ->method('read')
            ->willThrowException(new DatabaseException('An error was throw', 500));

        $this->expectException(DatabaseException::class);
        $this->expectExceptionCode(500);

        $response = $this->companyController->read(1);
        $this->assertEquals(500, $response->getStatusCode());
    }

    public function testCompanyUpdate(): void
    {
        $company = new Company(self::COMPANY_DATA);
        $request = new Request([], [], [], [], [], [], json_encode(self::COMPANY_DATA, JSON_THROW_ON_ERROR));

        $this->companyRepository
            ->expects($this->once())
            ->method('read')
            ->willReturn($company);

        $this->entityBuilder
            ->expects($this->once())
            ->method('buildEntity')
            ->willReturn($company);

        $this->companyRepository
            ->expects($this->once())
            ->method('update')
            ->willReturn(true);

        $response = $this->companyController->update(1, $request);
        $this->assertEquals(200, $response->getStatusCode());
    }

    public function testCompanyUpdateError400(): void
    {
        $company = new Company(self::COMPANY_DATA);
        $request = new Request([], [], [], [], [], [], json_encode(self::COMPANY_DATA, JSON_THROW_ON_ERROR));

        $this->expectException(InvalidRequestException::class);
        $this->expectExceptionCode(400);

        $this->companyRepository
            ->expects($this->once())
            ->method('read')
            ->willReturn($company);

        $this->requestValidatorService
            ->expects($this->once())
            ->method('throwError400FromData')
            ->willThrowException(new InvalidRequestException('Bad request', 400));

        $response = $this->companyController->update(1, $request);
        $this->assertEquals(400, $response->getStatusCode());
    }

    public function testCompanyUpdateError404(): void
    {
        $request = new Request([], [], [], [], [], [], json_encode(self::COMPANY_DATA, JSON_THROW_ON_ERROR));

        $this->expectException(ResourceNotFoundException::class);
        $this->expectExceptionCode(404);

        $this->companyRepository
            ->expects($this->once())
            ->method('read')
            ->willReturn(false);

        $response = $this->companyController->update(1, $request);
        $this->assertEquals(404, $response->getStatusCode());
    }

    public function testCompanyUpdateError500(): void
    {
        $company = new Company(self::COMPANY_DATA);
        $request = new Request([], [], [], [], [], [], json_encode(self::COMPANY_DATA, JSON_THROW_ON_ERROR));
        $this->expectException(DatabaseException::class);
        $this->expectExceptionCode(500);

        $this->companyRepository
            ->expects($this->once())
            ->method('read')
            ->willReturn($company);

        $this->entityBuilder
            ->expects($this->once())
            ->method('buildEntity')
            ->willReturn($company);

        $this->companyRepository
            ->expects($this->once())
            ->method('update')
            ->willThrowException(new DatabaseException('An error was throw', 500));

        $response = $this->companyController->update(1, $request);
        $this->assertEquals(500, $response->getStatusCode());
    }

    public function testCompanyDelete(): void
    {
        $company = new Company(self::COMPANY_DATA);
        $this->companyRepository
            ->expects($this->once())
            ->method('read')
            ->willReturn($company);

        $this->companyRepository
            ->expects($this->once())
            ->method('delete')
            ->willReturn(true);

        $response = $this->companyController->delete(1);
        $this->assertEquals(200, $response->getStatusCode());
    }

    public function testCompanyDeleteError404(): void
    {
        $this->expectException(ResourceNotFoundException::class);
        $this->companyRepository
            ->expects($this->once())
            ->method('read')
            ->willReturn(false);

        $response = $this->companyController->delete(1);
        $this->assertEquals(404, $response->getStatusCode());
    }

    public function testCompanyDeleteError500(): void
    {
        $company = new Company(self::COMPANY_DATA);

        $this->companyRepository
            ->expects($this->once())
            ->method('read')
            ->willReturn($company);

        $this->expectException(DatabaseException::class);
        $this->companyRepository
            ->expects($this->once())
            ->method('delete')
            ->willThrowException(new DatabaseException('An execption was throw', 500));

        $response = $this->companyController->delete(1);
        $this->assertEquals(500, $response->getStatusCode());
    }

    public function testCompanyList(): void
    {
        $company = new Company(self::COMPANY_DATA);

        $this->companyRepository
            ->expects($this->once())
            ->method('list')
            ->willReturn([$company]);

        $response = $this->companyController->list();
        $this->assertEquals(200, $response->getStatusCode());
    }

    public function testCompanyListError404(): void
    {
        $company = new Company(self::COMPANY_DATA);

        $this->companyRepository
            ->expects($this->once())
            ->method('list')
            ->willReturn([]);

        $this->expectException(ResourceNotFoundException::class);
        $response = $this->companyController->list();
        $this->assertEquals(404, $response->getStatusCode());
    }

    public function testCompanyList500(): void
    {
        $company = new Company(self::COMPANY_DATA);

        $this->expectException(DatabaseException::class);
        $this->companyRepository
            ->expects($this->once())
            ->method('list')
            ->willThrowException(new DatabaseException('An error was throw', 500));

        $response = $this->companyController->list();
        $this->assertEquals(500, $response->getStatusCode());
    }

    public function testTopOffers(): void
    {
        $company = new Company(self::COMPANY_DATA);

        $this->companyRepository
            ->expects($this->once())
            ->method('topOffers')
            ->willReturn([$company]);

        $response = $this->companyController->topOffers();
        $this->assertEquals(200, $response->getStatusCode());
    }

    public function testTopOffersError404(): void
    {
        $company = new Company(self::COMPANY_DATA);

        $this->companyRepository
            ->expects($this->once())
            ->method('topOffers')
            ->willReturn([]);

        $this->expectException(ResourceNotFoundException::class);
        $response = $this->companyController->topOffers();
        $this->assertEquals(404, $response->getStatusCode());
    }

    public function testTopOffersError500(): void
    {
        $company = new Company(self::COMPANY_DATA);

        $this->expectException(DatabaseException::class);
        $this->companyRepository
            ->expects($this->once())
            ->method('topOffers')
            ->willThrowException(new DatabaseException('An error was throw', 500));

        $response = $this->companyController->topOffers();
        $this->assertEquals(500, $response->getStatusCode());
    }
}
