<?php

namespace App\Tests;

use App\Exceptions\InvalidRequestException;
use App\Kernel;
use App\Services\ErrorService;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\HttpFoundation\Request;

class ErrorServiceTest extends KernelTestCase
{
    /**
     * @return string
     */
    protected static function getKernelClass() : string
    {
        return Kernel::class;
    }

    /**
     * @throws \JsonException
     * @throws \Exception
     */
    public function testGetErrorsJobOfferRequestThrowsExceptionForInvalidData(): void
    {
        self::bootKernel();
        $container = self::getContainer();
        $errorService = $container->get(ErrorService::class);

        $goodData = [
            'title' => 'test',
            'description' => 'nouelle description de présentation d une offre d emploi',
            'city' => 'test',
            'salaryMin' => 45000,
            'salaryMax' => 50000,
        ];

        $badData = [
            'title' => [
                [null, 'Request must contain title, description, city, salaryMin and salaryMax fields'],
                ['', 'Title is required'],
                [ true, 'Title must be a string'],
                ['az', 'Title must be at least 3 characters long'],
                ['azertyuiopqsdfghjklmwxcvbnazertyuiopqsdfghjklmwxcvbn', 'Title must be at least 50 characters long'],
            ],
            'description' => [
                [null, 'Request must contain title, description, city, salaryMin and salaryMax fields'],
                ['', 'Description is required'],
                [ true, 'Description must be a string'],
                ['az', 'Description must be at least 50 characters long'],
            ],
            'city' => [
                [null, 'Request must contain title, description, city, salaryMin and salaryMax fields'],
                ['', 'City is required'],
                [ true, 'City must be a string'],
                ['az', 'City must be at least 3 characters long'],
            ],
            'salaryMin' => [
                [null, 'Request must contain title, description, city, salaryMin and salaryMax fields'],
                ['', 'Salary min is required'],
                [ true, 'Salary min must be a number'],
                [-1, 'Salary min must be a positive number'],
                ['1a', 'Salary min must be a number'],
            ],
            'salaryMax' => [
                [null, 'Request must contain title, description, city, salaryMin and salaryMax fields'],
                [ true, 'Salary max must be a number'],
                [-1, 'Salary max must be a positive number'],
                ['1a', 'Salary max must be a number'],
            ],
        ];

        $this->testInvalidDataTriggersExceptions($goodData, $badData, $errorService, 'getErrorsJobOfferRequest');
    }

    /**
     * @return void
     * @throws \JsonException
     */
    public function testGetErrorsContractRequestThrowsExceptionForInvalidData(): void
    {
        self::bootKernel();
        $container = self::getContainer();
        $errorService = $container->get(ErrorService::class);

        $goodData = [
            'type' => 'CDI',
        ];

        $badData = [
            'type' => [
                [null, 'Request must contain type field'],
                ['', 'Type is required'],
                [ true, 'Type must be a string'],
                ['az', 'Type must be at least 3 characters long'],
                ['azertyuiopqsdfghjklmwxcvbnazertyuiopqsdfghjklmwxcvbn', 'Type must be at least 50 characters long'],
            ],
        ];

        $this->testInvalidDataTriggersExceptions($goodData, $badData, $errorService, 'getErrorsContractRequest');
    }

    /**
     * @return void
     * @throws \JsonException
     */
    public function testGetErrorsUserRequestThrowsExceptionForInvalidData(): void
    {
        self::bootKernel();
        $container = self::getContainer();
        $errorService = $container->get(ErrorService::class);

        $goodData = [
            'email' => 'fake@email.df',
            'password' => 'Du6oalfy4!',
            'roles' => 3, // choice [1, 3, 5, 9]
        ];

        $badData =[
            'email' => [
                [null, 'Request must contain email, password and roles fields'],
                ['', 'Email is required'],
                [ true, 'Email must be a valid email'],
                ['az', 'Email must be a valid email'],
                ['azertyuiopqsdfghjklmwxcvbnazertyuiopqsdfghjklmwxcvbn', 'Email must be a valid email'],
                ['a@.fr', 'Email must be a valid email'],
                ['oiaehbaepobzzrgreabebeerzgrzzefheapob@oiehbeopibe.fr', 'Email must be at least 50 characters long']
            ],
            'password' => [
                [null, 'Request must contain email, password and roles fields'],
                ['', 'Password is required'],
                [ true, 'Le mot de passe doit contenir au moins 8 caractères, une majuscule, une minuscule, un chiffre et un caractère spécial'],
                ['az', 'Le mot de passe doit contenir au moins 8 caractères, une majuscule, une minuscule, un chiffre et un caractère spécial'],
                ['azertyuiopqsdfghjklmwxcvbnazertyuiopqsdfghjklmwxcvbn', 'Le mot de passe doit contenir au moins 8 caractères, une majuscule, une minuscule, un chiffre et un caractère spécial'],
            ],
            'roles' => [
                [null, 'Request must contain email, password and roles fields'],
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

        $this->testInvalidDataTriggersExceptions($goodData, $badData, $errorService, 'getErrorsUserRequest');
    }

    /**
     * @return void
     * @throws \JsonException
     */
    public function testGetErrorsCandidateRequestThrowsExceptionForInvalidData(): void
    {
        self::bootKernel();
        $container = self::getContainer();

        $errorService = $container->get(ErrorService::class);

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
                [null, 'Request must contain firstname, lastname, userId fields'],
                ['', 'Firstname is required'],
                [ true, 'Firstname must be a string'],
                ['az', 'Firstname must be at least 3 characters long'],
                ['azertyuiopqsdfghjklmwxcvbnazertyuiopqsdfghjklmwxcvbn', 'Firstname must be at least 50 characters long'],
            ],
            'lastname' => [
                [null, 'Request must contain firstname, lastname, userId fields'],
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
                [null, 'Request must contain firstname, lastname, userId fields'],
                ['', 'User id is required'],
                [ true, 'User id must be an integer'],
                [-1, 'User id must be a positive integer'],
                ['1a', 'User id must be an integer'],
            ],
        ];

        $this->testInvalidDataTriggersExceptions($goodData, $badData, $errorService, 'getErrorsCandidateRequest');
    }

