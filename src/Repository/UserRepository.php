<?php

namespace App\Repository;

use App\Entity\User;
use App\Services\ConnectionDbService;
use PDO;
use PDOException;
use Symfony\Component\HttpFoundation\Request;

class UserRepository
{
    private PDO $connection;

    public function __construct(ConnectionDbService $connection)
    {
        $this->connection = $connection->connection();
    }

    public function create(User $user): bool
    {
        $this->connection->beginTransaction();

        try {
            $query = '
                INSERT INTO APICHADO.user 
                    (`email`,
                     `password`,
                     `roles`)
                VALUES 
                    (:email,
                     :password,
                     :role)';
            $statement = $this->connection->prepare($query);
            $statement->bindValue(':email', $user->getEmail());
            $statement->bindValue(':password', $user->getPassword());
            $statement->bindValue(':role', $user->getRolesValue());
            $statement->execute();
            $this->connection->commit();
            return true;
        } catch (PDOException $e) {
            $this->connection->rollBack();
            throw $e;
        }
    }

    public function read(int $userId): User | bool
    {
        $this->connection->beginTransaction();
        $query = 'SELECT * FROM APICHADO.user WHERE userId = :userId';
        $statement = $this->connection->prepare($query);
        $statement->bindValue(':userId', $userId);
        $statement->execute();
        $user = $statement->fetchObject(User::class);
        $this->connection->commit();

        return $user;
    }

    public function update(User $user): bool
    {
        $this->connection->beginTransaction();

        try {
            $query = '
                UPDATE APICHADO.user 
                SET 
                    `email` = :email,
                    `password` = :password,
                    `roles` = :role
                WHERE 
                    userId = :userId';
            $statement = $this->connection->prepare($query);
            $statement->bindValue(':userId', $user->getUserId());
            $statement->bindValue(':email', $user->getEmail());
            $statement->bindValue(':password', $user->getPassword());
            $statement->bindValue(':role', $user->getRolesValue());
            $statement->execute();
            $this->connection->commit();
            return true;
        } catch (PDOException $e) {
            $this->connection->rollBack();
            throw $e;
        }
    }

    public function delete(int $userId): bool
    {
        $this->connection->beginTransaction();

        try {
            $query = 'DELETE FROM APICHADO.user WHERE userId = :userId';
            $statement = $this->connection->prepare($query);
            $statement->bindValue(':userId', $userId);
            $statement->execute();
            $this->connection->commit();
            return true;
        } catch (PDOException $e) {
            $this->connection->rollBack();
            throw $e;
        }
    }

    public function list(): array
    {
        try {
            $this->connection->beginTransaction();
            $query = 'SELECT * FROM APICHADO.user';
            $statement = $this->connection->prepare($query);
            $statement->execute();
            $users = $statement->fetchAll(PDO::FETCH_CLASS, User::class);
            $this->connection->commit();
            return $users;
        } catch (PDOException $e) {
            $this->connection->rollBack();
            throw $e;
        }
    }

    public function findOneBy(array $array)
    {
        try {
            $this->connection->beginTransaction();
            $query = 'SELECT * FROM APICHADO.user WHERE ';
            foreach ($array as $key => $value) {
                $query .= $key . ' = :' . $key;
            }
            $statement = $this->connection->prepare($query);
            foreach ($array as $key => $value) {
                $statement->bindValue(':' . $key, $value);
            }
            $statement->execute();
            $user = $statement->fetchObject(User::class);
            $this->connection->commit();
            return $user;
        } catch (PDOException $e) {
            $this->connection->rollBack();
            throw $e;
        }
    }

    public function getLastId(): int
    {
        try {
            $this->connection->beginTransaction();
            $query = 'SELECT MAX(userId) FROM APICHADO.user';
            $statement = $this->connection->prepare($query);
            $statement->execute();
            $userId = $statement->fetchColumn();
            $this->connection->commit();
            return $userId;
        } catch (PDOException $e) {
            $this->connection->rollBack();
            throw $e;
        }
    }

    public function findByEmail(Request $request): bool
    {
        $data = json_decode($request->getContent(), true, 512, JSON_THROW_ON_ERROR);
        try {
            $this->connection->beginTransaction();
            $query = 'SELECT * FROM APICHADO.user WHERE email = :email';
            $statement = $this->connection->prepare($query);
            $statement->bindValue(':email', $data['email']);
            $statement->execute();
            $user = $statement->fetchObject(User::class);
            $this->connection->commit();
            if ($user) {
                return true;
            }
            return false;
        } catch (PDOException $e) {
            $this->connection->rollBack();
            throw $e;
        }
    }

    public function getUserWithCandidate(int $userId): array | bool
    {
        $this->connection->beginTransaction();
        $query = '
            SELECT u.*, sM.*, c.*
            FROM APICHADO.user u
            LEFT JOIN APICHADO.socialeMedia sM on u.userId = sM.userId
            LEFT JOIN APICHADO.candidate c ON u.userId = c.userId WHERE u.userId = :userId
            ';
        $statement = $this->connection->prepare($query);
        $statement->bindValue(':userId', $userId);
        $statement->execute();
        $user = $statement->fetch(PDO::FETCH_ASSOC);
        $this->connection->commit();

        return $user;
    }
}