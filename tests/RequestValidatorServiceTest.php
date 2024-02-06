<?php

namespace App\Tests;

use App\Entity\{Apply,Candidate, Company, Contract, JobOffer, Resume, User};
use App\Exceptions\InvalidRequestException;
use App\Kernel;
use App\Services\RequestValidator\RequestValidatorService;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class RequestValidatorServiceTest extends KernelTestCase
{
    private mixed $requestValidatorService;
    private mixed $container;
    /**
     * @return string
     */
    protected static function getKernelClass() : string
    {
        return Kernel::class;
    }

    /**
     * @throws \Exception
     */
    public function setUp(): void
    {
        self::bootKernel();
        $this->container = self::getContainer();
        $this->requestValidatorService = $this->container->get(RequestValidatorService::class);
    }

    /**
     * @throws \JsonException
     * @throws \Exception
     */
    public function testGetErrorsJobOfferRequestThrowsExceptionForInvalidData(): void
    {
        $object = new JobOffer();
        $goodData = [
            'title' => 'test',
            'description' => 'nouvelle description de présentation d une offre d emploi',
            'city' => 'test',
            'salaryMin' => 45000,
            'salaryMax' => 50000,
        ];
        $badData = [
            'title' => [
                [null, 'The request must contain the following fields:title, description, city, salaryMin, salaryMax, '],
                ['', 'Title is required'],
                [ true, 'Title must be a string'],
                ['az', 'Title must be at least 3 characters long'],
                ['azertyuiopqsdfghjklmwxcvbnazertyuiopqsdfghjklmwxcvbn', 'Title must be at least 50 characters long'],
            ],
            'description' => [
                [null, 'The request must contain the following fields:title, description, city, salaryMin, salaryMax, '],
                ['', 'Description is required'],
                [ true, 'Description must be a string'],
                ['az', 'Description must be at least 50 characters long'],
            ],
            'city' => [
                [null, 'The request must contain the following fields:title, description, city, salaryMin, salaryMax, '],
                ['', 'City is required'],
                [ true, 'City must be a string'],
                ['az', 'City must be at least 3 characters long'],
            ],
            'salaryMin' => [
                [null, 'The request must contain the following fields:title, description, city, salaryMin, salaryMax, '],
                ['', 'Salary min is required'],
                [ true, 'Salary min must be a number'],
                [-1, 'Salary min must be a positive number'],
                ['1a', 'Salary min must be a number'],
            ],
            'salaryMax' => [
                [null, 'The request must contain the following fields:title, description, city, salaryMin, salaryMax, '],
                [ true, 'Salary max must be a number'],
                [-1, 'Salary max must be a positive number'],
                ['1a', 'Salary max must be a number'],
            ],
        ];

        $this->testInvalidDataTriggersExceptions($object, $goodData, $badData, $this->requestValidatorService);
    }

    /**
     * @return void
     * @throws \JsonException
     */
    public function testGetErrorsContractRequestThrowsExceptionForInvalidData(): void
    {
        $object = new Contract();
        $goodData = [
            'type' => 'CDI',
        ];
        $badData = [
            'type' => [
                [null, 'The request must contain the following fields:type, '],
                ['', 'Type is required'],
                [ true, 'Type must be a string'],
                ['az', 'Type must be at least 3 characters long'],
                ['azertyuiopqsdfghjklmwxcvbnazertyuiopqsdfghjklmwxcvbn', 'Type must be at least 50 characters long'],
            ],
        ];

        $this->testInvalidDataTriggersExceptions($object,$goodData, $badData, $this->requestValidatorService);
    }

    /**
     * @return void
     * @throws \JsonException
     */
    public function testGetErrorsUserRequestThrowsExceptionForInvalidData(): void
    {
        $object = new User();
        $goodData = [
            'email' => 'fake@email.df',
            'password' => 'Du6oalfy4!',
            'roles' => 3, // choice [1, 3, 5, 9]
        ];
        $badData =[
            'email' => [
                [null, 'The request must contain the following fields:email, password, roles, '],
                ['', 'Email is required'],
                [ true, 'Email must be a valid email'],
                ['az', 'Email must be a valid email'],
                ['azertyuiopqsdfghjklmwxcvbnazertyuiopqsdfghjklmwxcvbn', 'Email must be a valid email'],
                ['a@.fr', 'Email must be a valid email'],
                ['oiaehbaepobzzrgreabebeerzgrzzefheapob@oiehbeopibe.fr', 'Email must be at least 50 characters long']
            ],
            'password' => [
                [null, 'The request must contain the following fields:email, password, roles, '],
                ['', 'Password is required'],
                [ true, 'Le mot de passe doit contenir au moins 8 caractères, une majuscule, une minuscule, un chiffre et un caractère spécial'],
                ['az', 'Le mot de passe doit contenir au moins 8 caractères, une majuscule, une minuscule, un chiffre et un caractère spécial'],
                ['azertyuiopqsdfghjklmwxcvbnazertyuiopqsdfghjklmwxcvbn', 'Le mot de passe doit contenir au moins 8 caractères, une majuscule, une minuscule, un chiffre et un caractère spécial'],
            ],
            'roles' => [
                [null, 'The request must contain the following fields:email, password, roles, '],
                ['', 'Role is required'],
                [ true, 'Role must be one of the following: 1 (ROLE_USER), 3 (ROLE_CANDIDATE), 5 (ROLE_COMPANY), 9 (ROLE_ADMIN)'],
                [0, 'Role must be one of the following: 1 (ROLE_USER), 3 (ROLE_CANDIDATE), 5 (ROLE_COMPANY), 9 (ROLE_ADMIN)'],
                [2, 'Role must be one of the following: 1 (ROLE_USER), 3 (ROLE_CANDIDATE), 5 (ROLE_COMPANY), 9 (ROLE_ADMIN)'],
                [4, 'Role must be one of the following: 1 (ROLE_USER), 3 (ROLE_CANDIDATE), 5 (ROLE_COMPANY), 9 (ROLE_ADMIN)'],
                [6, 'Role must be one of the following: 1 (ROLE_USER), 3 (ROLE_CANDIDATE), 5 (ROLE_COMPANY), 9 (ROLE_ADMIN)'],
                [8, 'Role must be one of the following: 1 (ROLE_USER), 3 (ROLE_CANDIDATE), 5 (ROLE_COMPANY), 9 (ROLE_ADMIN)'],
                [10, 'Role must be one of the following: 1 (ROLE_USER), 3 (ROLE_CANDIDATE), 5 (ROLE_COMPANY), 9 (ROLE_ADMIN)'],
            ],
        ];

        $this->testInvalidDataTriggersExceptions($object, $goodData, $badData, $this->requestValidatorService);
    }

    /**
     * @return void
     * @throws \JsonException
     */
    public function testGetErrorsCandidateRequestThrowsExceptionForInvalidData(): void
    {
        $object = new Candidate();
        $goodData = [
            'firstname' => 'John',
            'lastname' => 'Doe',
            'phone' => '0557625345',
            'address' => '19 rue de la paix',
            'city' => 'Paris',
            'country' => 'France',
            'userId' => 1
        ];
        $badData = [
            'firstname' => [
                [null, 'The request must contain the following fields:firstname, lastname, userId, '],
                ['', 'Firstname is required'],
                [ true, 'Firstname must be a string'],
                ['az', 'Firstname must be at least 3 characters long'],
                ['azertyuiopqsdfghjklmwxcvbnazertyuiopqsdfghjklmwxcvbn', 'Firstname must be at least 50 characters long'],
            ],
            'lastname' => [
                [null, 'The request must contain the following fields:firstname, lastname, userId, '],
                ['', 'Lastname is required'],
                [ true, 'Lastname must be a string'],
                ['az', 'Lastname must be at least 3 characters long'],
                ['azertyuiopqsdfghjklmwxcvbnazertyuiopqsdfghjklmwxcvbn', 'Lastname must be at least 50 characters long'],
            ],
            'phone' => [
                ['', 'This value should have exactly 10 characters.'],
                [ true, 'This value should have exactly 10 characters.'],
                ['az', 'This value should have exactly 10 characters.'],
                ['azertyuiopqsdfghjklmwxcvbnazertyuiopqsdfghjklmwxcvbn', 'This value should have exactly 10 characters.'],
                ['055762534', 'This value should have exactly 10 characters.'],
                ['0557625345a', 'This value should have exactly 10 characters.'],
            ],
            'address' => [
                ['', 'Address must be at least 3 characters long'],
                [ true, 'Address must be at least 3 characters long'],
                ['az', 'Address must be at least 3 characters long'],
            ],
            'city' => [
                ['', 'City must be at least 3 characters long'],
                [ true, 'City must be at least 3 characters long'],
                ['az', 'City must be at least 3 characters long']
            ],
            'country' => [
                [ true, 'Country must be at least 5 characters long'],
                ['az', 'Country must be at least 5 characters long'],
                ['Royaume-Uni de Grande-Bretagne et d\'Irlande du Nord', 'Country must be at least 50 characters long'],
            ],
            'userId' => [
                [null, 'The request must contain the following fields:firstname, lastname, userId, '],
                ['', 'User id is required'],
                [ true, 'User id must be an integer'],
                [-1, 'User id must be a positive integer'],
                ['1a', 'User id must be an integer'],
            ],
        ];

        $this->testInvalidDataTriggersExceptions($object, $goodData, $badData, $this->requestValidatorService);
    }

    /**
     * @return void
     * @throws \JsonException
     */
    public function testGetErrorsCompanyRequestThrowsExceptionForInvalidData(): void
    {
        $object = new Company();
        $goodData = [
            'name' => 'AS-Turing',
            'phone' =>  '0543627392',
            'address' => '29 rue de la paix',
            'city' => 'Paris',
            'country' => 'France',
            'siret' =>  '12345678901234',
            'userId' => 1,
        ];
        $badData = [
            'name' => [
                [null, 'The request must contain the following fields:name, phone, address, city, country, siret, userId, '],
                ['', 'Name is required'],
                [true, 'Name must be at least 3 characters long'],
                ['az', 'Name must be at least 3 characters long'],
                ['azertyuiopqsdfghjklmwxcvbnazertyuiopqsdfghjklmwxcvbnazertyuiopqsdfghjklmwxcvbn',
                    'Name must be at least 50 characters long'],
            ],
            'phone' => [
                [null, 'The request must contain the following fields:name, phone, address, city, country, siret, userId, '],
                ['', 'Phone is required'],
                [true, 'This value should have exactly 10 characters.'],
                ['az', 'This value should have exactly 10 characters.'],
                ['123456789', 'This value should have exactly 10 characters.'],
                ['12345678901', 'This value should have exactly 10 characters.'],
            ],
            'address' => [
                [null, 'The request must contain the following fields:name, phone, address, city, country, siret, userId, '],
                ['', 'Address is required'],
                [true, 'Address must be at least 3 characters long'],
                ['az', 'Address must be at least 3 characters long'],
            ],
            'city' => [
                [null, 'The request must contain the following fields:name, phone, address, city, country, siret, userId, '],
                ['', 'City is required'],
                [true, 'City must be at least 3 characters long'],
                ['az', 'City must be at least 3 characters long'],
            ],
            'country' => [
                [null, 'The request must contain the following fields:name, phone, address, city, country, siret, userId, '],
                ['', 'Country is required'],
                [true, 'Country must be at least 3 characters long'],
                ['az', 'Country must be at least 3 characters long'],
            ],
            'siret' => [
                [null, 'The request must contain the following fields:name, phone, address, city, country, siret, userId, '],
                ['', 'This value should have exactly 14 characters.'],
                [true, 'This value should have exactly 14 characters.'],
                ['1234567891234', 'This value should have exactly 14 characters.'],
                ['123456789012345', 'This value should have exactly 14 characters.'],
            ],
            'userId' => [
                [null, 'The request must contain the following fields:name, phone, address, city, country, siret, userId, '],
                ['', 'User id is required'],
                [true, 'User id must be a number'],
                [-1, 'User id must be a positive number'],
            ],
        ];

        $this->testInvalidDataTriggersExceptions($object, $goodData, $badData, $this->requestValidatorService);
    }

    /**
     * @return void
     * @throws \JsonException
     */
    public function testGetErrorsResumeRequestThrowsExceptionForInvalidData(): void
    {
        $object = new Resume();
        $goodData = [
            'title' => 'test',
            'candidateId' => 1,
            'filename' => 'test.pdf',
        ];
        $badData = [
            'title' => [
                [null, 'The request must contain the following fields:title, filename, candidateId, '],
                ['', 'Title is required'],
                [true, 'Title must be a string'],
                ['az', 'Title must be at least 3 characters long'],
                ['azertyuiopqsdfghjklmwxcvbnazertyuiopqsdfghjklmwxcvbn', 'Title must be at least 50 characters long'],
            ],
            'candidateId' => [
                [null, 'The request must contain the following fields:title, filename, candidateId, '],
                ['', 'Candidate Id is required'],
                [true, 'Candidate Id must be an integer'],
                [-1, 'Candidate Id must be a positive integer'],
                ['1a', 'Candidate Id must be an integer'],
            ],
            'filename' => [
                [null, 'The request must contain the following fields:title, filename, candidateId, '],
                ['', 'Filename is required'],
                [true, 'Filename must be a string'],
                ['az', 'Filename must be at least 3 characters long'],
                ['azertyuiopqsdfghjklmwxcvbnazertyuiopqsdfghjklmwxcvbn', 'Filename must be at least 50 characters long'],
            ],
        ];

        $this->testInvalidDataTriggersExceptions($object, $goodData, $badData, $this->requestValidatorService);
    }

    /**
     * @throws \JsonException
     */
    public function testGetErrorsApplyRequestThrowsExceptionForInvalidData(): void
    {

        $object = new Apply();
        $goodData = [
            'status' => 'pending',
            'candidateId' => 1,
            'resumeId' => 1,
            'jobofferId' => 1,
        ];
        $badData = [
            'status' => [
                [null, 'The request must contain the following fields:status, candidateId, resumeId, jobofferId, '],
                ['', 'Status is required'],
                [true, 'Status must be a string'],
                ['az', 'Status must be one of the following: \'accepted\', \'denied\', \'pending\''],
                ['azertyuiopqsdfghjklmwxcvbnazertyuiopqsdfghjklmwxcvbn',
                    'Status must be one of the following: \'accepted\', \'denied\', \'pending\''],
            ],
            'candidateId' => [
                [null, 'The request must contain the following fields:status, candidateId, resumeId, jobofferId, '],
                ['', 'Candidate id is required'],
                [true, 'Candidate id must be an integer'],
                [-1, 'Candidate id must be a positive integer'],
                ['1a', 'Candidate id must be an integer'],
            ],
            'resumeId' => [
                [null, 'The request must contain the following fields:status, candidateId, resumeId, jobofferId, '],
                ['', 'Resume id is required'],
                [true, 'Resume id must be an integer'],
                [-1, 'Resume id must be a positive integer'],
                ['1a', 'Resume id must be an integer'],
            ],
            'jobofferId' => [
                [null, 'The request must contain the following fields:status, candidateId, resumeId, jobofferId, '],
                ['', 'Job offer id is required'],
                [true, 'Job offer id must be an integer'],
                [-1, 'Job offer id must be a positive integer'],
                ['1a', 'Job offer id must be an integer'],
            ],
        ];

        $this->testInvalidDataTriggersExceptions($object,$goodData, $badData, $this->requestValidatorService);
    }

    /**
     * @param mixed $object
     * Object to be used to test the method
     * @param array $goodData
     * Array of data valid for the service method being tested, to be modified with invalid parameters from the $badData.
     *
     * @param array $badData
     * Array of various sets of invalid data. Each set will replace the corresponding key in $goodData to create invalid test cases
     *
     * @param mixed $errorService
     * Instance of the service containing the method being tested. This should be an instance of ErrorService.
     *
     * @param string $method
     * The name of the method on the ErrorService to test. This method should take a Request object as an argument and throw an InvalidRequestException.
     *
     * @return void
     *
     * @throws \JsonException
     */
    private function testInvalidDataTriggersExceptions(
        mixed $object,
        array $goodData,
        array $badData,
        mixed $RequestValidatorService,
    ): void
    {
        foreach ($goodData as $key => $value) {
            foreach ($badData[$key] as $invalidValue) {
                $newData = $goodData;
                if ($invalidValue[0] !== null) {
                    $newData[$key] = $invalidValue[0];
                } else {
                    unset($newData[$key]);
                }
                try {
                    $RequestValidatorService->throwError400FromData($newData, $object);
                    $this->fail("Expected InvalidRequestException was not thrown for key: $key");
                } catch (InvalidRequestException $exception) {
                    /* if an invalid value is null, we check that the error is the one expected for a missing field */
                    if ($invalidValue[0] === null) {
                        $field = 'request body';
                        $this->assertEquals("[{\"field\":\"$field\",\"message\":\"{$invalidValue[1]}\",\"missingFields\":\"{$key}\"}]",
                            $exception->getMessage());
                    /* else we check that the error is the one expected for the invalid value */
                    } else {
                        $passedValue = $invalidValue[0];
                        $field = $key;
                        $expected = [
                            [
                                "field" => $field,
                                "message" => $invalidValue[1],
                                "passedValue" => $passedValue
                            ]
                        ];
                        $actual = json_decode($exception->getMessage(), true, 512, JSON_THROW_ON_ERROR);
                        $this->assertEquals($expected, $actual);
                    }
                }
            }
        }
    }
}