    /**
     * @return void
     * @throws \JsonException
     */
    public function testGetErrorsCompanyRequestThrowsExceptionForInvalidData(): void
    {
        self::bootKernel();
        $container = self::getContainer();

        $errorService = $container->get(ErrorService::class);

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
                [null, 'Request must contain name, phone, address, city, country, siret and userId fields'],
                ['', 'Name is required'],
                [true, 'Name must be at least 3 characters long'],
                ['az', 'Name must be at least 3 characters long'],
                ['azertyuiopqsdfghjklmwxcvbnazertyuiopqsdfghjklmwxcvbnazertyuiopqsdfghjklmwxcvbn',
                    'Name must be at least 50 characters long'],
            ],
            'phone' => [
                [null, 'Request must contain name, phone, address, city, country, siret and userId fields'],
                ['', 'Phone is required'],
                [true, 'This value should have exactly 10 characters.'],
                ['az', 'This value should have exactly 10 characters.'],
                ['123456789', 'This value should have exactly 10 characters.'],
                ['12345678901', 'This value should have exactly 10 characters.'],
            ],
            'address' => [
                [null, 'Request must contain name, phone, address, city, country, siret and userId fields'],
                ['', 'Address is required'],
                [true, 'Address must be at least 3 characters long'],
                ['az', 'Address must be at least 3 characters long'],
            ],
            'city' => [
                [null, 'Request must contain name, phone, address, city, country, siret and userId fields'],
                ['', 'City is required'],
                [true, 'City must be at least 3 characters long'],
                ['az', 'City must be at least 3 characters long'],
            ],
            'country' => [
                [null, 'Request must contain name, phone, address, city, country, siret and userId fields'],
                ['', 'Country is required'],
                [true, 'Country must be at least 3 characters long'],
                ['az', 'Country must be at least 3 characters long'],
            ],
            'siret' => [
                [null, 'Request must contain name, phone, address, city, country, siret and userId fields'],
                ['', 'This value should have exactly 14 characters.'],
                [true, 'This value should have exactly 14 characters.'],
                ['1234567891234', 'This value should have exactly 14 characters.'],
                ['123456789012345', 'This value should have exactly 14 characters.'],
            ],
            'userId' => [
                [null, 'Request must contain name, phone, address, city, country, siret and userId fields'],
                ['', 'User id is required'],
                [true, 'User id must be a number'],
                [-1, 'User id must be a positive number'],
            ],
        ];

        $this->testInvalidDataTriggersExceptions($goodData, $badData, $errorService, 'getErrorsCompanyRequest');
    }

    /**
     * @return void
     * @throws \JsonException
     */
    public function testGetErrorsResumeRequestThrowsExceptionForInvalidData(): void
    {
        self::bootKernel();
        $container = self::getContainer();

        $errorService = $container->get(ErrorService::class);

        $goodData = [
            'title' => 'test',
            'candidateId' => 1,
        ];

        $badData = [
            'title' => [
                [null, 'Request must contain title and candidateId fields'],
                ['', 'Title is required'],
                [true, 'Title must be a string'],
                ['az', 'Title must be at least 3 characters long'],
                ['azertyuiopqsdfghjklmwxcvbnazertyuiopqsdfghjklmwxcvbn', 'Title must be at least 50 characters long'],
            ],
            'candidateId' => [
                [null, 'Request must contain title and candidateId fields'],
                ['', 'Candidate Id is required'],
                [true, 'Candidate Id must be an integer'],
                [-1, 'Candidate Id must be a positive integer'],
                ['1a', 'Candidate Id must be an integer'],
            ],
        ];

        $this->testInvalidDataTriggersExceptions($goodData, $badData, $errorService, 'getErrorsResumeRequest');
    }

    /**
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
        array $goodData,
        array $badData,
        mixed $errorService,
        string $method
    ): void
    {
        foreach ($goodData as $key => $value) {
            foreach ($badData[$key] as $invalidValue) {
                $newData = $goodData;
                $newData[$key] = $invalidValue[0];
                $request = new Request([], [], [], [], [], [], json_encode($newData, JSON_THROW_ON_ERROR));
                try {
                    $errorService->$method($request);
                    $this->fail("Expected InvalidRequestException was not thrown for key: $key");
                } catch (InvalidRequestException $exception) {
                    /*S’il manque un champ obligatoire dans la requête on vérifie que le message d’erreur est celui attendu*/
                    if ($invalidValue[0] === null) {
                        $field = 'request';
                        $this->assertEquals("[{\"field\":\"$field\",\"message\":\"{$invalidValue[1]}\"}]", $exception->getMessage());
                        /*Sinon, on vérifie que les erreurs sont bien celles attendues par rapport au champ invalide */
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
