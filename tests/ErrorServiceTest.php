<?php

namespace App\Tests;

use App\Kernel;
use App\Services\ErrorService;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\HttpFoundation\Request;

class ErrorServiceTest extends KernelTestCase
{
    protected static function getKernelClass() : string
    {
        return Kernel::class;
    }

    /**
     * @throws \JsonException
     * @throws \Exception
     */
    public function testGetErrorsJobOfferRequest(): void
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


        foreach ($goodData as $key => $value) {
            foreach ($badData[$key] as $invalidValue) {
                $newData = $goodData;
                $newData[$key] = $invalidValue[0];
                $request = new Request([], [], [], [], [], [], json_encode($newData, JSON_THROW_ON_ERROR));
                $errors = $errorService->getErrorsJobOfferRequest($request);
                $this->assertEquals($invalidValue[1], $errors[0]['message']);
            }
        }
    }

    /**
     * @throws \JsonException
     * @throws \Exception
     */
    public function testGetErrorsContractRequest(): void
    {
        self::bootKernel();
        $container = self::getContainer();

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

        foreach ($goodData as $key => $value) {
            foreach ($badData[$key] as $invalidValue) {
                $newData = $goodData;
                $newData[$key] = $invalidValue[0];
                $request = new Request([], [], [], [], [], [], json_encode($newData, JSON_THROW_ON_ERROR));
                $errors = $container->get(ErrorService::class)->getErrorsContractRequest($request);
                $this->assertEquals($invalidValue[1], $errors[0]['message']);
            }
        }
    }

    public function testGetErrorsUserRequest(): void
    {
        self::bootKernel();
        $container = self::getContainer();

        $errorService = $container->get(ErrorService::class);

        $goodData = [
            'email' => 'fake@email.df',
            'password' => 'Du6oalfy4!',
            'roles' => '3', // choice [1, 3, 5, 9]
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

        foreach ($goodData as $key => $value) {
            foreach ($badData[$key] as $invalidValue) {
                $newData = $goodData;
                $newData[$key] = $invalidValue[0];
                $request = new Request([], [], [], [], [], [], json_encode($newData, JSON_THROW_ON_ERROR));
                $errors = $errorService->getErrorsUserRequest($request);
                $this->assertEquals($invalidValue[1], $errors[0]['message']);
            }
        }
    }

    /**
     * @throws \JsonException
     * @throws \Exception
     */
    public function testGetErrorsCandidateRequest(): void
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
            'user_id' => 1
        ];

        $badData = [
            'firstname' => [
                [null, 'Request must contain firstname, lastname, user_id fields'],
                ['', 'Firstname is required'],
                [ true, 'Firstname must be a string'],
                ['az', 'Firstname must be at least 3 characters long'],
                ['azertyuiopqsdfghjklmwxcvbnazertyuiopqsdfghjklmwxcvbn', 'Firstname must be at least 50 characters long'],
            ],
            'lastname' => [
                [null, 'Request must contain firstname, lastname, user_id fields'],
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
            'user_id' => [
                [null, 'Request must contain firstname, lastname, user_id fields'],
                ['', 'User id is required'],
                [ true, 'User id must be an integer'],
                [-1, 'User id must be a positive integer'],
                ['1a', 'User id must be an integer'],
            ],
        ];

        foreach ($goodData as $key => $value) {
            foreach ($badData[$key] as $invalidValue) {
                $newData = $goodData;
                $newData[$key] = $invalidValue[0];
                $request = new Request([], [], [], [], [], [], json_encode($newData, JSON_THROW_ON_ERROR));
                $errors = $errorService->getErrorsCandidateRequest($request);
                $this->assertEquals($invalidValue[1], $errors[0]['message']);
            }
        }
    }

    /**
     * @throws \JsonException
     * @throws \Exception
     */
    public function testGetErrorsCompanyRequest(): void
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
            'user_id' => 1,
        ];

        $badData = [
            'name' => [
                [null, 'Request must contain name, phone, address, city, country, siret and user_id fields'],
                ['', 'Name is required'],
                [true, 'Name must be at least 3 characters long'],
                ['az', 'Name must be at least 3 characters long'],
                ['azertyuiopqsdfghjklmwxcvbnazertyuiopqsdfghjklmwxcvbnazertyuiopqsdfghjklmwxcvbn',
                    'Name must be at least 50 characters long'],
            ],
            'phone' => [
                [null, 'Request must contain name, phone, address, city, country, siret and user_id fields'],
                ['', 'Phone is required'],
                [true, 'This value should have exactly 10 characters.'],
                ['az', 'This value should have exactly 10 characters.'],
                ['123456789', 'This value should have exactly 10 characters.'],
                ['12345678901', 'This value should have exactly 10 characters.'],
            ],
            'address' => [
                [null, 'Request must contain name, phone, address, city, country, siret and user_id fields'],
                ['', 'Address is required'],
                [true, 'Address must be at least 3 characters long'],
                ['az', 'Address must be at least 3 characters long'],
            ],
            'city' => [
                [null, 'Request must contain name, phone, address, city, country, siret and user_id fields'],
                ['', 'City is required'],
                [true, 'City must be at least 3 characters long'],
                ['az', 'City must be at least 3 characters long'],
            ],
            'country' => [
                [null, 'Request must contain name, phone, address, city, country, siret and user_id fields'],
                ['', 'Country is required'],
                [true, 'Country must be at least 3 characters long'],
                ['az', 'Country must be at least 3 characters long'],
            ],
            'siret' => [
                [null, 'Request must contain name, phone, address, city, country, siret and user_id fields'],
                ['', 'This value should have exactly 14 characters.'],
                [true, 'This value should have exactly 14 characters.'],
                ['1234567891234', 'This value should have exactly 14 characters.'],
                ['123456789012345', 'This value should have exactly 14 characters.'],
            ],
            'user_id' => [
                [null, 'Request must contain name, phone, address, city, country, siret and user_id fields'],
                ['', 'User id is required'],
                [true, 'User id must be a number'],
                [-1, 'User id must be a positive number'],
            ],
        ];

        foreach ($goodData as $key => $value) {
            foreach ($badData[$key] as $invalidValue) {
                $newData = $goodData;
                $newData[$key] = $invalidValue[0];
                $request = new Request([], [], [], [], [], [], json_encode($newData, JSON_THROW_ON_ERROR));
                $errors = $errorService->getErrorsCompanyRequest($request);
//                dump($errors);
                $this->assertEquals($invalidValue[1], $errors[0]['message']);
            }
        }    }
}
