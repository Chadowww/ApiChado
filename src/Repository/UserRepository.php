<?php

namespace App\Repository;

use App\Entity\User;
use App\Services\ConnectionDbService;
use PDO;
use PDOException;

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
                     `role`)
                VALUES 
                    (:email,
                     :password,
                     :role)';
            $statement = $this->connection->prepare($query);
            $statement->bindValue(':email', $user->getEmail());
            $statement->bindValue(':password', $user->getPassword());
            $statement->bindValue(':role', $user->getRolesInt());
            $statement->execute();
            $this->connection->commit();
            return true;
        } catch (PDOException $e) {
            $this->connection->rollBack();
            throw $e;
        }
    }

    public function read(int $id): User | bool
    {
        $this->connection->beginTransaction();
        $query = 'SELECT * FROM APICHADO.user WHERE id = :id';
        $statement = $this->connection->prepare($query);
        $statement->bindValue(':id', $id);
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
                    `role` = :role
                WHERE 
                    `id` = :id';
            $statement = $this->connection->prepare($query);
            $statement->bindValue(':id', $user->getId());
            $statement->bindValue(':email', $user->getEmail());
            $statement->bindValue(':password', $user->getPassword());
            $statement->bindValue(':role', $user->getRolesInt());
            $statement->execute();
            $this->connection->commit();
            return true;
        } catch (PDOException $e) {
            $this->connection->rollBack();
            throw $e;
        }
    }

    public function delete(int $id): bool
    {
        $this->connection->beginTransaction();

        try {
            $query = 'DELETE FROM APICHADO.user WHERE id = :id';
            $statement = $this->connection->prepare($query);
            $statement->bindValue(':id', $id);
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
}